<?php

/**
 * ============================================================
 * Vite & Gourmand — Modify order entry point
 * ============================================================
 * Path: src/public/mes-commandes/modifier.php
 *
 * GET  → shows the pre-filled form
 * POST → checks the CSRF token, delegates to CommandeController,
 *        then redirects
 *
 * Rule from the specifications: everything can be changed except
 * the menu, and only while the order is still "pending".
 *
 * Thin entry point: amounts are recomputed by CommandeService and
 * saved by CommandeRepository, exactly like a new order.
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../controllers/CommandeController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'mes-commandes';

$pageTitle = $isEn ? 'Edit Order — Vite & Gourmand' : 'Modifier la commande — Vite & Gourmand';

AuthController::requireAuth('/connexion/');

$idCommande = (int) ($_GET['id'] ?? $_POST['id_commande'] ?? 0);
$userId     = (int) $_SESSION['user_id'];

if ($idCommande <= 0) {
    header('Location: /mes-commandes/');
    exit;
}

$controller = new CommandeController(getDbConnection());

// ── The order must exist, belong to the user and be pending ─
$commande = $controller->getEditableOrder($idCommande, $userId);

if ($commande === null) {
    $_SESSION['flash_error'] = 'Cette commande ne peut plus être modifiée.';
    header('Location: /mes-commandes/');
    exit;
}

$errors    = [];
$csrfToken = AuthController::generateCsrfToken();

// ════════════════════════════════════════════════════════════
// POST → CSRF check, then delegate to the controller
// ════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Session expirée. Veuillez réessayer.';
    } else {
        $result = $controller->updateOrder($idCommande, $_POST, $userId);

        if ($result['success']) {
            AuthController::regenerateCsrfToken();
            $_SESSION['flash_success'] = 'Commande #' . str_pad((string) $idCommande, 4, '0', STR_PAD_LEFT) . ' modifiée avec succès.';
            header('Location: /mes-commandes/');
            exit;
        }

        $errors = $result['errors'];
    }
}

// ── Display ──────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/mes-commandes/modifier.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
