<?php

/**
 * Vue : Page Nos Prestations
 * Chemin : src/views/pages/prestations.php
 */

$isEn = $isEn ?? false;
?>

<!-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ -->
<section class="prestations-hero">
    <div class="container">
        <h1 class="prestations-hero__title">
            <?= $isEn ? 'Our Services' : 'Nos Prestations' ?>
        </h1>
        <p class="prestations-hero__subtitle">
            <?= $isEn
                ? 'Personalized catering services for all your events, from intimate dinners to grand receptions.'
                : 'Des prestations traiteur sur mesure pour tous vos événements, des dîners intimistes aux grandes réceptions.' ?>
        </p>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     PRESTATIONS PRINCIPALES (grille 2×2)
══════════════════════════════════════════════════════════ -->
<section class="prestations-grid">
    <div class="container">

        <!-- Prestation 1 — Chef à Domicile -->
        <article class="prestation-card">
            <div class="prestation-card__icon" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/>
                    <line x1="6" y1="17" x2="18" y2="17"/>
                </svg>
            </div>
            <h2 class="prestation-card__title">
                <?= $isEn ? 'Private Chef at Home' : 'Chef à Domicile' ?>
            </h2>
            <p class="prestation-card__desc">
                <?= $isEn
                    ? 'A professional chef comes to your home to prepare a gourmet meal. Includes personalized menu, cooking, service, and cleanup.'
                    : 'Un chef professionnel se déplace chez vous pour préparer un repas gastronomique. Inclut menu personnalisé, cuisine, service et nettoyage.' ?>
            </p>
            <div class="prestation-card__price">
                <?= $isEn ? 'From' : 'À partir de' ?> <strong>80€</strong> <?= $isEn ? 'per person' : 'par personne' ?>
            </div>
            <ul class="prestation-card__conditions">
                <li><?= $isEn ? 'Minimum 4 guests' : 'Minimum 4 convives' ?></li>
                <li><?= $isEn ? 'Booking 48h in advance' : 'Réservation 48h à l\'avance' ?></li>
                <li><?= $isEn ? 'Within 30 km of Bordeaux' : 'Rayon 30 km autour de Bordeaux' ?></li>
            </ul>
            <a href="/contact/" class="btn btn--primary">
                <?= $isEn ? 'Request a quote' : 'Demander un devis' ?>
            </a>
        </article>

        <!-- Prestation 2 — Buffet -->
        <article class="prestation-card">
            <div class="prestation-card__icon" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h2 class="prestation-card__title">
                <?= $isEn ? 'Buffet Catering' : 'Buffet' ?>
            </h2>
            <p class="prestation-card__desc">
                <?= $isEn
                    ? 'An elegant and varied buffet for receptions and cocktail parties. Includes hot/cold buffet, refined presentation, tableware, and linens.'
                    : 'Un buffet élégant et varié pour vos réceptions et cocktails. Inclut buffet chaud/froid, présentation soignée, vaisselle et nappage.' ?>
            </p>
            <div class="prestation-card__price">
                <?= $isEn ? 'From' : 'À partir de' ?> <strong>45€</strong> <?= $isEn ? 'per person' : 'par personne' ?>
            </div>
            <ul class="prestation-card__conditions">
                <li><?= $isEn ? 'Minimum 10 guests' : 'Minimum 10 convives' ?></li>
                <li><?= $isEn ? 'Hot and cold options' : 'Options chaud et froid' ?></li>
                <li><?= $isEn ? 'Setup and breakdown included' : 'Installation et débarrassage inclus' ?></li>
            </ul>
            <a href="/contact/" class="btn btn--primary">
                <?= $isEn ? 'Request a quote' : 'Demander un devis' ?>
            </a>
        </article>

        <!-- Prestation 3 — Serveurs à Domicile -->
        <article class="prestation-card">
            <div class="prestation-card__icon" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <h2 class="prestation-card__title">
                <?= $isEn ? 'Waitstaff at Home' : 'Serveurs à Domicile' ?>
            </h2>
            <p class="prestation-card__desc">
                <?= $isEn
                    ? 'Professional staff to ensure service at your private receptions. Professional attire, table or buffet service, and cleanup.'
                    : 'Personnel professionnel pour assurer le service lors de vos réceptions privées. Tenue professionnelle, service à table ou buffet, débarrassage.' ?>
            </p>
            <div class="prestation-card__price">
                <strong>15€</strong> <?= $isEn ? 'per hour per server' : 'par heure / serveur' ?>
            </div>
            <ul class="prestation-card__conditions">
                <li><?= $isEn ? 'Minimum 4 hours' : 'Minimum 4 heures' ?></li>
                <li><?= $isEn ? 'Professional uniform provided' : 'Tenue professionnelle fournie' ?></li>
                <li><?= $isEn ? 'Available evenings and weekends' : 'Disponible soirs et week-ends' ?></li>
            </ul>
            <a href="/contact/" class="btn btn--primary">
                <?= $isEn ? 'Request a quote' : 'Demander un devis' ?>
            </a>
        </article>

        <!-- Prestation 4 — Restauration sur Place -->
        <article class="prestation-card">
            <div class="prestation-card__icon" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <h2 class="prestation-card__title">
                <?= $isEn ? 'On-Site Catering' : 'Restauration sur Place' ?>
            </h2>
            <p class="prestation-card__desc">
                <?= $isEn
                    ? 'Full service at our premises or your chosen venue. Chef, complete facilities, and professional team for your events.'
                    : 'Service complet dans nos locaux ou un lieu de votre choix. Chef cuisinier, installations complètes et équipe professionnelle pour vos événements.' ?>
            </p>
            <div class="prestation-card__price">
                <?= $isEn ? 'Custom quote' : 'Sur devis' ?>
            </div>
            <ul class="prestation-card__conditions">
                <li><?= $isEn ? 'Minimum 20 guests' : 'Minimum 20 convives' ?></li>
                <li><?= $isEn ? 'Custom setup' : 'Installation sur mesure' ?></li>
                <li><?= $isEn ? 'Full coordination' : 'Coordination complète' ?></li>
            </ul>
            <a href="/contact/" class="btn btn--primary">
                <?= $isEn ? 'Request a quote' : 'Demander un devis' ?>
            </a>
        </article>

    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     LOCATION DE LOCAL (bandeau vert)
