<?php

/**
 * ============================================================
 * Vite & Gourmand — Page Nos Prestations
 * ============================================================
 * Chemin : src/public/prestations/index.php
 * ============================================================
 */

session_start();

define('BASE_PATH', dirname(__DIR__, 2));

require BASE_PATH . '/config/database.php';
require BASE_PATH . '/config/lang.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'prestations';

$pageTitle = $isEn ? 'Our Services — Vite & Gourmand' : 'Nos Prestations — Vite & Gourmand';
$pageDesc  = $isEn
    ? 'Discover our catering services: private chef, buffets, waitstaff, and more.'
    : 'Découvrez nos prestations traiteur : chef à domicile, buffets, serveurs et plus encore.';

ob_start();
require BASE_PATH . '/views/pages/prestations.php';
$content = ob_get_clean();
require BASE_PATH . '/views/layouts/base.php';
