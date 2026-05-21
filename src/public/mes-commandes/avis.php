<?php

/**
 * ============================================================
 * Vite & Gourmand — Laisser un avis
 * ============================================================
 * Chemin : src/public/mes-commandes/avis.php
 *
 * GET  → Affiche le formulaire
 * POST → Enregistre l'avis
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'mes-commandes';

AuthController::requireAuth('/connexion/');

$idCommande = (int) ($_GET['id'] ?? $_POST['id_commande'] ?? 0);
$userId     = (int) $_SESSION['user_id'];

if ($idCommande <= 0) {
    header('Location: /mes-commandes/');
    exit;
}

$pdo = getDbConnection();

// ── Vérifier que la commande est terminée ────────────────
$stmt = $pdo->prepare("
    SELECT c.id_commande, c.statut,
           GROUP_CONCAT(m.titre SEPARATOR ', ') AS menus_titres
    FROM commande c
    LEFT JOIN ligne_commande lc ON c.id_commande = lc.id_commande
    LEFT JOIN menu m             ON lc.id_menu    = m.id_menu
    WHERE c.id_commande = :id AND c.id_utilisateur = :uid
    GROUP BY c.id_commande
");
$stmt->execute([':id' => $idCommande, ':uid' => $userId]);
$commande = $stmt->fetch();

if (!$commande || $commande['statut'] !== 'completed') {
    $_SESSION['flash_error'] = 'Vous ne pouvez laisser un avis que pour une commande terminée.';
    header('Location: /mes-commandes/');
    exit;
}

// ── Vérifier qu'un avis n'existe pas déjà ────────────────
$stmtCheck = $pdo->prepare("
    SELECT id_avis FROM avis
    WHERE id_commande = :id AND id_utilisateur = :uid
    LIMIT 1
");
$stmtCheck->execute([':id' => $idCommande, ':uid' => $userId]);
$avisExistant = $stmtCheck->fetch();

if ($avisExistant) {
    $_SESSION['flash_error'] = 'Vous avez déjà laissé un avis pour cette commande.';
    header('Location: /mes-commandes/');
    exit;
}

$errors = [];
$pageTitle = 'Laisser un avis — Vite & Gourmand';

// ════════════════════════════════════════════════════════
// POST — Enregistrer l'avis
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $note        = (int) ($_POST['note'] ?? 0);
    $commentaire = trim(strip_tags($_POST['commentaire'] ?? ''));

    // Validations
    if ($note < 1 || $note > 5) {
        $errors[] = 'La note doit être entre 1 et 5.';
    }
    if (empty($commentaire)) {
        $errors[] = 'Le commentaire est obligatoire.';
    } elseif (mb_strlen($commentaire) < 10) {
        $errors[] = 'Le commentaire doit contenir au moins 10 caractères.';
    } elseif (mb_strlen($commentaire) > 1000) {
        $errors[] = 'Le commentaire ne peut pas dépasser 1000 caractères.';
    }

    if (empty($errors)) {
        try {
            $pdo->prepare("
                INSERT INTO avis
                    (id_utilisateur, id_commande, note, commentaire, statut, created_at)
                VALUES
                    (:uid, :id, :note, :commentaire, 'pending', NOW())
            ")->execute([
                ':uid'         => $userId,
                ':id'          => $idCommande,
                ':note'        => $note,
                ':commentaire' => $commentaire,
            ]);

            $_SESSION['flash_success'] = 'Merci pour votre avis ! Il sera publié après modération.';
            header('Location: /mes-commandes/');
            exit;

        } catch (PDOException $e) {
            error_log('Avis insert: ' . $e->getMessage());
            $errors[] = 'Erreur technique. Veuillez réessayer.';
        }
    }
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/mes-commandes/avis.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
