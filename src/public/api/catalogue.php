<?php

/**
 * ============================================================
 * Vite & Gourmand — API Catalogue (endpoint AJAX)
 * ============================================================
 * Chemin : src/public/api/catalogue.php
 *
 * Retourne les menus filtrés en JSON
 * Appelé par catalogue.js via fetch()
 * ============================================================
 */

// Forcer JSON + sécurité
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// N'accepter que les requêtes AJAX
if (
    empty($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès direct non autorisé']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$pdo = getDbConnection();

// ── Récupération et nettoyage des filtres ────────────────
$search     = trim(strip_tags($_GET['search']     ?? ''));
$theme      = (int) ($_GET['theme']               ?? 0);
$regime     = (int) ($_GET['regime']              ?? 0);
$prixMax    = (float) ($_GET['prix_max']          ?? 0);
$nbPersonnes = (int) ($_GET['nb_personnes']       ?? 0);
$ordre      = trim(strip_tags($_GET['ordre']      ?? 'default'));
$allergenes = $_GET['allergenes'] ?? [];

// Nettoyer les allergènes
if (!is_array($allergenes)) $allergenes = [];
$allergenes = array_map('intval', $allergenes);
$allergenes = array_filter($allergenes, fn($a) => $a > 0);

// ── Construction de la requête ───────────────────────────
$sql = "
    SELECT
        m.id_menu,
        m.titre,
        m.slug,
        m.description,
        m.prix_par_personne,
        m.nb_personnes_min,
        m.nb_personnes_max,
        m.image_url,
        t.nom         AS theme_nom,
        t.couleur     AS theme_couleur,
        GROUP_CONCAT(DISTINCT r.nom ORDER BY r.nom SEPARATOR ', ')     AS regimes,
        GROUP_CONCAT(DISTINCT CONCAT(be.icone, ' ', be.nom) ORDER BY be.nom SEPARATOR ' | ') AS badges_eco
    FROM menu m
    LEFT JOIN theme t           ON m.id_theme      = t.id_theme
    LEFT JOIN menu_regime mr    ON m.id_menu        = mr.id_menu
    LEFT JOIN regime r          ON mr.id_regime     = r.id_regime
    LEFT JOIN menu_badge_eco mb ON m.id_menu        = mb.id_menu
    LEFT JOIN badge_eco be      ON mb.id_badge      = be.id_badge
    WHERE m.is_active = 1
";

$params = [];

// Filtre recherche texte
if (!empty($search)) {
    $sql .= " AND (m.titre LIKE :search OR m.description LIKE :search2)";
    $params[':search']  = '%' . $search . '%';
    $params[':search2'] = '%' . $search . '%';
}

// Filtre thème
if ($theme > 0) {
    $sql .= " AND m.id_theme = :theme";
    $params[':theme'] = $theme;
}

// Filtre régime
if ($regime > 0) {
    $sql .= " AND m.id_menu IN (
        SELECT id_menu FROM menu_regime WHERE id_regime = :regime
    )";
    $params[':regime'] = $regime;
}

// Filtre prix max
if ($prixMax > 0) {
    $sql .= " AND m.prix_par_personne <= :prix_max";
    $params[':prix_max'] = $prixMax;
}

// Filtre nombre de personnes minimum
if ($nbPersonnes > 0) {
    $sql .= " AND m.nb_personnes_min <= :nb_personnes";
    $params[':nb_personnes'] = $nbPersonnes;
}

// Filtre allergènes à exclure
if (!empty($allergenes)) {
    $placeholders = implode(',', array_fill(0, count($allergenes), '?'));
    $sql .= " AND m.id_menu NOT IN (
        SELECT DISTINCT cm.id_menu
        FROM composition_menu cm
        JOIN plat_allergene pa ON cm.id_plat = pa.id_plat
        WHERE pa.id_allergene IN ($placeholders)
    )";
}

// GROUP BY complet
$sql .= "
    GROUP BY
        m.id_menu, m.titre, m.slug, m.description,
        m.prix_par_personne, m.nb_personnes_min, m.nb_personnes_max,
        m.image_url, t.nom, t.couleur
";

// Tri
switch ($ordre) {
    case 'prix_asc':  $sql .= " ORDER BY m.prix_par_personne ASC";  break;
    case 'prix_desc': $sql .= " ORDER BY m.prix_par_personne DESC"; break;
    case 'titre':     $sql .= " ORDER BY m.titre ASC";              break;
    default:          $sql .= " ORDER BY m.id_menu ASC";            break;
}

// ── Exécution ────────────────────────────────────────────
try {
    $stmt = $pdo->prepare($sql);

    // Binder les paramètres nommés
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }

    // Binder les allergènes (paramètres positionnels)
    if (!empty($allergenes)) {
        $offset = count($params) + 1;
        foreach (array_values($allergenes) as $i => $aid) {
            $stmt->bindValue($offset + $i, $aid, PDO::PARAM_INT);
        }
    }

    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'total'   => count($menus),
        'menus'   => $menus,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    error_log('API Catalogue: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur', 'menus' => [], 'total' => 0]);
}
