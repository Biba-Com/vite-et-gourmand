<?php

/**
 * Vue : Catalogue — Liste des menus (v1.2.0)
 * Chemin : src/views/catalogue/index.php
 *
 * Variables disponibles :
 *  - $menus            array  Liste des menus actifs
 *  - $themes           array  Thèmes pour filtre
 *  - $regimes          array  Régimes alimentaires pour filtre
 *  - $allergenesList   array  Allergènes pour exclusion
 *  - $filtres          array  Filtres actifs (pre-fill)
 *  - $isEn             bool   Langue active
 */

$isEn            = $isEn            ?? false;
$menus           = $menus           ?? [];
$themes          = $themes          ?? [];
$regimes         = $regimes         ?? [];
$allergenesList  = $allergenesList  ?? [];
$filtres         = $filtres         ?? [];

// Le panneau filtres est ouvert si l'utilisateur a déjà filtré
$filtersOpen = !empty($filtres) && (
    !empty($filtres['id_theme']) ||
    !empty($filtres['id_regime']) ||
    !empty($filtres['prix_max']) ||
    !empty($filtres['nb_personnes']) ||
    !empty($filtres['allergenes_exclus'])
);
$allergenesExclusActifs = $filtres['allergenes_exclus'] ?? [];
?>

<!-- ══════════════════════════════════════════════════════════
     HERO COMPACT
══════════════════════════════════════════════════════════ -->
<section class="catalogue-hero">
    <div class="container">
        <h1 class="catalogue-hero__title">
            <?= $isEn ? 'Our Gourmet Menus' : 'Nos Menus Traiteur' ?>
        </h1>
        <p class="catalogue-hero__subtitle">
            <?= $isEn
                ? 'Tailored for your events — weddings, corporate, cocktails and more.'
                : 'Sur mesure pour vos événements — mariages, entreprises, cocktails et bien plus.' ?>
        </p>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     CATALOGUE — Recherche + Filtres + Grille
