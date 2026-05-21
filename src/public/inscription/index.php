<?php

/**
 * ============================================================
 * Vite & Gourmand — Point d'entrée Inscription
 * ============================================================
 * Chemin : src/public/inscription/index.php
 *
 * GET  → Affiche le formulaire
 * POST → Traite l'inscription via AuthController
 *
 * Variables transmises à la vue :
 *  - $errors   array  Erreurs par champ ['prenom' => '...', 'email' => '...']
 *  - $oldData  array  Valeurs précédentes (sauf mot de passe)
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
$currentPage = 'inscription';

$pageTitle = $isEn ? 'Register — Vite & Gourmand' : 'Créer un compte — Vite & Gourmand';
$pageDesc  = $isEn ? 'Create your account' : 'Rejoignez Vite & Gourmand et gérez vos commandes';

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
$errors    = [];
$oldData   = [];

// ── Traitement POST ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conserver les valeurs saisies (sauf mot de passe)
    $oldData = [
        'prenom'    => trim(strip_tags($_POST['prenom'] ?? '')),
        'nom'       => trim(strip_tags($_POST['nom'] ?? '')),
        'email'     => trim(strip_tags($_POST['email'] ?? '')),
        'telephone' => trim(strip_tags($_POST['telephone'] ?? '')),
    ];

    $result = $authController->register($_POST);

    if ($result['success']) {
        $_SESSION['flash_success'] = $isEn
            ? 'Account created! You can now sign in.'
            : 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
        header('Location: /connexion/');
        exit;
    }

    // Erreurs au format ['champ' => 'message'] — exactement ce qu'attend la vue
    $errors    = $result['errors'];
    $csrfToken = AuthController::generateCsrfToken();
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/pages/inscription.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
