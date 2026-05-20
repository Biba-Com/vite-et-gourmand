<?php

/**
 * ============================================================
 * Vite & Gourmand — Suivi de commande
 * ============================================================
 * Chemin : src/public/mes-commandes/detail.php
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

$idCommande = (int) ($_GET['id'] ?? 0);
$userId     = (int) $_SESSION['user_id'];

if ($idCommande <= 0) {
    header('Location: /mes-commandes/');
    exit;
}

$pdo = getDbConnection();

// ── Récupérer la commande ────────────────────────────────
$stmt = $pdo->prepare("
    SELECT
        c.*,
        GROUP_CONCAT(m.titre ORDER BY lc.id_ligne SEPARATOR ', ') AS menus_titres
    FROM commande c
    LEFT JOIN ligne_commande lc ON c.id_commande = lc.id_commande
    LEFT JOIN menu m             ON lc.id_menu    = m.id_menu
    WHERE c.id_commande = :id AND c.id_utilisateur = :uid
    GROUP BY c.id_commande
");
$stmt->execute([':id' => $idCommande, ':uid' => $userId]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: /mes-commandes/');
    exit;
}

$pageTitle = 'Suivi commande #' . str_pad($idCommande, 4, '0', STR_PAD_LEFT) . ' — Vite & Gourmand';

// ── Récupérer l'historique des statuts ───────────────────
$stmtHist = $pdo->prepare("
    SELECT
        h.statut_precedent,
        h.nouveau_statut,
        h.motif,
        h.created_at,
        u.prenom,
        u.nom
    FROM historique_statut h
    LEFT JOIN utilisateur u ON h.id_utilisateur = u.id_utilisateur
    WHERE h.id_commande = :id
    ORDER BY h.created_at ASC
");
$stmtHist->execute([':id' => $idCommande]);
$historique = $stmtHist->fetchAll();

// ── Définition des étapes dans l'ordre ──────────────────
$etapes = [
    'pending'        => ['label' => 'En attente',           'icon' => '⏳', 'color' => '#F59E0B'],
    'confirmed'      => ['label' => 'Confirmée',             'icon' => '✅', 'color' => '#3B82F6'],
    'in_preparation' => ['label' => 'En préparation',       'icon' => '👨‍🍳', 'color' => '#8B5CF6'],
    'in_delivery'    => ['label' => 'En cours de livraison','icon' => '🚚', 'color' => '#06B6D4'],
    'completed'      => ['label' => 'Terminée',              'icon' => '🎉', 'color' => '#10B981'],
    'cancelled'      => ['label' => 'Annulée',               'icon' => '❌', 'color' => '#EF4444'],
];

$statutActuel = $commande['statut'];

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/mes-commandes/detail.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