══════════════════════════════════════════════════════════ -->
<section class="location-banner">
    <div class="container">
        <div class="location-banner__content">
            <div class="location-banner__icon" aria-hidden="true">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="3" x2="9" y2="21"/>
                </svg>
            </div>
            <div class="location-banner__text">
                <h3 class="location-banner__title">
                    <?= $isEn ? 'Venue Rental' : 'Location de Local' ?>
                </h3>
                <p class="location-banner__desc">
                    <?= $isEn
                        ? 'A welcoming space for your events, with the option of full catering services. Capacity up to 80 guests.'
                        : 'Un espace chaleureux pour vos événements, avec possibilité de prestations traiteur complètes. Capacité jusqu\'à 80 personnes.' ?>
                </p>
            </div>
            <a href="/contact/" class="btn btn--ghost-white">
                <?= $isEn ? 'Learn more' : 'En savoir plus' ?>
            </a>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     OPTIONS COMPLÉMENTAIRES
══════════════════════════════════════════════════════════ -->
<section class="options-section">
    <div class="container">

        <h2 class="options-section__title">
            <?= $isEn ? 'Additional Options' : 'Options Complémentaires' ?>
        </h2>

        <div class="options-grid">

            <!-- Option 1 — Vaisselle -->
            <div class="option-card">
                <div class="option-card__icon" aria-hidden="true">
                    🍽️
                </div>
                <h3 class="option-card__title">
                    <?= $isEn ? 'Tableware & Glassware' : 'Vaisselle & Verrerie' ?>
                </h3>
                <p class="option-card__desc">
                    <?= $isEn
                        ? 'Premium porcelain tableware, crystal glassware, and elegant cutlery.'
                        : 'Vaisselle en porcelaine haut de gamme, verrerie en cristal et couverts élégants.' ?>
                </p>
                <div class="option-card__price">
                    <strong>+15€</strong> <?= $isEn ? 'per person' : 'par personne' ?>
                </div>
            </div>

            <!-- Option 2 — Matériel -->
            <div class="option-card">
                <div class="option-card__icon" aria-hidden="true">
                    🎪
                </div>
                <h3 class="option-card__title">
                    <?= $isEn ? 'Equipment & Furniture' : 'Matériel & Mobilier' ?>
                </h3>
                <p class="option-card__desc">
                    <?= $isEn
                        ? 'Tables, chairs, linens, lighting, and decorative elements for your event.'
                        : 'Tables, chaises, nappage, éclairage et éléments de décoration pour votre événement.' ?>
                </p>
                <div class="option-card__price">
                    <?= $isEn ? 'Custom quote' : 'Sur devis' ?>
                </div>
            </div>

            <!-- Option 3 — Livraison -->
            <div class="option-card">
                <div class="option-card__icon" aria-hidden="true">
                    🚚
                </div>
                <h3 class="option-card__title">
                    <?= $isEn ? 'Delivery' : 'Livraison' ?>
                </h3>
                <p class="option-card__desc">
                    <?= $isEn
                        ? 'Free delivery within 30 km of Bordeaux. €0.59/km beyond.'
                        : 'Livraison gratuite dans un rayon de 30 km autour de Bordeaux. Au-delà : 0,59€/km.' ?>
                </p>
                <div class="option-card__price">
                    <?= $isEn ? 'Free < 30 km' : 'Gratuit < 30 km' ?>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     CTA FINAL
══════════════════════════════════════════════════════════ -->
<section class="prestations-cta">
    <div class="container">
        <h2 class="prestations-cta__title">
            <?= $isEn ? 'Ready to plan your event?' : 'Prêt à organiser votre événement ?' ?>
        </h2>
        <p class="prestations-cta__text">
            <?= $isEn
                ? 'Contact us to discuss your needs and receive a personalized quote.'
                : 'Contactez-nous pour discuter de vos besoins et recevoir un devis personnalisé.' ?>
        </p>
        <div class="prestations-cta__actions">
            <a href="/contact/" class="btn btn--primary btn--lg">
                <?= $isEn ? 'Contact us' : 'Nous contacter' ?>
            </a>
            <a href="/catalogue/" class="btn btn--ghost btn--lg">
                <?= $isEn ? 'View our menus' : 'Voir nos menus' ?>
            </a>
        </div>
    </div>
</section>
