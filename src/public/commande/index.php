<?php

/**
 * ============================================================
 * Vite & Gourmand — Point d'entrée Commande
 * ============================================================
 * Chemin : src/public/commande/index.php
 *
 * GET  → Affiche le formulaire de commande pré-rempli
 * POST → Valide, enregistre en BDD, redirige vers mes-commandes
 *
 * Accès : utilisateur connecté uniquement
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'commande';

$pageTitle = $isEn ? 'Order — Vite & Gourmand' : 'Commander — Vite & Gourmand';
$pageDesc  = '';

// ── Connexion obligatoire ────────────────────────────────
AuthController::requireAuth('/connexion/');

// ── Panier obligatoire ───────────────────────────────────
if (empty($_SESSION['panier'])) {
    header('Location: /panier/');
    exit;
}

$pdo       = getDbConnection();
$userModel = new UserModel($pdo);
$user      = $userModel->findById((int) $_SESSION['user_id']);

if (!$user) {
    AuthController::logout();
    header('Location: /connexion/');
    exit;
}

// ── Récupérer premier item du panier ─────────────────────
$panier     = $_SESSION['panier'];
$firstItem  = reset($panier);

$errors = [];

// ════════════════════════════════════════════════════════════
// TRAITEMENT POST
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Extraction ───────────────────────────────────────
    $adresse      = trim(strip_tags($_POST['adresse_livraison'] ?? ''));
    $codePostal   = trim(strip_tags($_POST['code_postal']       ?? ''));
    $ville        = trim(strip_tags($_POST['ville']             ?? ''));
    $dateEvenement = trim(strip_tags($_POST['date_evenement']   ?? ''));
    $heureEvenement = trim(strip_tags($_POST['heure_evenement'] ?? '12:00'));
    $notes        = trim(strip_tags($_POST['notes']             ?? ''));

    // ── Validations ──────────────────────────────────────
    if (empty($adresse))     $errors[] = 'L\'adresse est obligatoire.';
    if (empty($codePostal))  $errors[] = 'Le code postal est obligatoire.';
    if (empty($ville))       $errors[] = 'La ville est obligatoire.';

    if (empty($dateEvenement)) {
        $errors[] = 'La date est obligatoire.';
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d', $dateEvenement);
        $minDate = new DateTime('+2 days');
        if (!$dateObj || $dateObj < $minDate) {
            $errors[] = 'La date doit être au minimum 48h à l\'avance.';
        }
    }

    // ── Calcul distance + frais ──────────────────────────
    $distanceKm     = 0.0;
    $fraisLivraison = 0.0;

    if (strtolower(trim($ville)) !== 'bordeaux') {
        $distanceKm     = getOrCacheDistance($pdo, $ville);
        $fraisLivraison = round(5.00 + ($distanceKm * 0.59), 2);
    }

    // ── Calcul totaux panier ─────────────────────────────
    $sousTotal    = 0.0;
    $totalRemises = 0.0;
    $nbPersonnes  = 0;

    foreach ($panier as $item) {
        $sousTotal    += $item['sous_total_brut'];
        $totalRemises += $item['remise'];
        $nbPersonnes  += $item['nb_personnes'];
    }

    $sousTotal   = round($sousTotal, 2);
    $montantRemise = round($totalRemises, 2);
    $total       = round($sousTotal - $montantRemise + $fraisLivraison, 2);

    // ── Insertion en BDD si pas d'erreurs ────────────────
    if (empty($errors)) {

        try {
            $pdo->beginTransaction();

            $dateHeure = $dateEvenement . ' ' . $heureEvenement . ':00';

            // INSERT commande
            $stmt = $pdo->prepare("
                INSERT INTO commande (
                    id_utilisateur, date_evenement,
                    adresse_livraison, code_postal_livraison, ville_livraison,
                    distance_km, nb_personnes,
                    sous_total, montant_remise, frais_livraison, total,
                    statut, created_at
                ) VALUES (
                    :id_utilisateur, :date_evenement,
                    :adresse, :code_postal, :ville,
                    :distance_km, :nb_personnes,
                    :sous_total, :montant_remise, :frais_livraison, :total,
                    'pending', NOW()
                )
            ");

            $stmt->execute([
                ':id_utilisateur'  => (int) $user['id_utilisateur'],
                ':date_evenement'  => $dateHeure,
                ':adresse'         => $adresse,
                ':code_postal'     => $codePostal,
                ':ville'           => $ville,
                ':distance_km'     => $distanceKm,
                ':nb_personnes'    => $nbPersonnes,
                ':sous_total'      => $sousTotal,
                ':montant_remise'  => $montantRemise,
                ':frais_livraison' => $fraisLivraison,
                ':total'           => $total,
            ]);

            $idCommande = (int) $pdo->lastInsertId();

            // INSERT lignes de commande
            $stmtLigne = $pdo->prepare("
                INSERT INTO ligne_commande (
                    id_commande, id_menu, quantite,
                    prix_unitaire, sous_total, notes
                ) VALUES (
                    :id_commande, :id_menu, :quantite,
                    :prix_unitaire, :sous_total, :notes
                )
            ");

            foreach ($panier as $item) {
                $stmtLigne->execute([
                    ':id_commande'   => $idCommande,
                    ':id_menu'       => (int) $item['id_menu'],
                    ':quantite'      => (int) $item['nb_personnes'],
                    ':prix_unitaire' => (float) $item['prix_unitaire'],
                    ':sous_total'    => (float) $item['sous_total'],
                    ':notes'         => $notes ?: null,
                ]);
            }

            // INSERT historique statut initial
            $stmtHist = $pdo->prepare("
                INSERT INTO historique_statut (
                    id_commande, statut_precedent, nouveau_statut,
                    id_utilisateur, motif, created_at
                ) VALUES (
                    :id_commande, 'pending', 'pending',
                    :id_utilisateur, 'Commande créée par le client', NOW()
                )
            ");

            $stmtHist->execute([
                ':id_commande'    => $idCommande,
                ':id_utilisateur' => (int) $user['id_utilisateur'],
            ]);

            $pdo->commit();

            // Vider le panier
            unset($_SESSION['panier']);

            // Flash message
            $_SESSION['flash_success'] = 'Votre commande #' . $idCommande . ' a été enregistrée ! Nous vous contactons sous 24h.';

            header('Location: /mes-commandes/');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log('Commande::insert — ' . $e->getMessage());
            $errors[] = 'Erreur technique. Veuillez réessayer.';
        }
    }
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/commande/index.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';


// ════════════════════════════════════════════════════════════
// FONCTION — Distance avec cache BDD
// ════════════════════════════════════════════════════════════
/**
 * Retourne la distance en km depuis le cache BDD.
 * Si pas en cache ou expiré → distance estimée par défaut.
 * En production : remplacer par appel API Google Distance Matrix.
 *
 * @param PDO    $pdo
 * @param string $ville
 * @return float Distance en km
 */
