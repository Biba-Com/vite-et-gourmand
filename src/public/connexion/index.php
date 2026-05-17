<?php

/**
 * Page: Connexion
 * Path: src/public/connexion/index.php
 * Sécurité : CSRF token · session · validation serveur
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/config/lang.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$currentLang = function_exists('currentLang') ? currentLang() : 'fr';

/* Redirection si déjà connecté */
if (!empty($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

/* Génération CSRF */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error   = null;
$oldData = [];

/* ----------------------------------------------------------------
   Traitement POST
   ---------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Vérification CSRF */
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = $currentLang === 'en'
            ? 'Security error. Please try again.'
            : 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        /* Validation basique avant appel controller */
        if (empty($email) || empty($password)) {
            $error = $currentLang === 'en'
                ? 'Please fill in all fields.'
                : 'Veuillez remplir tous les champs.';
            $oldData['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = $currentLang === 'en'
                ? 'Invalid email address.'
                : 'Adresse email invalide.';
            $oldData['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        } else {
            require_once BASE_PATH . '/controllers/AuthController.php';
            $auth   = new AuthController();
            $result = $auth->login($email, $password, $remember);

            if ($result['success']) {
                /* Régénérer le CSRF après login réussi */
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                header('Location: /');
                exit;
            } else {
                $error            = $result['message'];
                $oldData['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                /* Régénérer le CSRF après échec aussi */
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    }
}

/* ----------------------------------------------------------------
   Rendu via base.php
   ---------------------------------------------------------------- */
$pageTitle   = $currentLang === 'en'
    ? 'Sign In — Vite & Gourmand'
    : 'Connexion — Vite & Gourmand';

$pageDesc    = $currentLang === 'en'
    ? 'Sign in to your Vite & Gourmand personal account to manage your orders and quotes.'
    : 'Connectez-vous à votre espace Vite & Gourmand pour gérer vos commandes et devis.';

$currentPage = 'connexion';

ob_start();
include BASE_PATH . '/views/pages/connexion.php';
$content = ob_get_clean();

include BASE_PATH . '/views/layouts/base.php';
