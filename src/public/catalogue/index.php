<?php

/**
 * ============================================================
 * Vite & Gourmand — Point d'entrée Catalogue
 * ============================================================
 * Chemin : src/public/catalogue/index.php
 * URL     : http://localhost/vite-et-gourmand/catalogue/
 *
 * Orchestration MVC :
 *  1. Session + dépendances
 *  2. Controller récupère les données
 *  3. Output buffering → capture la vue fragment
 *  4. Injection dans le layout base.php
 * ============================================================
 */

// ── 1. Session & dépendances ──────────────────────────────────
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../models/MenuModel.php';
require_once __DIR__ . '/../../controllers/MenuController.php';

// ── 2. Métadonnées de la page ─────────────────────────────────
$currentPage = 'catalogue';
$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';

$pageTitle = $isEn
    ? 'Our Menus — Vite & Gourmand'
    : 'Nos Menus — Vite & Gourmand';

$pageDesc = $isEn
    ? 'Discover our catering menus for weddings, corporate events and more.'
    : 'Découvrez nos menus traiteur pour mariages, événements d\'entreprise et bien plus.';

// ── 3. Récupérer les données via le Controller ────────────────
$controller      = new MenuController();
$filtres         = $controller->getFiltresCatalogue();
$pdo             = getDbConnection();
$menuModel       = new MenuModel($pdo);
$menus           = $menuModel->getAll($filtres);
$themes          = $menuModel->getThemes();
$regimes         = $menuModel->getRegimes();
$allergenesList  = $menuModel->getAllergenesList();

// ── 4. Capturer la vue fragment en output buffering ───────────
ob_start();
require_once __DIR__ . '/../../views/catalogue/index.php';
$content = ob_get_clean();

// ── 5. Injecter dans le layout ────────────────────────────────
require_once __DIR__ . '/../../views/layouts/base.php';
