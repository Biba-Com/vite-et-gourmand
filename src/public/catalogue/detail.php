<?php

/**
 * ============================================================
 * Vite & Gourmand — Point d'entrée Détail Menu
 * ============================================================
 * Chemin : src/public/catalogue/detail.php
 *
 * GET  → Affiche la page détail
 * POST → Ajoute au panier + redirige
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../models/MenuModel.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'catalogue';

// ── Validation slug ──────────────────────────────────────
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

// ── Récupérer le menu ────────────────────────────────────
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

// ══════════════════════════════════════════════════════════
// POST — Ajouter au panier
// ══════════════════════════════════════════════════════════
$cartErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_to_cart') {

    // ── Connexion obligatoire (énoncé Studi page 5) ──────
    if (!AuthController::isLoggedIn()) {
        $_SESSION['redirect_after_login'] = '/catalogue/detail.php?slug=' . urlencode($slug);
        header('Location: /connexion/');
        exit;
    }

    // ── Récupération + nettoyage ─────────────────────────
    $nbPersonnes   = (int)   ($_POST['nb_personnes']    ?? 0);
    $dateEvenement = trim(strip_tags($_POST['date_evenement']   ?? ''));
    $adresse       = trim(strip_tags($_POST['adresse_livraison'] ?? ''));
    $codePostal    = trim(strip_tags($_POST['code_postal']       ?? ''));
    $ville         = trim(strip_tags($_POST['ville']             ?? ''));

    $minPersonnes = (int) $menu['nb_personnes_min'];
    $maxPersonnes = $menu['nb_personnes_max'] ? (int) $menu['nb_personnes_max'] : 9999;

    // ── Validations ──────────────────────────────────────
    if ($nbPersonnes < $minPersonnes) {
        $cartErrors[] = "Minimum {$minPersonnes} personnes requis pour ce menu.";
    } elseif ($nbPersonnes > $maxPersonnes) {
        $cartErrors[] = "Maximum {$maxPersonnes} personnes pour ce menu.";
    }

    if (empty($dateEvenement)) {
        $cartErrors[] = "La date de l'événement est obligatoire.";
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d', $dateEvenement);
        $minDate = new DateTime('+2 days');
        if (!$dateObj || $dateObj < $minDate) {
            $cartErrors[] = "La date doit être au minimum 48h à l'avance.";
        }
    }

    if (empty($adresse))    $cartErrors[] = "L'adresse de livraison est obligatoire.";
    if (empty($codePostal)) $cartErrors[] = "Le code postal est obligatoire.";
    if (empty($ville))      $cartErrors[] = "La ville est obligatoire.";

    // ── Ajouter au panier si pas d'erreurs ───────────────
    if (empty($cartErrors)) {
        $prixUnitaire = (float) $menu['prix_par_personne'];
        $sousTotal    = $prixUnitaire * $nbPersonnes;

        // Règle énoncé page 7 : -10% si nb_personnes >= (min + 5)
        $remise = 0.0;
        if ($nbPersonnes >= ($minPersonnes + 5)) {
            $remise    = round($sousTotal * 0.10, 2);
            $sousTotal = round($sousTotal - $remise, 2);
        }

        // Frais livraison (énoncé page 7 : 5€ + 0.59€/km hors Bordeaux)
        $fraisLivraison = 0.0;
        if (strtolower(trim($ville)) !== 'bordeaux') {
            $fraisLivraison = 5.00; // Distance calculée à la commande
        }

        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        $cartKey = 'menu_' . $menu['id_menu'];

        $_SESSION['panier'][$cartKey] = [
            'id_menu'           => (int) $menu['id_menu'],
            'titre'             => $menu['titre'],
            'slug'              => $menu['slug'],
            'image_url'         => $menu['image_url'] ?? null,
            'prix_unitaire'     => $prixUnitaire,
            'nb_personnes'      => $nbPersonnes,
            'nb_personnes_min'  => $minPersonnes,
            'sous_total_brut'   => round($prixUnitaire * $nbPersonnes, 2),
            'remise'            => $remise,
            'sous_total'        => $sousTotal,
            'date_evenement'    => $dateEvenement,
            'adresse_livraison' => $adresse,
            'code_postal'       => $codePostal,
            'ville'             => $ville,
            'frais_livraison'   => $fraisLivraison,
        ];

        $_SESSION['flash_success'] = '"' . $menu['titre'] . '" ajouté au panier !';
        header('Location: /panier/');
        exit;
    }
}

// ── Données pour la vue ──────────────────────────────────
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