══════════════════════════════════════════════════════════ -->
<section class="catalogue" aria-label="<?= $isEn ? 'Menu catalogue' : 'Catalogue des menus' ?>">
    <div class="container">

        <!-- ── FORMULAIRE FILTRES ───────────────────────────── -->
        <form
            class="catalogue__filters-wrapper"
            method="GET"
            action=""
            role="search"
            aria-label="<?= $isEn ? 'Search and filter menus' : 'Rechercher et filtrer les menus' ?>">

            <!-- Ligne 1 : Recherche + Toggle filtres -->
            <div class="filter-bar">

                <!-- Barre de recherche -->
                <div class="filter-search">
                    <svg class="filter-search__icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <label for="search" class="visually-hidden">
                        <?= $isEn ? 'Search a menu' : 'Rechercher un menu' ?>
                    </label>
                    <input
                        type="search"
                        id="search"
                        name="search"
                        class="filter-search__input"
                        placeholder="<?= $isEn ? 'Search a menu...' : 'Rechercher un menu...' ?>"
                        value="<?= htmlspecialchars($filtres['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        maxlength="100"
                        autocomplete="off">
                </div>

                <!-- Bouton toggle filtres -->
                <button
                    type="button"
                    class="filter-toggle"
                    id="filterToggle"
                    aria-expanded="<?= $filtersOpen ? 'true' : 'false' ?>"
                    aria-controls="advancedFilters">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    <span><?= $isEn ? 'Advanced filters' : 'Filtres avancés' ?></span>
                </button>

                <button type="submit" class="filter-submit">
                    <?= $isEn ? 'Search' : 'Rechercher' ?>
                </button>
            </div>

            <!-- Ligne 2 : Filtres avancés (collapsible) -->
            <div
                class="filter-advanced <?= $filtersOpen ? 'filter-advanced--open' : '' ?>"
                id="advancedFilters"
                aria-hidden="<?= $filtersOpen ? 'false' : 'true' ?>">

                <div class="filter-advanced__grid">

                    <!-- Thème -->
                    <div class="filter-group">
                        <label class="filter-label" for="theme">
                            <?= $isEn ? 'Theme' : 'Thème' ?>
                        </label>
                        <select class="filter-select" id="theme" name="theme">
                            <option value=""><?= $isEn ? 'All themes' : 'Tous les thèmes' ?></option>
                            <?php foreach ($themes as $theme): ?>
                                <option
                                    value="<?= (int) $theme['id_theme'] ?>"
                                    <?= ($filtres['id_theme'] ?? 0) === (int) $theme['id_theme'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($theme['nom'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Régime -->
                    <div class="filter-group">
                        <label class="filter-label" for="regime">
                            <?= $isEn ? 'Dietary' : 'Régime alimentaire' ?>
                        </label>
                        <select class="filter-select" id="regime" name="regime">
                            <option value=""><?= $isEn ? 'All diets' : 'Tous régimes' ?></option>
                            <?php foreach ($regimes as $regime): ?>
                                <option
                                    value="<?= (int) $regime['id_regime'] ?>"
                                    <?= ($filtres['id_regime'] ?? 0) === (int) $regime['id_regime'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($regime['nom'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Prix max -->
                    <div class="filter-group">
                        <label class="filter-label" for="prix_max">
                            <?= $isEn ? 'Max price/person' : 'Prix max/personne' ?>
                        </label>
                        <select class="filter-select" id="prix_max" name="prix_max">
                            <option value=""><?= $isEn ? 'No limit' : 'Sans limite' ?></option>
                            <?php foreach ([20, 30, 50, 80, 100, 150] as $prix): ?>
                                <option
                                    value="<?= $prix ?>"
                                    <?= ($filtres['prix_max'] ?? 0) == $prix ? 'selected' : '' ?>>
                                    <?= $prix ?> €
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Nombre de personnes -->
                    <div class="filter-group">
                        <label class="filter-label" for="nb_personnes">
                            <?= $isEn ? 'Guests' : 'Nombre de personnes' ?>
                        </label>
                        <select class="filter-select" id="nb_personnes" name="nb_personnes">
                            <option value=""><?= $isEn ? 'Any' : 'Indifférent' ?></option>
                            <?php foreach ([10, 20, 30, 50, 100, 200] as $nb): ?>
                                <option
                                    value="<?= $nb ?>"
                                    <?= ($filtres['nb_personnes'] ?? 0) == $nb ? 'selected' : '' ?>>
                                    <?= $nb ?> <?= $isEn ? 'guests' : 'pers.' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tri -->
                    <div class="filter-group">
                        <label class="filter-label" for="ordre">
                            <?= $isEn ? 'Sort by' : 'Trier par' ?>
                        </label>
                        <select class="filter-select" id="ordre" name="ordre">
                            <option value="prix_asc"  <?= ($filtres['ordre'] ?? '') === 'prix_asc'  ? 'selected' : '' ?>>
                                <?= $isEn ? 'Price ↑' : 'Prix croissant' ?>
                            </option>
                            <option value="prix_desc" <?= ($filtres['ordre'] ?? '') === 'prix_desc' ? 'selected' : '' ?>>
                                <?= $isEn ? 'Price ↓' : 'Prix décroissant' ?>
                            </option>
                            <option value="titre"     <?= ($filtres['ordre'] ?? '') === 'titre'     ? 'selected' : '' ?>>
                                <?= $isEn ? 'Name (A-Z)' : 'Nom (A-Z)' ?>
                            </option>
                        </select>
                    </div>

                </div><!-- /.filter-advanced__grid -->

                <!-- Allergènes à exclure (pills) -->
                <div class="filter-allergenes">
                    <fieldset>
                        <legend class="filter-label">
                            <?= $isEn ? 'Exclude allergens' : 'Exclure les allergènes' ?>
                        </legend>
                        <div class="filter-allergenes__pills">
                            <?php foreach ($allergenesList as $allergene): ?>
                                <?php $idAllergene = (int) $allergene['id_allergene']; ?>
                                <?php $isChecked = in_array($idAllergene, $allergenesExclusActifs, true); ?>
                                <label class="pill <?= $isChecked ? 'pill--active' : '' ?>">
                                    <input
                                        type="checkbox"
                                        name="allergenes[]"
                                        value="<?= $idAllergene ?>"
                                        class="pill__input"
                                        <?= $isChecked ? 'checked' : '' ?>>
                                    <span class="pill__label">
                                        <?= htmlspecialchars($allergene['icone'], ENT_QUOTES, 'UTF-8') ?>
                                        <?= htmlspecialchars($allergene['nom'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                </div>

                <!-- Actions filtres -->
                <div class="filter-advanced__actions">
                    <a href="?" class="btn btn--ghost btn--sm">
                        <?= $isEn ? 'Reset all' : 'Réinitialiser' ?>
                    </a>
                </div>

            </div><!-- /.filter-advanced -->

        </form>

        <!-- ── COMPTEUR ────────────────────────────────────── -->
        <div class="catalogue__results-info" aria-live="polite">
            <?php if (empty($menus)): ?>
                <p class="catalogue__empty">
                    <?= $isEn
                        ? 'No menu matches your criteria. Try adjusting the filters.'
                        : 'Aucun menu ne correspond à vos critères. Essayez d\'ajuster les filtres.' ?>
                </p>
            <?php else: ?>
                <p class="catalogue__count">
                    <strong><?= count($menus) ?></strong>
                    <?= $isEn
                        ? (count($menus) > 1 ? ' menus found' : ' menu found')
                        : (count($menus) > 1 ? ' menus disponibles' : ' menu disponible') ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- ── GRILLE MENUS ────────────────────────────────── -->
        <?php if (!empty($menus)): ?>
        <div class="menus-grid" role="list">

            <?php foreach ($menus as $menu): ?>
            <article
                class="menu-card"
                role="listitem"
                aria-label="<?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>">

                <!-- Image + badge -->
                <a href="detail.php?slug=<?= htmlspecialchars($menu['slug'], ENT_QUOTES, 'UTF-8') ?>"
                   class="menu-card__image-link"
                   tabindex="-1"
                   aria-hidden="true">
                    <?php if (!empty($menu['image_url'])): ?>
                        <img
                            class="menu-card__image"
                            src="<?= htmlspecialchars($menu['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                            alt=""
                            loading="lazy"
                            width="400"
                            height="280">
                    <?php else: ?>
                        <div class="menu-card__image menu-card__image--placeholder" aria-hidden="true">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M3 11l19-9-9 19-2-8-8-2z"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </a>

                <!-- Corps -->
                <div class="menu-card__body">

                    <!-- Header : titre + prix -->
                    <div class="menu-card__header">
                        <h2 class="menu-card__title">
                            <a
                                href="detail.php?slug=<?= htmlspecialchars($menu['slug'], ENT_QUOTES, 'UTF-8') ?>"
                                class="menu-card__title-link">
                                <?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h2>
                        <span class="menu-card__price">
                            <?= number_format((float) $menu['prix_par_personne'], 0, ',', ' ') ?>€
                        </span>
                    </div>

                    <!-- Description -->
                    <?php if (!empty($menu['description'])): ?>
                        <p class="menu-card__description">
                            <?= htmlspecialchars(
                                mb_substr($menu['description'], 0, 100) . (mb_strlen($menu['description']) > 100 ? '...' : ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Méta : personnes + thème -->
                    <div class="menu-card__meta">
                        <span class="menu-card__meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                            <?= $isEn ? 'Min.' : 'Min.' ?> <?= (int) $menu['nb_personnes_min'] ?> <?= $isEn ? 'pers.' : 'pers.' ?>
                        </span>
                        <?php if (!empty($menu['theme_nom'])): ?>
                        <span class="menu-card__meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                            </svg>
                            <?= htmlspecialchars($menu['theme_nom'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Allergènes -->
                    <?php if (!empty($menu['allergenes_resume'])): ?>
                        <div class="menu-card__allergenes">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span>
                                <?= $isEn ? 'Contains:' : 'Contient:' ?>
                                <?= htmlspecialchars($menu['allergenes_resume'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- CTA -->
                    <a
                        href="detail.php?slug=<?= htmlspecialchars($menu['slug'], ENT_QUOTES, 'UTF-8') ?>"
                        class="menu-card__cta">
                        <?= $isEn ? 'View details' : 'Voir le détail' ?>
                    </a>

                </div>

            </article>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>

    </div>
</section>
