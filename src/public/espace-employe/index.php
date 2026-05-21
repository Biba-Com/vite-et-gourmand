<?php

/**
 * ============================================================
 * Vite & Gourmand — Espace Employé
 * ============================================================
 * Chemin : src/public/espace-employe/index.php
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'espace-employe';
$pageTitle   = 'Espace Employé — Vite & Gourmand';
$pageDesc    = '';

// ── Accès réservé employé + admin ───────────────────────
AuthController::requireAuth('/connexion/');
AuthController::requireRole('employee');

$pdo = getDbConnection();

// ── Messages flash ───────────────────────────────────────
$flashSuccess = null;
$flashError   = null;
if (!empty($_SESSION['flash_success'])) { $flashSuccess = $_SESSION['flash_success']; unset($_SESSION['flash_success']); }
if (!empty($_SESSION['flash_error']))   { $flashError   = $_SESSION['flash_error'];   unset($_SESSION['flash_error']); }

// ── Onglet actif ─────────────────────────────────────────
$tab  = $_GET['tab'] ?? 'commandes';
$tabs = ['commandes', 'avis'];
if (!in_array($tab, $tabs)) $tab = 'commandes';

// ── Filtres commandes ────────────────────────────────────
$filtreStatut = trim(strip_tags($_GET['statut'] ?? ''));
$filtreClient = trim(strip_tags($_GET['client'] ?? ''));

$statutsValides = ['pending','confirmed','in_preparation','in_delivery','completed','cancelled'];

// ── Requête commandes ────────────────────────────────────
$sql = "
    SELECT
        c.id_commande,
        c.date_evenement,
        c.nb_personnes,
        c.ville_livraison,
        c.total,
        c.statut,
        c.created_at,
        u.prenom,
        u.nom,
        u.email,
        u.telephone,
        GROUP_CONCAT(m.titre ORDER BY lc.id_ligne SEPARATOR ', ') AS menus_titres
    FROM commande c
    JOIN utilisateur u          ON c.id_utilisateur = u.id_utilisateur
    LEFT JOIN ligne_commande lc ON c.id_commande    = lc.id_commande
    LEFT JOIN menu m            ON lc.id_menu       = m.id_menu
    WHERE 1=1
";
$params = [];

if (!empty($filtreStatut) && in_array($filtreStatut, $statutsValides)) {
    $sql .= " AND c.statut = :statut";
    $params[':statut'] = $filtreStatut;
}

if (!empty($filtreClient)) {
    $sql .= " AND (u.prenom LIKE :client OR u.nom LIKE :client2 OR u.email LIKE :client3)";
    $params[':client']  = '%' . $filtreClient . '%';
    $params[':client2'] = '%' . $filtreClient . '%';
    $params[':client3'] = '%' . $filtreClient . '%';
}

// ── GROUP BY complet (conformité only_full_group_by) ─────
$sql .= "
    GROUP BY
        c.id_commande,
        c.date_evenement,
        c.nb_personnes,
        c.ville_livraison,
        c.total,
        c.statut,
        c.created_at,
        u.prenom,
        u.nom,
        u.email,
        u.telephone
    ORDER BY c.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll();

// ── Avis en attente de modération ────────────────────────
$stmtAvis = $pdo->prepare("
    SELECT
        a.id_avis,
        a.note,
        a.commentaire,
        a.created_at,
        a.statut,
        u.prenom,
        u.nom,
        c.date_evenement,
        GROUP_CONCAT(m.titre ORDER BY lc.id_ligne SEPARATOR ', ') AS menu_titre
    FROM avis a
    JOIN utilisateur u          ON a.id_utilisateur = u.id_utilisateur
    JOIN commande c             ON a.id_commande    = c.id_commande
    LEFT JOIN ligne_commande lc ON c.id_commande    = lc.id_commande
    LEFT JOIN menu m            ON lc.id_menu       = m.id_menu
    WHERE a.statut = 'pending'
    GROUP BY
        a.id_avis,
        a.note,
        a.commentaire,
        a.created_at,
        a.statut,
        u.prenom,
        u.nom,
        c.date_evenement
    ORDER BY a.created_at DESC
");
$stmtAvis->execute();
$avisEnAttente = $stmtAvis->fetchAll();

// ── Labels statuts ───────────────────────────────────────
$statutLabels = [
    'pending'        => ['label' => 'En attente',     'color' => '#F59E0B'],
    'confirmed'      => ['label' => 'Confirmée',       'color' => '#3B82F6'],
    'in_preparation' => ['label' => 'En préparation', 'color' => '#8B5CF6'],
    'in_delivery'    => ['label' => 'En livraison',   'color' => '#06B6D4'],
    'completed'      => ['label' => 'Terminée',        'color' => '#10B981'],
    'cancelled'      => ['label' => 'Annulée',         'color' => '#EF4444'],
];

$prochainStatut = [
    'pending'        => 'confirmed',
    'confirmed'      => 'in_preparation',
    'in_preparation' => 'in_delivery',
    'in_delivery'    => 'completed',
];

// ════════════════════════════════════════════════════════
// POST — Actions
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Changer statut commande ───────────────────────
    if ($action === 'update_statut') {
        $idCommande    = (int) ($_POST['id_commande']    ?? 0);
        $nouveauStatut = trim($_POST['nouveau_statut']   ?? '');
        $motif         = trim(strip_tags($_POST['motif'] ?? ''));

        if ($idCommande > 0 && in_array($nouveauStatut, $statutsValides)) {
            try {
                $pdo->beginTransaction();

                // Récupérer statut actuel
                $stmtCurrent = $pdo->prepare("
                    SELECT statut FROM commande WHERE id_commande = :id
                ");
                $stmtCurrent->execute([':id' => $idCommande]);
                $current = $stmtCurrent->fetch();

                // Mettre à jour commande
                $pdo->prepare("
                    UPDATE commande SET statut = :statut WHERE id_commande = :id
                ")->execute([':statut' => $nouveauStatut, ':id' => $idCommande]);

                // Historique
                $pdo->prepare("
                    INSERT INTO historique_statut
                        (id_commande, statut_precedent, nouveau_statut,
                         id_utilisateur, motif, created_at)
                    VALUES (:id, :prev, :new, :uid, :motif, NOW())
                ")->execute([
                    ':id'    => $idCommande,
                    ':prev'  => $current['statut'] ?? 'pending',
                    ':new'   => $nouveauStatut,
                    ':uid'   => (int) $_SESSION['user_id'],
                    ':motif' => $motif ?: 'Statut mis à jour par l\'équipe',
                ]);

                $pdo->commit();
                $_SESSION['flash_success'] = 'Commande #' . str_pad($idCommande, 4, '0', STR_PAD_LEFT) . ' mise à jour → ' . ($statutLabels[$nouveauStatut]['label'] ?? $nouveauStatut);

            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log($e->getMessage());
                $_SESSION['flash_error'] = 'Erreur technique. Veuillez réessayer.';
            }
        }
        header('Location: /espace-employe/?tab=commandes');
        exit;
    }

    // ── Valider / Refuser un avis ─────────────────────
    if (in_array($action, ['approve_avis', 'reject_avis'])) {
        $idAvis    = (int) ($_POST['id_avis'] ?? 0);
        $newStatut = $action === 'approve_avis' ? 'approved' : 'rejected';

        if ($idAvis > 0) {
            try {
                $pdo->prepare("
                    UPDATE avis SET
                        statut        = :statut,
                        id_moderateur = :uid,
                        moderated_at  = NOW()
                    WHERE id_avis = :id
                ")->execute([
                    ':statut' => $newStatut,
                    ':uid'    => (int) $_SESSION['user_id'],
                    ':id'     => $idAvis,
                ]);
                $_SESSION['flash_success'] = 'Avis ' . ($newStatut === 'approved' ? 'validé ✅' : 'refusé ❌') . ' avec succès.';
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $_SESSION['flash_error'] = 'Erreur technique.';
            }
        }
        header('Location: /espace-employe/?tab=avis');
        exit;
    }
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/espace-employe/index.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
