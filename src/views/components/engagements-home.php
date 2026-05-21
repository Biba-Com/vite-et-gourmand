<?php

/**
 * Section: Nos Engagements — Teaser Home
 * Path: src/views/components/engagements-home.php
 *
 * Affiche un résumé des 3 engagements RSE depuis la BDD
 * CTA → renvoie vers /engagements/
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';

$engagements = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        SELECT titre, contenu
        FROM contenu_rse
        ORDER BY ordre_affichage ASC
    ");
    $stmt->execute();
    $engagements = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Engagements home: ' . $e->getMessage());
}

$engagementIcons = [
    '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>',
    '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12a9 9 0 1 1-9-9"/><path d="M21 3v6h-6"/><path d="M3 12a9 9 0 0 1 9-9"/></svg>',
    '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
];
?>

<?php if (!empty($engagements)): ?>

<section class="engagements-home" aria-labelledby="engagements-title" data-animate="fade-up">
    <div class="container">

        <div class="section-header section-header--center">
            <h2 id="engagements-title" class="section-header__title">
                <?= $currentLang === 'en' ? 'Our Commitments' : 'Nos Engagements' ?>
            </h2>
            <p class="section-header__subtitle">
                <?= $currentLang === 'en'
                    ? 'A responsible gastronomy, rooted in our Bordeaux terroir.'
                    : 'Une gastronomie responsable, ancrée dans notre terroir bordelais.' ?>
            </p>
        </div>

        <div class="engagements-home__grid">
            <?php foreach ($engagements as $i => $eng): ?>
                <article class="engagement-card" data-animate="card-up">
                    <div class="engagement-card__icon" aria-hidden="true">
                        <?= $engagementIcons[$i] ?? $engagementIcons[0] ?>
                    </div>
                    <h3 class="engagement-card__title">
                        <?= htmlspecialchars($eng['titre'], ENT_QUOTES, 'UTF-8') ?>
                    </h3>
                    <p class="engagement-card__text">
                        <?= htmlspecialchars($eng['contenu'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- CTA vers la page complète -->
        <div style="text-align:center;margin-top:var(--space-xl);">
            <a href="/engagements/" class="btn btn--forest">
                <?= $currentLang === 'en' ? 'See our commitments →' : 'Voir nos engagements →' ?>
            </a>
        </div>

    </div>
</section>

<?php endif; ?>
