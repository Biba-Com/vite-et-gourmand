<?php

/**
 * ============================================================
 * Vite & Gourmand — Annulation commande
 * ============================================================
 * Chemin : src/public/mes-commandes/annuler.php
 *
 * POST uniquement — annule une commande si statut = pending
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

AuthController::requireAuth('/connexion/');

// POST uniquement
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /mes-commandes/');
    exit;
}

$idCommande = (int) ($_POST['id_commande'] ?? 0);
$userId     = (int) $_SESSION['user_id'];

if ($idCommande <= 0) {
    header('Location: /mes-commandes/');
    exit;
}

$pdo = getDbConnection();

// ── Vérifier que la commande appartient à l'utilisateur ──
$stmt = $pdo->prepare("
    SELECT id_commande, statut
    FROM commande
    WHERE id_commande = :id AND id_utilisateur = :uid
    LIMIT 1
");
$stmt->execute([':id' => $idCommande, ':uid' => $userId]);
$commande = $stmt->fetch();

if (!$commande) {
    $_SESSION['flash_error'] = 'Commande introuvable.';
    header('Location: /mes-commandes/');
    exit;
}

// ── Vérifier que le statut permet l'annulation ───────────
if ($commande['statut'] !== 'pending') {
    $_SESSION['flash_error'] = 'Cette commande ne peut plus être annulée (déjà acceptée par notre équipe). Contactez-nous.';
    header('Location: /mes-commandes/');
    exit;
}

// ── Annuler ──────────────────────────────────────────────
try {
    $pdo->beginTransaction();

    // Mettre à jour le statut
    $pdo->prepare("
        UPDATE commande
        SET statut = 'cancelled',
            motif_annulation = 'Annulée par le client',
            id_utilisateur_annulation = :uid,
            annulee_a = NOW()
        WHERE id_commande = :id
    ")->execute([':uid' => $userId, ':id' => $idCommande]);

    // Historique statut
    $pdo->prepare("
        INSERT INTO historique_statut
            (id_commande, statut_precedent, nouveau_statut, id_utilisateur, motif, created_at)
        VALUES
            (:id, 'pending', 'cancelled', :uid, 'Annulée par le client depuis son espace', NOW())
    ")->execute([':id' => $idCommande, ':uid' => $userId]);

    $pdo->commit();

    $_SESSION['flash_success'] = 'Commande #' . str_pad($idCommande, 4, '0', STR_PAD_LEFT) . ' annulée avec succès.';

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Annulation commande: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Erreur technique. Veuillez réessayer.';
}

header('Location: /mes-commandes/');
exit;
