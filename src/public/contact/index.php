<?php

/**
 * ============================================================
 * Vite & Gourmand — Page Contact
 * ============================================================
 * Chemin : src/public/contact/index.php
 *
 * GET  → Affiche le formulaire
 * POST → Enregistre le message + redirige
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'contact';

$pageTitle = $isEn ? 'Contact — Vite & Gourmand' : 'Contact — Vite & Gourmand';
$pageDesc  = $isEn
    ? 'Contact our team for any question or quote request.'
    : 'Contactez notre équipe pour toute question ou demande de devis.';

$errors  = [];
$success = false;
$old     = [];

// ── Message flash (si déjà envoyé) ──────────────────────
if (!empty($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// ════════════════════════════════════════════════════════
// POST — Traitement formulaire
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom         = trim(strip_tags($_POST['nom_expediteur']   ?? ''));
    $email       = trim(strip_tags($_POST['email_expediteur'] ?? ''));
    $telephone   = trim(strip_tags($_POST['telephone']        ?? ''));
    $sujet       = trim(strip_tags($_POST['sujet']            ?? ''));
    $message     = trim(strip_tags($_POST['message']          ?? ''));

    $old = compact('nom', 'email', 'telephone', 'sujet', 'message');

    // ── Validations ──────────────────────────────────────
    if (empty($nom) || mb_strlen($nom) < 2) {
        $errors['nom'] = 'Le nom est obligatoire (min. 2 caractères).';
    }

    if (empty($email)) {
        $errors['email'] = 'L\'email est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'adresse email n\'est pas valide.';
    }

    if (!empty($telephone)) {
        $telClean = preg_replace('/[\s\.\-]/', '', $telephone);
        if (!preg_match('/^(\+33|0)[1-9]\d{8}$/', $telClean)) {
            $errors['telephone'] = 'Numéro de téléphone invalide.';
        }
    }

    if (empty($sujet) || mb_strlen($sujet) < 3) {
        $errors['sujet'] = 'Le sujet est obligatoire (min. 3 caractères).';
    } elseif (mb_strlen($sujet) > 200) {
        $errors['sujet'] = 'Le sujet ne peut pas dépasser 200 caractères.';
    }

    if (empty($message) || mb_strlen($message) < 10) {
        $errors['message'] = 'Le message est obligatoire (min. 10 caractères).';
    } elseif (mb_strlen($message) > 2000) {
        $errors['message'] = 'Le message ne peut pas dépasser 2000 caractères.';
    }

    // ── Insertion si pas d'erreurs ────────────────────────
    if (empty($errors)) {
        try {
            $pdo = getDbConnection();
            $pdo->prepare("
                INSERT INTO message_contact
                    (nom_expediteur, email_expediteur, telephone_expediteur,
                     sujet, message, statut)
                VALUES
                    (:nom, :email, :telephone, :sujet, :message, 'unread')
            ")->execute([
                ':nom'       => $nom,
                ':email'     => $email,
                ':telephone' => $telephone ?: null,
                ':sujet'     => $sujet,
                ':message'   => $message,
            ]);

            // TODO: Envoyer email à jose.viteetgourmand@gmail.com

            $_SESSION['flash_success'] = $isEn
                ? 'Your message has been sent! We\'ll get back to you within 24 hours.'
                : 'Votre message a été envoyé ! Nous vous répondons sous 24h.';

            header('Location: /contact/');
            exit;

        } catch (PDOException $e) {
            error_log('Contact insert: ' . $e->getMessage());
            $errors['global'] = 'Erreur technique. Veuillez réessayer.';
        }
    }
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/pages/contact.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
