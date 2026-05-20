<?php

/**
 * ============================================================
 * Vite & Gourmand — Point d'entrée Connexion
 * ============================================================
 * Chemin : src/public/connexion/index.php
 *
 * GET  → Affiche le formulaire
 * POST → Traite la connexion via AuthController
 *
 * Variables transmises à la vue :
 *  - $error     string|null  Message d'erreur unique
 *  - $success   string|null  Message flash (après inscription)
 *  - $oldData   array        Valeurs précédentes (email)
 *  - $csrfToken string       Token CSRF
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
$currentPage = 'connexion';

$pageTitle = $isEn ? 'Login — Vite & Gourmand' : 'Connexion — Vite & Gourmand';
$pageDesc  = $isEn ? 'Login to your account' : 'Connectez-vous à votre espace personnel';

// ── Si déjà connecté → rediriger ─────────────────────────
if (AuthController::isLoggedIn()) {
    header('Location: /');
    exit;
}

// ── Initialiser ──────────────────────────────────────────
$pdo            = getDbConnection();
$userModel      = new UserModel($pdo);
$authController = new AuthController($userModel, $pdo);

$csrfToken = AuthController::generateCsrfToken();
$error     = null;
$success   = null;
$oldData   = ['email' => ''];

// ── Message flash (après inscription réussie) ────────────
if (!empty($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// ── Traitement POST ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldData['email'] = trim(strip_tags($_POST['email'] ?? ''));

    $result = $authController->login($_POST);

    if ($result['success']) {
        $redirect = $_SESSION['redirect_after_login'] ?? '/';
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirect);
        exit;
    }

    $error = $result['message'];
    $csrfToken = AuthController::generateCsrfToken();
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/pages/connexion.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
