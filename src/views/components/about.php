<?php

/**
 * Section: Une Cuisine d'Exception
 * Path: src/views/components/about.php
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';
?>

<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "AboutPage",
    "mainEntity": {
      "@type": "FoodEstablishment",
      "name": "Vite & Gourmand",
      "description": "<?= $currentLang === 'en' ? 'Since 1999, Vite & Gourmand has been accompanying your most precious events with passion and professionalism. Based in Bordeaux, we offer refined cuisine combining artisan tradition and gastronomic innovation.' : 'Depuis 1999, Vite & Gourmand accompagne vos événements les plus précieux avec passion et professionnalisme. Basés à Bordeaux, nous vous proposons une cuisine raffinée qui marie tradition artisanale et innovation gastronomique.' ?>",
      "foundingDate": "1999",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Bordeaux",
        "addressCountry": "FR"
      },
      "servesCuisine": ["Française", "Bio", "Locale"],
      "priceRange": "€€€"
    }
  }
</script>

<section class="about" role="region" aria-labelledby="about-title" aria-describedby="about-desc" data-animate="fade-up">

  <p id="about-desc" class="sr-only">
    <?= $currentLang === 'en' ? 'Discover our story, our values and our commitment to exceptional local cuisine since 1999' : 'Découvrez notre histoire, nos valeurs et notre engagement pour une cuisine locale d\'exception depuis 1999' ?>
  </p>

  <div class="container about__inner">

    <div class="about__content">
      <h2 id="about-title" class="about__title">
        <?= $currentLang === 'en' ? 'Exceptional Cuisine,<br>Tailored Service' : 'Une Cuisine d\'Exception,<br>un Service sur Mesure' ?>
      </h2>

      <p class="about__text">
        <?= $currentLang === 'en' ? 'Since 1999, Vite &amp; Gourmand has been accompanying your most precious events with passion and professionalism. Based in Bordeaux, we are proud to offer cuisine that combines artisan tradition and gastronomic innovation.' : 'Depuis 1999, Vite &amp; Gourmand accompagne vos événements les plus précieux avec passion et professionnalisme. Basés à Bordeaux, nous sommes fiers de vous proposer une cuisine qui marie tradition artisanale et innovation gastronomique.' ?>
      </p>

      <p class="about__text">
        <?= $currentLang === 'en' ? 'Our commitment: exceptional local products, a recognized artisan expertise and a tailor-made service to make every meal an unforgettable moment.' : 'Notre engagement : des produits locaux d\'exception, un savoir-faire reconnu et un service sur mesure pour transformer chaque repas en un moment inoubliable.' ?>
      </p>

      <a href="/catalogue/" class="btn btn--forest about__cta" aria-label="<?= $currentLang === 'en' ? 'Discover all our gastronomic menus and catering services' : 'Découvrir tous nos menus gastronomiques et services traiteur' ?>">
        <?= $currentLang === 'en' ? 'Discover our menus' : 'Découvrir nos menus' ?>
        <span aria-hidden="true">→</span>
      </a>
    </div>

    <div class="about__gallery" role="img" aria-label="<?= $currentLang === 'en' ? 'Gallery of our culinary creations' : 'Galerie de nos créations culinaires' ?>">
      <div class="about__gallery-grid">

        <div class="about__gallery-col about__gallery-col--left">
          <img
            src="/assets/img/about/about-chef-dresse-assiette.webp"
            alt="<?= $currentLang === 'en' ? 'Chef artisan of Vite & Gourmand carefully preparing a refined gastronomic dish with fresh local products in Bordeaux' : 'Chef artisan de Vite & Gourmand dressant avec soin une assiette gastronomique raffinée avec des produits locaux frais à Bordeaux' ?>"
            title="<?= $currentLang === 'en' ? 'Artisan craftsmanship' : 'Savoir-faire artisanal' ?>"
            class="about__gallery-img about__gallery-img--tall"
            width="540" height="360"
            loading="lazy"
            decoding="async">
        </div>

        <div class="about__gallery-col about__gallery-col--right">
          <img
            src="/assets/img/about/about-vaisselle.webp"
            alt="<?= $currentLang === 'en' ? 'Elegant table setting and refined dishware showcasing the premium service of Vite & Gourmand catering' : 'Vaisselle élégante et dressage de table raffiné illustrant le service premium du traiteur Vite & Gourmand' ?>"
            title="<?= $currentLang === 'en' ? 'Refined table setting' : 'Dressage de table raffiné' ?>"
            class="about__gallery-img"
            width="540" height="172"
            loading="lazy"
            decoding="async">

          <img
            src="/assets/img/about/about-chef-preparation.webp"
            alt="<?= $currentLang === 'en' ? 'Fresh seasonal vegetables from local Bordeaux market, highlighting our commitment to local sourcing and organic farming' : 'Légumes frais de saison du marché local de Bordeaux, illustrant notre engagement pour l\'approvisionnement local et l\'agriculture biologique' ?>"
            title="<?= $currentLang === 'en' ? 'Local fresh products' : 'Produits frais locaux' ?>"
            class="about__gallery-img"
            width="540" height="172"
            loading="lazy"
            decoding="async">
        </div>

      </div>
    </div>

  </div>
</section>