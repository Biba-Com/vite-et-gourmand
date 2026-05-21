<?php

/**
 * Section: Bandeau Anti-Gaspillage
 * Path: src/views/components/anti-gaspi.php
 * Bande d'accroche entre le hero et la section about
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';
?>

<section class="anti-gaspi" aria-labelledby="anti-gaspi-title" data-animate="fade-up">
    <div class="container anti-gaspi__inner">

        <div class="anti-gaspi__left">
            <!-- Icône badge vert -->
            <div class="anti-gaspi__icon" aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2z" />
                    <path d="M8 12l2.5 2.5L16 9" />
                </svg>
            </div>

            <div class="anti-gaspi__text">
                <p id="anti-gaspi-title" class="anti-gaspi__title">
                    <?= $currentLang === 'en' ? 'Anti-Waste Offers' : 'Offres Anti-Gaspillage' ?>
                </p>
                <p class="anti-gaspi__subtitle">
                    <?= $currentLang === 'en'
                        ? 'Up to -50% on our daily anti-waste selection.'
                        : 'Jusqu\'à -50% sur notre sélection du jour.' ?>
                </p>
            </div>
        </div>

       <a href="/catalogue/" class="btn btn--primary anti-gaspi__cta">
            <?= $currentLang === 'en' ? 'View offers' : 'Voir les offres' ?>
        </a>

    </div>
</section>