<?php

/**
 * Vue : Détail d'un menu
 * Chemin : src/views/catalogue/detail.php
 *
 * Variables disponibles (injectées par le point d'entrée) :
 *  - $menu              array   Données du menu (titre, slug, prix...)
 *  - $composition       array   Plats du menu
 *  - $allergenes        array   Allergènes du menu
 *  - $platsParCategorie array   Plats groupés par catégorie
 *  - $isEn              bool    Langue active
 */

$isEn              = $isEn              ?? false;
$menu              = $menu              ?? [];
$allergenes        = $allergenes        ?? [];
$platsParCategorie = $platsParCategorie ?? [];

// Labels traduits par catégorie
$labelsCategorie = [
    'starter' => $isEn ? '🥗 Starters'       : '🥗 Entrées',
    'main'    => $isEn ? '🍽️ Main courses'   : '🍽️ Plats principaux',
    'dessert' => $isEn ? '🍰 Desserts'        : '🍰 Desserts',
    'drink'   => $isEn ? '☕ Drinks'          : '☕ Boissons',
    'other'   => $isEn ? '🧀 Other'           : '🧀 Autres',
];
?>

<!-- ── Fil d'Ariane ─────────────────────────────────────────── -->
<nav class="breadcrumb" aria-label="<?= $isEn ? 'Breadcrumb' : 'Fil d\'ariane' ?>">
    <div class="container">
        <ol class="breadcrumb__list">
            <li class="breadcrumb__item">
                <a href="/" class="breadcrumb__link">
                    <?= $isEn ? 'Home' : 'Accueil' ?>
                </a>
            </li>
            <li class="breadcrumb__item" aria-hidden="true">›</li>
            <li class="breadcrumb__item">
                <a href="/catalogue/" class="breadcrumb__link">
                    <?= $isEn ? 'Menus' : 'Catalogue' ?>
                </a>
            </li>
            <li class="breadcrumb__item" aria-hidden="true">›</li>
            <li class="breadcrumb__item breadcrumb__item--active" aria-current="page">
                <?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>
            </li>
        </ol>
    </div>
</nav>

