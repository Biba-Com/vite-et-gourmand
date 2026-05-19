<?php

/**
 * Vue : Erreur 404
 * Chemin : src/views/errors/404.php
 */

$isEn = ($currentLang ?? 'fr') === 'en';
?>

<section class="error-page" aria-labelledby="error-title">
    <div class="container">
        <div class="error-page__content">

            <div class="error-page__code" aria-hidden="true">404</div>

            <h1 class="error-page__title" id="error-title">
                <?= $isEn ? 'Page not found' : 'Page introuvable' ?>
            </h1>

            <p class="error-page__message">
                <?= $isEn
                    ? 'The menu or page you are looking for does not exist or has been moved.'
                    : 'Le menu ou la page que vous cherchez n\'existe pas ou a été déplacé.' ?>
            </p>

            <div class="error-page__actions">
                <a href="/catalogue/" class="btn btn--primary">
                    <?= $isEn ? 'Browse our menus' : 'Voir nos menus' ?>
                </a>
                <a href="/" class="btn btn--ghost">
                    <?= $isEn ? 'Back to home' : 'Retour à l\'accueil' ?>
                </a>
            </div>

        </div>
    </div>
</section>