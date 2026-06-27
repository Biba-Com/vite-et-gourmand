<?php

/**
 * ============================================================
 * Vite & Gourmand — Commande entry point
 * ============================================================
 * Path: src/public/commande/index.php
 *
 * GET  → shows the pre-filled order form
 * POST → delegates to CommandeController, then redirects
 *
 * Access: logged-in customers only.
 *
 * This is a THIN entry point: it only wires the pieces together
 * and handles HTTP (session, redirects). All the real work is done
 * by the controller → service → repository chain.
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/CommandeController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'commande';

$pageTitle = $isEn ? 'Order — Vite & Gourmand' : 'Commander — Vite & Gourmand';
$pageDesc  = '';

// ── Login required ───────────────────────────────────────
AuthController::requireAuth('/connexion/');

// ── Cart required ────────────────────────────────────────
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

// ── First cart item (used by the view to pre-fill the form) ─
$panier    = $_SESSION['panier'];
$firstItem = reset($panier);

$errors = [];

// ════════════════════════════════════════════════════════════
// POST → delegate everything to the controller
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $controller = new CommandeController($pdo);
    $result = $controller->submitOrder($_POST, (int) $user['id_utilisateur'], $panier);

    if ($result['success']) {
        // Order saved: empty the cart and go to the orders page.
        unset($_SESSION['panier']);
        $_SESSION['flash_success'] =
            'Votre commande #' . $result['orderId'] . ' a été enregistrée ! Nous vous contactons sous 24h.';
        header('Location: /mes-commandes/');
        exit;
    }

    // Something went wrong: show the errors in the form.
    $errors = $result['errors'];
}

// ── Display ──────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/commande/index.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';