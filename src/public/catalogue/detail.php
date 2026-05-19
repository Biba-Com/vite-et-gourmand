<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../models/MenuModel.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'catalogue';

$slug = trim(strip_tags($_GET['slug'] ?? ''));

if (empty($slug) || !preg_match('/^[a-z0-9\-]{1,180}$/', $slug)) {
    http_response_code(404);
    $pageTitle = '404 — Page introuvable';
    $pageDesc  = '';
    ob_start();
    echo '<p>Menu introuvable.</p>';
    $content = ob_get_clean();
    require_once __DIR__ . '/../../views/layouts/base.php';
    exit;
}

$pdo       = getDbConnection();
$menuModel = new MenuModel($pdo);
$menu      = $menuModel->getBySlug($slug);

if ($menu === false) {
    http_response_code(404);
    $pageTitle = '404 — Menu introuvable';
    $pageDesc  = '';
    ob_start();
    echo '<p>Ce menu n\'existe pas.</p>';
    $content = ob_get_clean();
    require_once __DIR__ . '/../../views/layouts/base.php';
    exit;
}

$composition = $menuModel->getComposition($menu['id_menu']);
$allergenes  = $menuModel->getAllergenes($menu['id_menu']);

$platsParCategorie = [];
foreach (['starter', 'main', 'dessert', 'drink', 'other'] as $cat) {
    $platsParCategorie[$cat] = [];
}
foreach ($composition as $plat) {
    $cat = $plat['categorie'] ?? 'other';
    $platsParCategorie[$cat][] = $plat;
}
$platsParCategorie = array_filter($platsParCategorie, fn($g) => !empty($g));

$pageTitle = htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') . ' — Vite & Gourmand';
$pageDesc  = htmlspecialchars($menu['description'] ?? '', ENT_QUOTES, 'UTF-8');

ob_start();
require_once __DIR__ . '/../../views/catalogue/detail.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';