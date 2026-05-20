<?php

/**
 * ============================================================
 * Vite & Gourmand — Point d'entrée Mes Commandes
 * ============================================================
 * Chemin : src/public/mes-commandes/index.php
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

$pageTitle = $isEn ? 'My Orders — Vite & Gourmand' : 'Mes commandes — Vite & Gourmand';
$pageDesc  = '';

AuthController::requireAuth('/connexion/');

$userId = (int) $_SESSION['user_id'];
$pdo    = getDbConnection();

// ── Messages flash ───────────────────────────────────────
$flashSuccess = null;
$flashError   = null;

if (!empty($_SESSION['flash_success'])) {
    $flashSuccess = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (!empty($_SESSION['flash_error'])) {
    $flashError = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

// ── Récupérer les commandes ──────────────────────────────
$stmt = $pdo->prepare("
    SELECT
        c.id_commande,
        c.date_evenement,
        c.adresse_livraison,
        c.code_postal_livraison,
        c.ville_livraison,
        c.nb_personnes,
        c.sous_total,
        c.montant_remise,
        c.frais_livraison,
        c.total,
        c.statut,
        c.created_at,
        GROUP_CONCAT(m.titre ORDER BY lc.id_ligne SEPARATOR ', ') AS menus_titres
    FROM commande c
    LEFT JOIN ligne_commande lc ON c.id_commande = lc.id_commande
    LEFT JOIN menu m             ON lc.id_menu    = m.id_menu
    WHERE c.id_utilisateur = :id
    GROUP BY c.id_commande
    ORDER BY c.created_at DESC
");
$stmt->execute([':id' => $userId]);
$commandes = $stmt->fetchAll();

// ── Labels statuts ───────────────────────────────────────
$statutLabels = [
    'pending'        => ['label' => 'En attente',     'color' => '#F59E0B'],
    'confirmed'      => ['label' => 'Confirmée',       'color' => '#3B82F6'],
    'in_preparation' => ['label' => 'En préparation', 'color' => '#8B5CF6'],
    'in_delivery'    => ['label' => 'En livraison',   'color' => '#06B6D4'],
    'completed'      => ['label' => 'Terminée',        'color' => '#10B981'],
    'cancelled'      => ['label' => 'Annulée',         'color' => '#EF4444'],
];

ob_start();
require_once __DIR__ . '/../../views/mes-commandes/index.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
