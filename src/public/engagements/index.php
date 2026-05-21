<?php

/**
 * ============================================================
 * Vite & Gourmand — Page Nos Engagements
 * ============================================================
 * Chemin : src/public/engagements/index.php
 * ============================================================
 */

session_start();

define('BASE_PATH', dirname(__DIR__, 2));

require BASE_PATH . '/config/database.php';
require BASE_PATH . '/config/lang.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'engagements';

$pageTitle = $isEn ? 'Our Commitments — Vite & Gourmand' : 'Nos Engagements — Vite & Gourmand';
$pageDesc  = $isEn
    ? 'Discover our commitment to local sourcing, artisan expertise and eco-responsibility.'
    : 'Découvrez nos engagements : approvisionnement local, savoir-faire artisanal et démarche éco-responsable.';

$pdo = getDbConnection();

// ── Partenaires locaux ───────────────────────────────────
$stmtPart = $pdo->prepare("
    SELECT nom, description, logo_url, site_web, categorie
    FROM partenaire
    WHERE is_active = 1
    ORDER BY nom ASC
");
$stmtPart->execute();
$partenaires = $stmtPart->fetchAll();

// ── Membres équipe ───────────────────────────────────────
$stmtEquipe = $pdo->prepare("
    SELECT prenom, nom, poste, bio, photo_url
    FROM membre_equipe
    WHERE is_active = 1
    ORDER BY ordre_affichage ASC
");
$stmtEquipe->execute();
$equipe = $stmtEquipe->fetchAll();

// ── Certifications ───────────────────────────────────────
$stmtCert = $pdo->prepare("
    SELECT nom, description, logo_url
    FROM certification
    ORDER BY id_certification ASC
");
$stmtCert->execute();
$certifications = $stmtCert->fetchAll();

ob_start();
require BASE_PATH . '/views/pages/engagements.php';
$content = ob_get_clean();
require BASE_PATH . '/views/layouts/base.php';
