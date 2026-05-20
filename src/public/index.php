<?php

/**
 * Front Controller — Page d'accueil
 * Path: src/public/index.php
 */

define('BASE_PATH', dirname(__DIR__));

/* 1. Moteur i18n EN PREMIER */
require BASE_PATH . '/config/lang.php';

/* 2. Variables de la page */
$assetsBase  = '/assets';
$currentPage = 'accueil';
$pageTitle   = __('meta.home.title');
$pageDesc    = __('meta.home.desc');

/* 3. Capturer toutes les sections dans l'ordre Figma */
ob_start();

include BASE_PATH . '/views/components/hero.php';
include BASE_PATH . '/views/components/anti-gaspi.php';
include BASE_PATH . '/views/components/about.php';
include BASE_PATH . '/views/components/menus-home.php';
include BASE_PATH . '/views/components/engagements-home.php';
include BASE_PATH . '/views/components/testimonials.php';

$content = ob_get_clean();

/* 4. Rendu via layout */
require BASE_PATH . '/views/layouts/base.php';