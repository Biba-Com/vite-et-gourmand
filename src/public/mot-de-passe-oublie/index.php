<?php

/**
 * ============================================================
 * Vite & Gourmand — Mot de passe oublié
 * ============================================================
 * Chemin : src/public/mot-de-passe-oublie/index.php
 *
 * Fix timezone : expiry calculé par MySQL (NOW() + INTERVAL)
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../models/UserModel.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'connexion';
$pageTitle   = $isEn ? 'Forgot password — Vite & Gourmand' : 'Mot de passe oublié — Vite & Gourmand';
$pageDesc    = '';

$errors  = [];
$success = false;
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim(strip_tags($_POST['email'] ?? '')));
    $old   = ['email' => $email];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse email invalide.';
    }

    if (empty($errors)) {
        $pdo       = getDbConnection();
        $userModel = new UserModel($pdo);
        $user      = $userModel->findByEmail($email);

        if ($user && $user['is_active']) {

            // Générer un token sécurisé
            $token = bin2hex(random_bytes(32));

            // ── Fix timezone ─────────────────────────────────
            // On laisse MySQL calculer l'expiry avec son propre NOW()
            // pour éviter tout décalage UTC vs heure locale
            // Only one valid link at a time: older unused tokens are closed
            $pdo->prepare("
                UPDATE token_mot_de_passe SET used_at = NOW()
                WHERE id_utilisateur = :id AND used_at IS NULL
            ")->execute([':id' => $user['id_utilisateur']]);

            // Only the SHA-256 hash is stored: a database leak does not expose usable links
            $pdo->prepare("
                INSERT INTO token_mot_de_passe (id_utilisateur, token_hash, expires_at)
                VALUES (:id, :hash, DATE_ADD(NOW(), INTERVAL 1 HOUR))
            ")->execute([
                ':id'   => $user['id_utilisateur'],
                ':hash' => hash('sha256', $token),
            ]);

            // Construire le lien
            $resetLink = '/reinitialiser-mot-de-passe/?token=' . $token;

            // Log email simulé
            try {
                $pdo->prepare("
                    INSERT INTO log_email
                        (destinataire, sujet, template, statut, type_entite, id_entite, envoye_a)
                    VALUES
                        (:email, 'Réinitialisation de votre mot de passe',
                         'password_reset', 'sent', 'utilisateur', :id, NOW())
                ")->execute([
                    ':email' => $email,
                    ':id'    => $user['id_utilisateur'],
                ]);
            } catch (PDOException $e) {
                error_log('Log email reset: ' . $e->getMessage());
            }

            // Mode développement : afficher le lien
            // No e-mail server: the link is written to the server log only,
            // never shown on screen (otherwise anyone could reset any account).
            error_log('Password reset link for user #' . $user['id_utilisateur'] . ': ' . $resetLink);
        }

        $success = true;
    }
}

ob_start();
require_once __DIR__ . '/../../views/pages/mot-de-passe-oublie.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
