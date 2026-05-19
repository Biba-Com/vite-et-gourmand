<?php

/**
 * Section: Avis de nos Clients
 * Path: src/views/components/testimonials.php
 * 3 témoignages + note globale — Schema.org Review pour SEO
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';

$testimonials = [
    [
        'stars'   => 5,
        'quote_fr' => 'Prestation exceptionnelle pour notre mariage ! Les plats étaient délicieux et le service irréprochable. Nous recommandons vivement Vite &amp; Gourmand.',
        'quote_en' => 'Exceptional service for our wedding! The dishes were delicious and the service impeccable. We highly recommend Vite &amp; Gourmand.',
        'name'    => 'Marie Dubois',
        'context_fr' => 'Mariée (Juillet 2023)',
        'context_en' => 'Bride (July 2023)',
        'initials' => 'MD',
        'color'   => '#800020',
        'date'    => '2023-07-15',
    ],
    [
        'stars'   => 5,
        'quote_fr' => 'Le buffet corporatif était parfait pour notre séminaire. Présentation soignée, produits frais, équipe ponctuelle. Nos collaborateurs étaient ravis.',
        'quote_en' => 'The corporate buffet was perfect for our seminar. Careful presentation, fresh products, punctual team. Our colleagues were delighted.',
        'name'    => 'Pierre Martin',
        'context_fr' => 'Comité d\'entreprise (Sept 2023)',
        'context_en' => 'Works Council (Sept 2023)',
        'initials' => 'PM',
        'color'   => '#063A1F',
        'date'    => '2023-09-20',
    ],
    [
        'stars'   => 5,
        'quote_fr' => 'Service professionnel et cuisine raffinée. Le chef a su créer une expérience gastronomique inoubliable pour l\'anniversaire de ma mère.',
        'quote_en' => 'Professional service and refined cuisine. The chef created an unforgettable gastronomic experience for my mother\'s birthday.',
        'name'    => 'Sophie Laurent',
        'context_fr' => 'Anniversaire (Février 2024)',
        'context_en' => 'Birthday (February 2024)',
        'initials' => 'SL',
        'color'   => '#D4AF37',
        'date'    => '2024-02-10',
    ],
];

$globalRating = 4.9;
$reviewCount  = 127;
?>

<!-- Schema.org AggregateRating — SEO Rich Snippet Google -->
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Vite & Gourmand",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "<?= $globalRating ?>",
            "reviewCount": "<?= $reviewCount ?>",
            "bestRating": "5",
            "worstRating": "1"
        }
    }
</script>

<section class="testimonials" aria-labelledby="testimonials-title" data-animate="fade-up">
    <div class="container">

        <div class="section-header section-header--center">
            <h2 id="testimonials-title" class="section-header__title">
                <?= $currentLang === 'en' ? 'Client Reviews' : 'Avis de nos Clients' ?>
            </h2>
            <p class="section-header__subtitle">
                <?= $currentLang === 'en'
                    ? 'Discover the testimonials of those who trusted us.'
                    : 'Découvrez les témoignages de ceux qui nous ont fait confiance.' ?>
            </p>
        </div>

        <!-- 3 cartes témoignages -->
        <div class="testimonials__grid">
            <?php foreach ($testimonials as $t) : ?>
                <article class="testimonial-card" itemscope itemtype="https://schema.org/Review" data-animate="card-up">
                    <!-- Étoiles -->
                    <div class="testimonial-card__stars" aria-label="<?= $t['stars'] ?> étoiles sur 5">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <svg width="16" height="16" viewBox="0 0 24 24"
                                fill="<?= $i <= $t['stars'] ? '#D4AF37' : 'none' ?>"
                                stroke="#D4AF37" stroke-width="1.5"
                                aria-hidden="true">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        <?php endfor; ?>
                    </div>

                    <!-- Citation -->
                    <blockquote class="testimonial-card__quote" itemprop="reviewBody">
                        "<?= $currentLang === 'en' ? $t['quote_en'] : $t['quote_fr'] ?>"
                    </blockquote>

                    <!-- Auteur -->
                    <footer class="testimonial-card__footer">
                        <div class="testimonial-card__avatar"
                            style="background-color: <?= $t['color'] ?>;"
                            aria-hidden="true">
                            <?= $t['initials'] ?>
                        </div>
                        <div class="testimonial-card__author">
                            <span class="testimonial-card__name" itemprop="author"><?= $t['name'] ?></span>
                            <span class="testimonial-card__context">
                                <?= $currentLang === 'en' ? $t['context_en'] : $t['context_fr'] ?>
                            </span>
                        </div>
                    </footer>

                    <!-- Hidden meta pour Schema.org -->
                    <meta itemprop="datePublished" content="<?= $t['date'] ?>">
                    <meta itemprop="ratingValue" content="<?= $t['stars'] ?>">
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Note globale -->
        <div class="testimonials__global" aria-label="<?= $currentLang === 'en' ? "Global rating: $globalRating out of 5 based on $reviewCount certified reviews" : "Note globale : $globalRating sur 5 basée sur $reviewCount avis certifiés" ?>">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37" stroke="#D4AF37" stroke-width="1" aria-hidden="true">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
            </svg>
            <strong class="testimonials__score"><?= $globalRating ?>/5</strong>
            <span class="testimonials__count">
                (<?= $reviewCount ?> <?= $currentLang === 'en' ? 'certified reviews' : 'avis certifiés' ?>)
            </span>
        </div>

    </div>
</section>