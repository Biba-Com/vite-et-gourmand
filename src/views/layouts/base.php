<?php

/**
 * Layout: Base HTML template
 * Path: src/views/layouts/base.php
 */

$pageTitle   = $pageTitle   ?? __('meta.home.title');
$pageDesc    = $pageDesc    ?? __('meta.home.desc');
$currentPage = $currentPage ?? 'accueil';
$assetsBase  = $assetsBase  ?? '/assets';
$currentLang = currentLang();
$baseUrl     = 'https://www.viteetgourmand.fr';
$currentPath = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8') ?>" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="theme-color" content="#063A1F">

  <!-- SEO Canonical + hreflang -->
  <link rel="canonical" href="<?= $baseUrl . $currentPath ?>?lang=<?= $currentLang ?>">
  <link rel="alternate" hreflang="fr"        href="<?= $baseUrl . $currentPath ?>?lang=fr">
  <link rel="alternate" hreflang="en"        href="<?= $baseUrl . $currentPath ?>?lang=en">
  <link rel="alternate" hreflang="x-default" href="<?= $baseUrl . $currentPath ?>">

  <!-- Open Graph -->
  <meta property="og:title"       content="<?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($pageDesc,  ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:type"        content="website">
  <meta property="og:locale"      content="<?= $currentLang === 'en' ? 'en_GB' : 'fr_FR' ?>">

  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Favicon -->
  <link rel="icon" href="<?= $assetsBase ?>/img/logos/favicon.svg" type="image/svg+xml">

  <!-- Styles globaux -->
  <link rel="stylesheet" href="<?= $assetsBase ?>/css/main.css">

  <!-- Styles conditionnels par page -->
  <?php if ($currentPage === 'accueil'): ?>
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/home.css">
  <?php elseif (in_array($currentPage, ['connexion', 'inscription'])): ?>
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/auth.css">
  <?php elseif ($currentPage === 'catalogue'): ?>
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/catalogue.css">
  <?php elseif ($currentPage === 'panier'): ?>
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/panier.css">
  <?php elseif ($currentPage === 'mes-commandes'): ?>
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/mes-commandes.css">
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/commande.css">
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/suivi-commande.css">
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/avis.css">
  <?php elseif ($currentPage === 'contact'): ?>
    <link rel="stylesheet" href="<?= $assetsBase ?>/css/pages/contact.css">
  <?php endif; ?>

  <!-- JSON-LD Schema.org -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FoodService",
    "name": "Vite & Gourmand",
    "description": "<?= htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8') ?>",
    "url": "<?= $baseUrl ?>",
    "inLanguage": "<?= $currentLang ?>",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Bordeaux",
      "addressRegion": "Nouvelle-Aquitaine",
      "addressCountry": "FR"
    },
    "priceRange": "€€"
  }
  </script>
</head>

<body>
  <?php include __DIR__ . '/../components/header.php'; ?>

  <main id="main-content" role="main">
    <?= $content ?? '' ?>
  </main>

  <?php include __DIR__ . '/../components/footer.php'; ?>

  <!-- JS globaux -->
  <script src="<?= $assetsBase ?>/js/header.js" defer></script>
  <script src="<?= $assetsBase ?>/js/footer.js" defer></script>

  <!-- JS conditionnels par page -->
  <?php if ($currentPage === 'accueil'): ?>
    <script src="<?= $assetsBase ?>/js/home.js" defer></script>
  <?php elseif (in_array($currentPage, ['connexion', 'inscription'])): ?>
    <script src="<?= $assetsBase ?>/js/auth.js" defer></script>
  <?php elseif ($currentPage === 'catalogue'): ?>
    <script src="<?= $assetsBase ?>/js/catalogue.js" defer></script>
  <?php endif; ?>

</body>

</html>
