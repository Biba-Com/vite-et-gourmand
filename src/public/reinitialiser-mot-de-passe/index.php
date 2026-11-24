<?php

/**
 * ============================================================
 * Vite & Gourmand — Réinitialisation du mot de passe
 * ============================================================
 * Chemin : src/public/reinitialiser-mot-de-passe/index.php
 *
 * GET  → Vérifie le token + affiche formulaire nouveau MDP
 * POST → Valide + met à jour le mot de passe
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'connexion';
$pageTitle   = $isEn ? 'Reset password — Vite & Gourmand' : 'Réinitialiser le mot de passe — Vite & Gourmand';
$pageDesc    = '';

$token   = trim(strip_tags($_GET['token'] ?? $_POST['token'] ?? ''));
$errors  = [];
$success = false;
$user    = null;

// ── Valider le token ─────────────────────────────────────
if (empty($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $tokenInvalid = true;
} else {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("
        SELECT t.id_token, u.id_utilisateur, u.prenom, u.email
        FROM token_mot_de_passe t
        JOIN utilisateur u ON u.id_utilisateur = t.id_utilisateur
        WHERE t.token_hash = :hash
          AND t.expires_at > NOW()
          AND t.used_at IS NULL
          AND u.is_active  = 1
        LIMIT 1
    ");
    $stmt->execute([':hash' => hash('sha256', $token)]);
    $user = $stmt->fetch();

    $tokenInvalid = !$user;
}

// ════════════════════════════════════════════════════════
// POST — Mettre à jour le mot de passe
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$tokenInvalid && $user) {

    $password        = $_POST['password']         ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // Règles énoncé page 5 : 10 car. min, maj, min, chiffre, spécial
    if (mb_strlen($password) < 10) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 10 caractères.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = 'Le mot de passe doit contenir au moins une majuscule.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors['password'] = 'Le mot de passe doit contenir au moins une minuscule.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Le mot de passe doit contenir au moins un chiffre.';
    } elseif (!preg_match('/[\W_]/', $password)) {
        $errors['password'] = 'Le mot de passe doit contenir au moins un caractère spécial.';
    } elseif ($password !== $passwordConfirm) {
        $errors['password_confirm'] = 'Les mots de passe ne correspondent pas.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // New password and token consumption in one transaction (single use)
        $pdo->beginTransaction();
        $pdo->prepare("
            UPDATE utilisateur
            SET mot_de_passe = :hash,
                updated_at   = NOW()
            WHERE id_utilisateur = :id
        ")->execute([':hash' => $hash, ':id' => $user['id_utilisateur']]);
        $pdo->prepare("
            UPDATE token_mot_de_passe SET used_at = NOW() WHERE id_token = :token
        ")->execute([':token' => $user['id_token']]);
        $pdo->commit();

        $success = true;

        // Rediriger vers connexion après 3 secondes
        header('Refresh: 3; url=/connexion/');
    }
}

ob_start();
require_once __DIR__ . '/../../views/pages/reinitialiser-mot-de-passe.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
