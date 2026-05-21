<?php

/**
 * Vue : Détail d'un menu (v3.0)
 * Chemin : src/views/catalogue/detail.php
 *
 * Variables disponibles :
 *  - $menu              array   Données du menu
 *  - $composition       array   Plats du menu
 *  - $allergenes        array   Allergènes du menu
 *  - $platsParCategorie array   Plats groupés par catégorie
 *  - $cartErrors        array   Erreurs panier (optionnel)
 *  - $isEn              bool    Langue active
 */

$isEn              = $isEn              ?? false;
$menu              = $menu              ?? [];
$allergenes        = $allergenes        ?? [];
$platsParCategorie = $platsParCategorie ?? [];
$cartErrors        = $cartErrors        ?? [];

$labelsCategorie = [
    'starter' => $isEn ? '🥗 Starters'     : '🥗 Entrées',
    'main'    => $isEn ? '🍽️ Main courses' : '🍽️ Plats principaux',
    'dessert' => $isEn ? '🍰 Desserts'      : '🍰 Desserts',
    'drink'   => $isEn ? '☕ Drinks'        : '☕ Boissons',
    'other'   => $isEn ? '🧀 Other'         : '🧀 Autres',
];

$minPersonnes = (int) ($menu['nb_personnes_min'] ?? 1);
$maxPersonnes = !empty($menu['nb_personnes_max']) ? (int) $menu['nb_personnes_max'] : 9999;
$prixUnitaire = (float) ($menu['prix_par_personne'] ?? 0);

// Date minimum : aujourd'hui + 2 jours (48h)
$dateMin = (new DateTime('+2 days'))->format('Y-m-d');
$dateMax = (new DateTime('+2 years'))->format('Y-m-d');
?>

<!-- ── Fil d'Ariane ─────────────────────────────────────── -->
<nav class="breadcrumb" aria-label="<?= $isEn ? 'Breadcrumb' : 'Fil d\'ariane' ?>">
    <div class="container">
        <ol class="breadcrumb__list">
            <li class="breadcrumb__item">
                <a href="/" class="breadcrumb__link"><?= $isEn ? 'Home' : 'Accueil' ?></a>
            </li>
            <li class="breadcrumb__item" aria-hidden="true">›</li>
            <li class="breadcrumb__item">
                <a href="/catalogue/" class="breadcrumb__link"><?= $isEn ? 'Menus' : 'Catalogue' ?></a>
            </li>
            <li class="breadcrumb__item" aria-hidden="true">›</li>
            <li class="breadcrumb__item breadcrumb__item--active" aria-current="page">
                <?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>
            </li>
        </ol>
    </div>
</nav>

