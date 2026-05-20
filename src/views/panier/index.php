<?php

/**
 * Vue : Page Panier
 * Chemin : src/views/panier/index.php
 *
 * Variables disponibles :
 * - $panier        array   Items du panier
 * - $totalPanier   float   Sous-total des menus (déjà remisés)
 * - $totalRemises  float   Total des remises
 * - $totalFrais    float   Total frais de livraison
 * - $totalGeneral  float   Total à payer
 * - $flashSuccess  string  Message flash
 * - $isEn          bool    Langue
 */

$isEn = $isEn ?? false;
?>

<div class="panier-page">
    <div class="container">

        <div class="panier-page__header">
            <h1 class="panier-page__title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <?= $isEn ? 'My Cart' : 'Mon panier' ?>
            </h1>
            <a href="/catalogue/" class="panier-page__back">
                ← <?= $isEn ? 'Continue browsing' : 'Continuer ma sélection' ?>
            </a>
        </div>

        <?php if ($flashSuccess): ?>
            <div class="cart-alert cart-alert--success" role="status">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (empty($panier)): ?>
        <div class="panier-empty">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <h2><?= $isEn ? 'Your cart is empty' : 'Votre panier est vide' ?></h2>
            <p><?= $isEn ? 'Discover our menus and add them to your cart.' : 'Découvrez nos menus et ajoutez-les à votre panier.' ?></p>
            <a href="/catalogue/" class="btn btn--primary btn--lg">
                <?= $isEn ? 'Browse our menus' : 'Voir nos menus' ?>
            </a>
        </div>

        <?php else: ?>
        <div class="panier-layout">

            <div class="panier-items">
                <div class="panier-items__header">
                    <h2 class="panier-items__title">
                        <?= count($panier) ?> <?= $isEn ? 'menu(s) selected' : 'menu(s) sélectionné(s)' ?>
                    </h2>

                    <form method="POST" action="/panier/">
                        <button type="submit" name="action" value="clear" class="panier-items__clear"
                            onclick="return confirm('<?= $isEn ? 'Clear cart?' : 'Vider le panier ?' ?>')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                            <?= $isEn ? 'Clear cart' : 'Vider le panier' ?>
                        </button>
                    </form>
                </div>

                <?php foreach ($panier as $cartKey => $item): ?>
                <div class="panier-item">

                    <div class="panier-item__image">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?= htmlspecialchars($item['image_url'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars($item['titre'], ENT_QUOTES, 'UTF-8') ?>"
                                 width="120" height="80" loading="lazy">
                        <?php else: ?>
                            <div class="panier-item__image-placeholder" aria-hidden="true">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M3 11l19-9-9 19-2-8-8-2z"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="panier-item__info">
                        <h3 class="panier-item__title">
                            <a href="/catalogue/detail.php?slug=<?= htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($item['titre'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </h3>

                        <div class="panier-item__details">
                            <span class="panier-item__detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <?= $item['nb_personnes'] ?> <?= $isEn ? 'guests' : 'personnes' ?>
                            </span>
                            <span class="panier-item__detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <?= htmlspecialchars(
                                    date('d/m/Y', strtotime($item['date_evenement'])),
                                    ENT_QUOTES, 'UTF-8'
                                ) ?>
                            </span>
                            <span class="panier-item__detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <?= htmlspecialchars($item['ville'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <?php if ($item['remise'] > 0): ?>
                            <span class="panier-item__badge-remise">
                                🎉 -10% appliqués (<?= $item['nb_personnes'] ?> pers. ≥ <?= $item['nb_personnes_min'] + 5 ?>)
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="panier-item__pricing">
                        <?php if ($item['remise'] > 0): ?>
                            <span class="panier-item__price-original">
                                <?= number_format($item['sous_total_brut'], 2, ',', ' ') ?> €
                            </span>
                        <?php endif; ?>
                        <span class="panier-item__price">
                            <?= number_format($item['sous_total'], 2, ',', ' ') ?> €
                        </span>
                        <span class="panier-item__price-unit">
                            <?= number_format($item['prix_unitaire'], 2, ',', ' ') ?> € / pers.
                        </span>
                    </div>

                    <form method="POST" action="/panier/" class="panier-item__remove-form">
                        <input type="hidden" name="cart_key" value="<?= htmlspecialchars($cartKey, ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" name="action" value="remove"
                            class="panier-item__remove"
                            aria-label="<?= $isEn ? 'Remove ' . $item['titre'] : 'Supprimer ' . $item['titre'] ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </form>

                </div>
                <?php endforeach; ?>
            </div><aside class="panier-summary" aria-label="Récapitulatif de commande">
                <h2 class="panier-summary__title">
                    <?= $isEn ? 'Order Summary' : 'Récapitulatif' ?>
                </h2>

                <dl class="panier-summary__lines">
                    <div class="panier-summary__line">
                        <dt><?= $isEn ? 'Subtotal' : 'Sous-total menus' ?></dt>
                        <dd><?= number_format($totalPanier + $totalRemises, 2, ',', ' ') ?> €</dd> 
                    </div>

                    <?php if ($totalRemises > 0): ?>
                    <div class="panier-summary__line panier-summary__line--remise">
                        <dt><?= $isEn ? 'Discounts (-10%)' : 'Remises (-10%)' ?></dt>
                        <dd>- <?= number_format($totalRemises, 2, ',', ' ') ?> €</dd>
                    </div>
                    <?php endif; ?>

                    <div class="panier-summary__line">
                        <dt><?= $isEn ? 'Delivery' : 'Frais de livraison' ?></dt>
                        <dd>
                            <?php if ($totalFrais > 0): ?>
                                <?= number_format($totalFrais, 2, ',', ' ') ?> €
                            <?php else: ?>
                                <span class="panier-summary__free"><?= $isEn ? 'Free' : 'Offerts' ?> 🎉</span>
                            <?php endif; ?>
                        </dd>
                    </div>

                    <div class="panier-summary__line panier-summary__line--total">
                        <dt><?= $isEn ? 'Total' : 'Total TTC' ?></dt>
                        <dd><?= number_format($totalGeneral, 2, ',', ' ') ?> €</dd>
                    </div>
                </dl>

                <?php if (AuthController::isLoggedIn()): ?>
                    <a href="/commande/" class="btn btn--primary btn--lg panier-summary__cta">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <?= $isEn ? 'Place my order' : 'Passer la commande' ?>
                    </a>
                <?php else: ?>
                    <a href="/connexion/" class="btn btn--primary btn--lg panier-summary__cta">
                        <?= $isEn ? 'Login to order' : 'Se connecter pour commander' ?>
                    </a>
                <?php endif; ?>

                <p class="panier-summary__note">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?= $isEn
                        ? 'Delivery fees calculated based on distance. Bordeaux: free.'
                        : 'Frais de livraison calculés selon la distance. Bordeaux : offerts.' ?>
                </p>
            </aside>

        </div><?php endif; ?>

    </div>
</div>