function getOrCacheDistance(PDO $pdo, string $ville): float
{
    $villeClean = mb_strtolower(trim($ville));

    // Chercher dans le cache (non expiré)
    $stmt = $pdo->prepare("
        SELECT distance_km FROM cache_distance
        WHERE LOWER(ville) = :ville
          AND expires_at > NOW()
        LIMIT 1
    ");
    $stmt->execute([':ville' => $villeClean]);
    $row = $stmt->fetch();

    if ($row) {
        return (float) $row['distance_km'];
    }

    // Distances estimées pour les principales villes girondines
    $distancesDefaut = [
        'mérignac'       => 8.0,
        'merignac'       => 8.0,
        'pessac'         => 6.0,
        'talence'        => 4.0,
        'bègles'         => 5.0,
        'begles'         => 5.0,
        'villenave-d\'ornon' => 8.0,
        'le bouscat'     => 5.0,
        'mérignac'       => 8.0,
        'libourne'       => 30.0,
        'arcachon'       => 65.0,
        'cap ferret'     => 75.0,
        'blaye'          => 50.0,
        'pauillac'       => 55.0,
        'saint-émilion'  => 35.0,
        'le taillan-médoc' => 14.0,
        'paris'          => 580.0,
    ];

    $distance = $distancesDefaut[$villeClean] ?? 25.0; // 25km par défaut

    // Sauvegarder en cache (valide 30 jours)
    try {
        $stmtInsert = $pdo->prepare("
            INSERT INTO cache_distance (ville, distance_km, cached_at, expires_at)
            VALUES (:ville, :distance, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))
            ON DUPLICATE KEY UPDATE
                distance_km = :distance2,
                cached_at   = NOW(),
                expires_at  = DATE_ADD(NOW(), INTERVAL 30 DAY)
        ");
        $stmtInsert->execute([
            ':ville'     => $villeClean,
            ':distance'  => $distance,
            ':distance2' => $distance,
        ]);
    } catch (PDOException $e) {
        error_log('cache_distance insert: ' . $e->getMessage());
    }

    return $distance;
}