<!-- ── Hero du menu ───────────────────────────────────────────── -->
<section class="menu-detail-hero">
    <div class="container">

        <div class="menu-detail-hero__inner">

            <!-- Image -->
            <div class="menu-detail-hero__image-wrapper">
                <?php if (!empty($menu['image_url'])): ?>
                    <img
                        class="menu-detail-hero__image"
                        src="<?= htmlspecialchars($menu['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>"
                        width="600"
                        height="400">
                <?php else: ?>
                    <div class="menu-detail-hero__image menu-detail-hero__image--placeholder" aria-hidden="true">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M3 11l19-9-9 19-2-8-8-2z" />
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Infos principales -->
            <div class="menu-detail-hero__content">

                <!-- Thème -->
                <?php if (!empty($menu['theme_nom'])): ?>
                    <span
                        class="menu-detail__theme"
                        style="--theme-color: <?= htmlspecialchars($menu['theme_couleur'] ?? '#D4AF37', ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($menu['theme_nom'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>

                <!-- Titre -->
                <h1 class="menu-detail__title">
                    <?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <!-- Description -->
                <?php if (!empty($menu['description'])): ?>
                    <p class="menu-detail__description">
                        <?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <!-- Prix et capacité -->
                <div class="menu-detail__pricing">
                    <div class="menu-detail__price">
                        <span class="menu-detail__price-amount">
                            <?= number_format((float) $menu['prix_par_personne'], 2, ',', ' ') ?> €
                        </span>
                        <span class="menu-detail__price-unit">
                            / <?= $isEn ? 'person (excl. tax)' : 'personne HT' ?>
                        </span>
                    </div>

                    <div class="menu-detail__capacity">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <?= (int) $menu['nb_personnes_min'] ?>
                        <?php if (!empty($menu['nb_personnes_max'])): ?>
                            – <?= (int) $menu['nb_personnes_max'] ?>
                        <?php else: ?>
                            +
                        <?php endif; ?>
                        <?= $isEn ? 'guests' : 'personnes' ?>
                    </div>
                </div>

                <!-- Badges éco -->
                <?php if (!empty($menu['badges_eco'])): ?>
                    <div class="menu-detail__badges" aria-label="<?= $isEn ? 'Eco badges' : 'Badges éco-responsables' ?>">
                        <?php foreach (explode(' | ', $menu['badges_eco']) as $badge): ?>
                            <span class="badge badge--eco">
                                <?= htmlspecialchars(trim($badge), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Régimes alimentaires -->
                <?php if (!empty($menu['regimes'])): ?>
                    <div class="menu-detail__regimes">
                        <span class="menu-detail__regimes-label">
                            <?= $isEn ? 'Dietary options:' : 'Régimes:' ?>
                        </span>
                        <span class="menu-detail__regimes-value">
                            <?= htmlspecialchars($menu['regimes'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- CTA Devis -->
                <div class="menu-detail__actions">
                    <a href="/contact/?menu=<?= htmlspecialchars($menu['slug'], ENT_QUOTES, 'UTF-8') ?>"
                        class="btn btn--primary btn--lg">
                        <?= $isEn ? 'Request a quote' : 'Demander un devis' ?>
                    </a>
                    <a href="/catalogue/" class="btn btn--ghost btn--lg">
                        <?= $isEn ? '← Back to catalogue' : '← Retour au catalogue' ?>
                    </a>
                </div>

            </div><!-- /.menu-detail-hero__content -->

        </div><!-- /.menu-detail-hero__inner -->

    </div>
</section>

<!-- ── Composition du menu ────────────────────────────────────── -->
<section class="menu-composition" aria-labelledby="composition-title">
    <div class="container">

        <h2 class="section-title" id="composition-title">
            <?= $isEn ? 'Menu Composition' : 'Composition du menu' ?>
        </h2>

        <?php if (empty($platsParCategorie)): ?>
            <p class="menu-composition__empty">
                <?= $isEn ? 'Composition available on request.' : 'Composition disponible sur demande.' ?>
            </p>
        <?php else: ?>
            <div class="menu-composition__categories">

                <?php foreach ($platsParCategorie as $categorie => $plats): ?>
                    <div class="menu-composition__category">

                        <h3 class="menu-composition__category-title">
                            <?= $labelsCategorie[$categorie] ?? htmlspecialchars($categorie, ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <ul class="menu-composition__plats" role="list">
                            <?php foreach ($plats as $plat): ?>
                                <li class="composition-plat">
                                    <?php if (!empty($plat['image_url'])): ?>
                                        <img
                                            class="composition-plat__image"
                                            src="<?= htmlspecialchars($plat['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($plat['nom'], ENT_QUOTES, 'UTF-8') ?>"
                                            width="80"
                                            height="80"
                                            loading="lazy">
                                    <?php endif; ?>
                                    <div class="composition-plat__info">
                                        <strong class="composition-plat__nom">
                                            <?= htmlspecialchars($plat['nom'], ENT_QUOTES, 'UTF-8') ?>
                                        </strong>
                                        <?php if (!empty($plat['description'])): ?>
                                            <span class="composition-plat__desc">
                                                <?= htmlspecialchars($plat['description'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                    </div>
                <?php endforeach; ?>

            </div><!-- /.menu-composition__categories -->
        <?php endif; ?>

    </div>
</section>

<!-- ── Allergènes ─────────────────────────────────────────────── -->
<section class="menu-allergenes" aria-labelledby="allergenes-title">
    <div class="container">

        <h2 class="section-title" id="allergenes-title">
            <?= $isEn ? 'Allergen Information' : 'Informations allergènes' ?>
        </h2>

        <!-- Mention légale obligatoire -->
        <p class="menu-allergenes__legal">
            <?= $isEn
                ? 'In accordance with EU Regulation No. 1169/2011. Contact us for any specific dietary requirement.'
                : 'Conformément au règlement UE n°1169/2011. Contactez-nous pour tout besoin alimentaire spécifique.' ?>
        </p>

        <?php if (empty($allergenes)): ?>
            <p class="menu-allergenes__none">
                <?= $isEn ? 'No major allergens detected.' : 'Aucun allergène majeur détecté.' ?>
            </p>
        <?php else: ?>
            <ul class="allergenes-list" role="list" aria-label="<?= $isEn ? 'Allergens present' : 'Allergènes présents' ?>">
                <?php foreach ($allergenes as $allergene): ?>
                    <li class="allergene-item">
                        <span class="allergene-item__icone" aria-hidden="true">
                            <?= htmlspecialchars($allergene['icone'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <span class="allergene-item__nom">
                            <?= htmlspecialchars($allergene['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>
</section>

<!-- ── CTA final ─────────────────────────────────────────────── -->
<section class="menu-detail-cta">
    <div class="container">
        <h2 class="menu-detail-cta__title">
            <?= $isEn
                ? 'Interested in this menu?'
                : 'Ce menu vous intéresse ?' ?>
        </h2>
        <p class="menu-detail-cta__text">
            <?= $isEn
                ? 'Our team will get back to you within 24 hours.'
                : 'Notre équipe vous répond sous 24h.' ?>
        </p>
        <a
            href="/contact/?menu=<?= htmlspecialchars($menu['slug'], ENT_QUOTES, 'UTF-8') ?>"
            class="btn btn--primary btn--lg">
            <?= $isEn ? 'Request a free quote' : 'Demander un devis gratuit' ?>
        </a>
    </div>
</section>