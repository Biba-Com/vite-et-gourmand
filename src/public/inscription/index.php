<?php

/**
 * Page: Inscription
 * Path: src/public/inscription/index.php
 * Sécurité : CSRF token · session · validation serveur complète
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

$errors  = [];
$oldData = [];

/* ----------------------------------------------------------------
   Traitement POST
   ---------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* Vérification CSRF */
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $errors['global'] = $currentLang === 'en'
            ? 'Security error. Please try again.'
            : 'Erreur de sécurité. Veuillez réessayer.';
    } else {
        /* Récupération et nettoyage */
        $prenom          = trim($_POST['prenom'] ?? '');
        $nom             = trim($_POST['nom'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $telephone       = trim($_POST['telephone'] ?? '');
        $password        = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $cgv             = !empty($_POST['cgv']);

        /* Conservation des données saisies (sauf passwords) */
        $oldData = [
            'prenom'    => htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'),
            'nom'       => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
            'email'     => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            'telephone' => htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8'),
        ];

        /* ---- Validations ---- */
        if (empty($prenom) || mb_strlen($prenom) < 2) {
            $errors['prenom'] = $currentLang === 'en'
                ? 'First name must be at least 2 characters.'
                : 'Le prénom doit contenir au moins 2 caractères.';
        }

        if (empty($nom) || mb_strlen($nom) < 2) {
            $errors['nom'] = $currentLang === 'en'
                ? 'Last name must be at least 2 characters.'
                : 'Le nom doit contenir au moins 2 caractères.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = $currentLang === 'en'
                ? 'Please enter a valid email address.'
                : 'Veuillez saisir une adresse email valide.';
        }

        if (!empty($telephone) && !preg_match('/^[0-9\s\+\-\(\)]{10,15}$/', $telephone)) {
            $errors['telephone'] = $currentLang === 'en'
                ? 'Invalid phone number format.'
                : 'Format de téléphone invalide.';
        }

        if (mb_strlen($password) < 8) {
            $errors['password'] = $currentLang === 'en'
                ? 'Password must be at least 8 characters.'
                : 'Le mot de passe doit contenir au moins 8 caractères.';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = $currentLang === 'en'
                ? 'Passwords do not match.'
                : 'Les mots de passe ne correspondent pas.';
        }

        if (!$cgv) {
            $errors['cgv'] = $currentLang === 'en'
                ? 'You must accept the Terms of Service.'
                : 'Vous devez accepter les Conditions Générales.';
        }

        /* ---- Appel controller si pas d'erreurs ---- */
        if (empty($errors)) {
            require_once BASE_PATH . '/controllers/AuthController.php';
            $auth   = new AuthController();
            $result = $auth->register([
                'prenom'    => $prenom,
                'nom'       => $nom,
                'email'     => $email,
                'password'  => $password,
                'telephone' => $telephone,
            ]);

            if ($result['success']) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['flash_success'] = $currentLang === 'en'
                    ? 'Welcome! Your account has been created.'
                    : 'Bienvenue ! Votre compte a été créé avec succès.';
                header('Location: /');
                exit;
            } else {
                /* Erreur BDD (ex: email déjà utilisé) */
                $field = $result['field'] ?? 'global';
                $errors[$field] = $result['message'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        } else {
            /* Régénérer CSRF même si validation échoue */
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}

/* ----------------------------------------------------------------
   Rendu via base.php
   ---------------------------------------------------------------- */
$pageTitle   = $currentLang === 'en'
    ? 'Create an account — Vite & Gourmand'
    : 'Créer un compte — Vite & Gourmand';

$pageDesc    = $currentLang === 'en'
    ? 'Create your Vite & Gourmand account to place orders and request quotes for your events.'
    : 'Créez votre compte Vite & Gourmand pour passer des commandes et demander des devis pour vos événements.';

$currentPage = 'inscription';

ob_start();
include BASE_PATH . '/views/pages/inscription.php';
$content = ob_get_clean();

include BASE_PATH . '/views/layouts/base.php';
