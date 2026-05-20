<?php

/**
 * ============================================================
 * Vite & Gourmand — Point d'entrée Panier
 * ============================================================
 * Chemin : src/public/panier/index.php
 *
 * GET            → Affiche le panier
 * POST action=remove → Supprime un item
 * POST action=clear  → Vide le panier
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'catalogue';

$pageTitle = $isEn ? 'My Cart — Vite & Gourmand' : 'Mon panier — Vite & Gourmand';
$pageDesc  = $isEn ? 'Review your cart before ordering.' : 'Vérifiez votre panier avant de commander.';

// ── Message flash ────────────────────────────────────────
$flashSuccess = null;
if (!empty($_SESSION['flash_success'])) {
    $flashSuccess = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// ── Traitement POST ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Supprimer un item
    if ($action === 'remove' && !empty($_POST['cart_key'])) {
        $key = $_POST['cart_key'];
        unset($_SESSION['panier'][$key]);
        header('Location: /panier/');
        exit;
    }

    // Vider le panier
    if ($action === 'clear') {
        $_SESSION['panier'] = [];
        header('Location: /panier/');
        exit;
    }
}

// ── Calculer les totaux ──────────────────────────────────
$panier       = $_SESSION['panier'] ?? [];
$totalPanier  = 0.0;
$totalRemises = 0.0;
$totalFrais   = 0.0;

foreach ($panier as $item) {
    $totalPanier  += $item['sous_total'];
    $totalRemises += $item['remise'];
    $totalFrais   += $item['frais_livraison'];
}

$totalGeneral = $totalPanier + $totalFrais;

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/panier/index.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
