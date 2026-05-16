<?php

/**
 * Section: Nos Engagements (homepage)
 * Path: src/views/components/engagements-home.php
 * 3 cartes image avec overlay badge + texte
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';

$engagements = [
    [
        'image'   => '/assets/img/engagements/engagement-circuit-court.webp',
        'alt_fr'  => 'Producteur local livrant des légumes frais de saison à Bordeaux',
        'alt_en'  => 'Local producer delivering fresh seasonal vegetables in Bordeaux',
        'badge'   => 'CIRCUIT COURT',
        'title_fr' => 'Approvisionnement Local',
        'title_en' => 'Local Sourcing',
        'desc_fr' => 'Produits frais de Bordeaux &amp; alentour.',
        'desc_en' => 'Fresh products from Bordeaux &amp; surroundings.',
    ],
    [
        'image'   => '/assets/img/engagements/engagement-savoir-faire.webp',
        'alt_fr'  => 'Chef artisan préparant un plat avec soin et expertise',
        'alt_en'  => 'Artisan chef carefully preparing a dish with expertise',
        'badge'   => 'TOP CHEFS',
        'title_fr' => 'Savoir-Faire Artisanal',
        'title_en' => 'Artisan Expertise',
        'desc_fr' => '25 ans d\'excellence culinaire.',
        'desc_en' => '25 years of culinary excellence.',
    ],
    [
        'image'   => '/assets/img/engagements/engagement-certification-bio.webp',
        'alt_fr'  => 'Plantes aromatiques biologiques cultivées localement pour nos préparations',
        'alt_en'  => 'Locally grown organic aromatic plants for our preparations',
        'badge'   => 'LABEL BIO',
        'title_fr' => 'Certifié Bio',
        'title_en' => 'Organic Certified',
        'desc_fr' => 'Partenariat avec Bio Aïl &amp; Fenouil.',
        'desc_en' => 'Partnership with Bio Aïl &amp; Fenouil.',
    ],
];
?>

<section class="engagements-home" aria-labelledby="engagements-home-title" data-animate="fade-up">
    <div class="container">

        <div class="section-header section-header--center">
            <h2 id="engagements-home-title" class="section-header__title">
                <?= $currentLang === 'en' ? 'Our Commitments' : 'Nos Engagements' ?>
            </h2>
            <p class="section-header__subtitle">
                <?= $currentLang === 'en'
                    ? 'The values that drive our cuisine every day and guarantee the excellence of our services.'
                    : 'Les valeurs qui animent notre cuisine au quotidien et garantissent l\'excellence de nos prestations.' ?>
            </p>
        </div>

        <div class="engagements-home__grid">
            <?php foreach ($engagements as $eng) : ?>
                <article class="engagement-card" data-animate="card-up">
                    <img
                        src="<?= $eng['image'] ?>"
                        alt="<?= $currentLang === 'en' ? $eng['alt_en'] : $eng['alt_fr'] ?>"
                        class="engagement-card__img"
                        width="400" height="320"
                        loading="lazy"
                        decoding="async">

                    <?php if (isset($eng['badge']) && $eng['badge']) : ?>
                        <span class="engagement-card__badge"><?= $eng['badge'] ?></span>
                    <?php endif; ?>

                    <div class="engagement-card__content">
                        <h3 class="engagement-card__title">
                            <?= $currentLang === 'en' ? $eng['title_en'] : $eng['title_fr'] ?>
                        </h3>
                        <p class="engagement-card__desc">
                            <?= $currentLang === 'en' ? $eng['desc_en'] : $eng['desc_fr'] ?>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="engagements-home__footer">
            <a href="/engagements" class="engagements-home__link">
                <?= $currentLang === 'en' ? 'Discover all our partners' : 'Découvrir tous nos partenaires' ?>
            </a>
        </div>

    </div>
</section>