<?php

/**
 * Section: Hero — Page d'accueil
 * Path: src/views/components/hero.php
 *
 * Image : hero-buffet-traiteur-bordeaux.webp (fichier existant dans /hero/)
 * Contenu : aligné à gauche, tagline + H1 + subtitle + 2 CTAs
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';
?>

<section class="hero" aria-label="<?= $currentLang === 'en' ? 'Welcome banner' : 'Bannière d\'accueil' ?>">

  <picture class="hero__picture">
    <img
      src="/assets/img/hero/hero-buffet-bordeaux.webp"
      alt="<?= $currentLang === 'en' ? 'Luxury gastronomic buffet with refined dishes for celebrations in Bordeaux' : 'Buffet gastronomique de luxe avec mets raffinés pour célébrations à Bordeaux' ?>"
      class="hero__img"
      width="1920" height="1080"
      fetchpriority="high"
      decoding="async">
  </picture>

  <div class="container hero__container">

    <p class="hero__tagline">
      <span class="hero__tagline-line" aria-hidden="true"></span>
      <?= $currentLang === 'en' ? 'Artisan Caterer from Bordeaux' : 'Traiteur Artisanal &amp; Bordelais' ?>
    </p>

    <h1 class="hero__title">
      <?= $currentLang === 'en' ? '25 Years of Culinary' : '25 ans d\'Excellence' ?>
      <br>
      <em><?= $currentLang === 'en' ? 'Excellence' : 'Culinaire' ?></em>
    </h1>

    <p class="hero__description">
      <?= $currentLang === 'en' ? 'Elevate your events with refined, local and committed gastronomy. An artisan expertise at the service of your taste buds.' : 'Sublimez vos événements avec une gastronomie raffinée, locale et engagée. Un savoir-faire artisanal au service de vos papilles.' ?>
    </p>

    <div class="hero__actions">
      <a href="/catalogue/" class="hero__btn--primary"
        aria-label="<?= $currentLang === 'en' ? 'Discover all our gastronomic menus' : 'Découvrir tous nos menus gastronomiques' ?>">
        <?= $currentLang === 'en' ? 'Discover our Menus' : 'Découvrir nos Menus' ?>
      </a>

      <a href="/contact/" class="hero__btn--secondary"
        aria-label="<?= $currentLang === 'en' ? 'Request a free quote for your event' : 'Demander un devis gratuit pour votre événement' ?>">
        <?= $currentLang === 'en' ? 'Request a Quote' : 'Demander un Devis' ?>
      </a>
    </div>

  </div>

  <span class="hero__scroll" aria-hidden="true">
    <span class="hero__scroll-line"></span>
  </span>
</section>