<!-- ── Erreurs panier (si POST invalide) ────────────────── -->
<?php if (!empty($cartErrors)): ?>
<div class="container">
    <div class="cart-error-banner" role="alert">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <ul>
            <?php foreach ($cartErrors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════
     HERO — 2 colonnes
══════════════════════════════════════════════════════ -->
<section class="menu-detail-hero">
    <div class="container">
        <div class="menu-detail-hero__inner">

            <!-- Image -->
            <div class="menu-detail-hero__image-wrapper">
                <?php if (!empty($menu['image_url'])): ?>
                    <img class="menu-detail-hero__image"
                         src="<?= htmlspecialchars($menu['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>"
                         width="600" height="400">
                <?php else: ?>
                    <div class="menu-detail-hero__image menu-detail-hero__image--placeholder" aria-hidden="true">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path d="M3 11l19-9-9 19-2-8-8-2z"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Infos -->
            <div class="menu-detail-hero__content">

                <?php if (!empty($menu['theme_nom'])): ?>
                    <span class="menu-detail__theme"
                          style="--theme-color: <?= htmlspecialchars($menu['theme_couleur'] ?? '#D4AF37', ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($menu['theme_nom'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                <?php endif; ?>

                <h1 class="menu-detail__title">
                    <?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <?php if (!empty($menu['description'])): ?>
                    <p class="menu-detail__description">
                        <?= htmlspecialchars($menu['description'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <!-- Prix + capacité -->
                <div class="menu-detail__pricing">
                    <div class="menu-detail__price">
                        <span class="menu-detail__price-amount" id="prix-display">
                            <?= number_format($prixUnitaire, 2, ',', ' ') ?> €
                        </span>
                        <span class="menu-detail__price-unit">
                            / <?= $isEn ? 'person' : 'personne' ?>
                        </span>
                    </div>
                    <div class="menu-detail__capacity">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <?= $minPersonnes ?>
                        <?php if (!empty($menu['nb_personnes_max'])): ?>
                            – <?= (int) $menu['nb_personnes_max'] ?>
                        <?php else: ?>+<?php endif; ?>
                        <?= $isEn ? 'guests min.' : 'personnes min.' ?>
                    </div>
                </div>

                <!-- Badges éco -->
                <?php if (!empty($menu['badges_eco'])): ?>
                    <div class="menu-detail__badges">
                        <?php foreach (explode(' | ', $menu['badges_eco']) as $badge): ?>
                            <span class="badge badge--eco">
                                <?= htmlspecialchars(trim($badge), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Régimes -->
                <?php if (!empty($menu['regimes'])): ?>
                    <div class="menu-detail__regimes">
                        <span class="menu-detail__regimes-label">
                            <?= $isEn ? 'Dietary:' : 'Régimes:' ?>
                        </span>
                        <?= htmlspecialchars($menu['regimes'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <!-- ── CTA ──────────────────────────────── -->
                <div class="menu-detail__actions">

                    <!-- Bouton Ajouter au panier → ouvre la modale -->
                    <button
                        type="button"
                        class="btn btn--primary btn--lg"
                        id="btn-open-cart-modal"
                        aria-haspopup="dialog"
                        aria-controls="cart-modal">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <?= $isEn ? 'Add to cart' : 'Ajouter au panier' ?>
                    </button>

                    <!-- Demander un devis (secondaire) -->
                    <a href="/contact/?menu=<?= htmlspecialchars($menu['slug'], ENT_QUOTES, 'UTF-8') ?>"
                       class="btn btn--ghost btn--lg">
                        <?= $isEn ? 'Request a quote' : 'Demander un devis' ?>
                    </a>

                    <a href="/catalogue/" class="btn btn--ghost btn--lg">
                        ← <?= $isEn ? 'Back' : 'Retour' ?>
                    </a>
                </div>

            </div><!-- /.menu-detail-hero__content -->
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════
     COMPOSITION
══════════════════════════════════════════════════════ -->
<section class="menu-composition" aria-labelledby="composition-title">
    <div class="container">
        <h2 class="section-title" id="composition-title">
            <?= $isEn ? 'Menu Composition' : 'Composition du menu' ?>
        </h2>

        <?php if (empty($platsParCategorie)): ?>
            <p><?= $isEn ? 'Composition available on request.' : 'Composition disponible sur demande.' ?></p>
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
                                        <img class="composition-plat__image"
                                             src="<?= htmlspecialchars($plat['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                                             alt="<?= htmlspecialchars($plat['nom'], ENT_QUOTES, 'UTF-8') ?>"
                                             width="80" height="80" loading="lazy">
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
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════
     ALLERGÈNES
══════════════════════════════════════════════════════ -->
<section class="menu-allergenes" aria-labelledby="allergenes-title">
    <div class="container">
        <h2 class="section-title" id="allergenes-title">
            <?= $isEn ? 'Allergen Information' : 'Informations allergènes' ?>
        </h2>
        <p class="menu-allergenes__legal">
            <?= $isEn
                ? 'EU Regulation No. 1169/2011 — Contact us for specific dietary requirements.'
                : 'Conformément au règlement UE n°1169/2011. Contactez-nous pour tout besoin spécifique.' ?>
        </p>
        <?php if (empty($allergenes)): ?>
            <p class="menu-allergenes__none">✅ <?= $isEn ? 'No major allergens.' : 'Aucun allergène majeur détecté.' ?></p>
        <?php else: ?>
            <ul class="allergenes-list" role="list">
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

<!-- ══════════════════════════════════════════════════════
     CTA FINAL
══════════════════════════════════════════════════════ -->
<section class="menu-detail-cta">
    <div class="container">
        <h2 class="menu-detail-cta__title">
            <?= $isEn ? 'Interested in this menu?' : 'Ce menu vous intéresse ?' ?>
        </h2>
        <p class="menu-detail-cta__text">
            <?= $isEn ? 'Our team replies within 24 hours.' : 'Notre équipe vous répond sous 24h.' ?>
        </p>
        <button type="button" class="btn btn--primary btn--lg" id="btn-open-cart-modal-cta">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <?= $isEn ? 'Add to cart' : 'Ajouter au panier' ?>
        </button>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════
     MODALE — Ajouter au panier
══════════════════════════════════════════════════════ -->
<div
    class="cart-modal"
    id="cart-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="cart-modal-title"
    aria-hidden="true">

    <div class="cart-modal__overlay" id="cart-modal-overlay"></div>

    <div class="cart-modal__panel">

        <!-- En-tête modale -->
        <div class="cart-modal__header">
            <h2 class="cart-modal__title" id="cart-modal-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <?= $isEn ? 'Add to cart' : 'Ajouter au panier' ?>
            </h2>
            <button type="button" class="cart-modal__close" id="cart-modal-close"
                    aria-label="<?= $isEn ? 'Close' : 'Fermer' ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <!-- Résumé menu -->
        <div class="cart-modal__menu-summary">
            <strong><?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span class="cart-modal__price-unit">
                <?= number_format($prixUnitaire, 2, ',', ' ') ?> € / <?= $isEn ? 'person' : 'pers.' ?>
            </span>
        </div>

        <!-- Formulaire POST -->
        <form
            class="cart-modal__form"
            method="POST"
            action="/catalogue/detail.php?slug=<?= htmlspecialchars($menu['slug'], ENT_QUOTES, 'UTF-8') ?>"
            novalidate>

            <input type="hidden" name="action" value="add_to_cart">

            <!-- Nombre de personnes -->
            <div class="cart-modal__field">
                <label class="cart-modal__label" for="modal-nb-personnes">
                    <?= $isEn ? 'Number of guests' : 'Nombre de personnes' ?>
                    <span class="form-required" aria-label="obligatoire">*</span>
                </label>
                <input
                    type="number"
                    id="modal-nb-personnes"
                    name="nb_personnes"
                    class="cart-modal__input"
                    min="<?= $minPersonnes ?>"
                    max="<?= $maxPersonnes < 9999 ? $maxPersonnes : '' ?>"
                    value="<?= $minPersonnes ?>"
                    required
                    aria-required="true"
                    aria-describedby="nb-personnes-hint">
                <span class="cart-modal__hint" id="nb-personnes-hint">
                    <?= $isEn ? "Min. {$minPersonnes} guests" : "Min. {$minPersonnes} personnes" ?>
                    <?php if ($maxPersonnes < 9999): ?>
                        — <?= $isEn ? "Max. {$maxPersonnes}" : "Max. {$maxPersonnes}" ?>
                    <?php endif; ?>
                </span>
            </div>

            <!-- Date événement -->
            <div class="cart-modal__field">
                <label class="cart-modal__label" for="modal-date">
                    <?= $isEn ? 'Event date' : 'Date de l\'événement' ?>
                    <span class="form-required" aria-label="obligatoire">*</span>
                </label>
                <input
                    type="date"
                    id="modal-date"
                    name="date_evenement"
                    class="cart-modal__input"
                    min="<?= $dateMin ?>"
                    max="<?= $dateMax ?>"
                    required
                    aria-required="true"
                    aria-describedby="date-hint">
                <span class="cart-modal__hint" id="date-hint">
                    <?= $isEn ? 'Min. 48h in advance' : 'Minimum 48h à l\'avance' ?>
                </span>
            </div>

            <!-- Adresse -->
            <div class="cart-modal__field">
                <label class="cart-modal__label" for="modal-adresse">
                    <?= $isEn ? 'Delivery address' : 'Adresse de livraison' ?>
                    <span class="form-required" aria-label="obligatoire">*</span>
                </label>
                <input
                    type="text"
                    id="modal-adresse"
                    name="adresse_livraison"
                    class="cart-modal__input"
                    placeholder="<?= $isEn ? '12 Rue de la Paix' : '12 Rue de la Gastronomie' ?>"
                    maxlength="255"
                    required
                    aria-required="true">
            </div>

            <!-- Code postal + Ville sur 2 colonnes -->
            <div class="cart-modal__row">
                <div class="cart-modal__field">
                    <label class="cart-modal__label" for="modal-cp">
                        <?= $isEn ? 'Postal code' : 'Code postal' ?>
                        <span class="form-required" aria-label="obligatoire">*</span>
                    </label>
                    <input
                        type="text"
                        id="modal-cp"
                        name="code_postal"
                        class="cart-modal__input"
                        placeholder="33000"
                        maxlength="10"
                        required
                        aria-required="true">
                </div>
                <div class="cart-modal__field">
                    <label class="cart-modal__label" for="modal-ville">
                        <?= $isEn ? 'City' : 'Ville' ?>
                        <span class="form-required" aria-label="obligatoire">*</span>
                    </label>
                    <input
                        type="text"
                        id="modal-ville"
                        name="ville"
                        class="cart-modal__input"
                        placeholder="Bordeaux"
                        maxlength="100"
                        required
                        aria-required="true"
                        id="modal-ville-input">
                </div>
            </div>

            <!-- Aperçu prix dynamique -->
            <div class="cart-modal__price-preview" id="price-preview" aria-live="polite">
                <div class="cart-modal__price-line">
                    <span><?= $isEn ? 'Subtotal' : 'Sous-total' ?></span>
                    <strong id="preview-subtotal">—</strong>
                </div>
                <div class="cart-modal__price-line" id="preview-remise-line" style="display:none;">
                    <span><?= $isEn ? 'Discount -10%' : 'Remise -10%' ?></span>
                    <strong id="preview-remise" class="cart-modal__price-remise">—</strong>
                </div>
                <div class="cart-modal__price-line cart-modal__price-line--total">
                    <span><?= $isEn ? 'Total' : 'Total' ?></span>
                    <strong id="preview-total">—</strong>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn--primary btn--lg cart-modal__submit">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <?= $isEn ? 'Add to cart' : 'Ajouter au panier' ?>
            </button>

        </form>

    </div><!-- /.cart-modal__panel -->
</div><!-- /#cart-modal -->

<!-- ══════════════════════════════════════════════════════
     JS — Modale panier
══════════════════════════════════════════════════════ -->
<script>
(function () {
    'use strict';

    const modal      = document.getElementById('cart-modal');
    const overlay    = document.getElementById('cart-modal-overlay');
    const btnOpen    = document.getElementById('btn-open-cart-modal');
    const btnOpenCta = document.getElementById('btn-open-cart-modal-cta');
    const btnClose   = document.getElementById('cart-modal-close');
    const nbInput    = document.getElementById('modal-nb-personnes');
    const villeInput = document.getElementById('modal-ville');

    // Prix depuis PHP
    const prixUnitaire  = <?= $prixUnitaire ?>;
    const minPersonnes  = <?= $minPersonnes ?>;

    // ── Ouvrir modale ──────────────────────────────────
    function openModal() {
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('cart-modal--open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => nbInput?.focus(), 100);
        updatePricePreview();
    }

    // ── Fermer modale ──────────────────────────────────
    function closeModal() {
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('cart-modal--open');
        document.body.style.overflow = '';
        btnOpen?.focus();
    }

    btnOpen?.addEventListener('click', openModal);
    btnOpenCta?.addEventListener('click', openModal);
    btnClose?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('cart-modal--open')) {
            closeModal();
        }
    });

    // ── Calcul prix dynamique ──────────────────────────
    function updatePricePreview() {
        const nb        = parseInt(nbInput?.value) || 0;
        const subtotal  = prixUnitaire * nb;
        const hasRemise = nb >= (minPersonnes + 5);
        const remise    = hasRemise ? subtotal * 0.10 : 0;
        const total     = subtotal - remise;

        const fmtEur = (val) => val.toLocaleString('fr-FR', {
            minimumFractionDigits: 2, maximumFractionDigits: 2
        }) + ' €';

        const elSubtotal    = document.getElementById('preview-subtotal');
        const elRemise      = document.getElementById('preview-remise');
        const elRemiseLine  = document.getElementById('preview-remise-line');
        const elTotal       = document.getElementById('preview-total');

        if (nb < minPersonnes) {
            if (elSubtotal) elSubtotal.textContent = '—';
            if (elTotal)    elTotal.textContent    = '—';
            if (elRemiseLine) elRemiseLine.style.display = 'none';
            return;
        }

        if (elSubtotal) elSubtotal.textContent = fmtEur(subtotal);
        if (elTotal)    elTotal.textContent    = fmtEur(total);

        if (hasRemise && elRemiseLine && elRemise) {
            elRemiseLine.style.display = 'flex';
            elRemise.textContent = '- ' + fmtEur(remise);
        } else if (elRemiseLine) {
            elRemiseLine.style.display = 'none';
        }
    }

    nbInput?.addEventListener('input', updatePricePreview);

    // ── Frais livraison hint ───────────────────────────
    villeInput?.addEventListener('input', function () {
        const isBordeaux = this.value.trim().toLowerCase() === 'bordeaux';
        let hint = document.getElementById('ville-delivery-hint');
        if (!hint) {
            hint = document.createElement('span');
            hint.id = 'ville-delivery-hint';
            hint.className = 'cart-modal__hint';
            this.parentNode.appendChild(hint);
        }
        hint.textContent = isBordeaux
            ? '🎉 <?= $isEn ? "Free delivery in Bordeaux!" : "Livraison offerte à Bordeaux !" ?>'
            : '📍 <?= $isEn ? "Delivery: 5€ + 0.59€/km" : "Livraison : 5€ + 0,59€/km" ?>';
    });

    // ── Init ───────────────────────────────────────────
    updatePricePreview();

    // ── Si erreurs panier → ouvrir modale auto ────────
    <?php if (!empty($cartErrors)): ?>
    openModal();
    <?php endif; ?>

})();
</script>
