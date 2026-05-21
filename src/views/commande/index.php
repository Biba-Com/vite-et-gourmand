<?php

/**
 * Vue : Formulaire de commande
 * Chemin : src/views/commande/index.php
 *
 * Variables disponibles :
 *  - $user       array   Données de l'utilisateur connecté
 *  - $panier     array   Items du panier
 *  - $firstItem  array   Premier item (pour pré-remplir date/adresse)
 *  - $errors     array   Erreurs de validation
 *  - $isEn       bool    Langue
 */

$isEn      = $isEn      ?? false;
$user      = $user      ?? [];
$panier    = $panier    ?? [];
$firstItem = $firstItem ?? [];
$errors    = $errors    ?? [];

// Calculs totaux
$sousTotal    = 0.0;
$totalRemises = 0.0;
$nbPersonnes  = 0;

foreach ($panier as $item) {
    $sousTotal    += $item['sous_total_brut'] ?? ($item['prix_unitaire'] * $item['nb_personnes']);
    $totalRemises += $item['remise'] ?? 0;
    $nbPersonnes  += $item['nb_personnes'];
}

$sousNet = $sousTotal - $totalRemises;
?>

<div class="commande-page">
    <div class="container">

        <!-- En-tête -->
        <div class="commande-page__header">
            <h1 class="commande-page__title">
                <?= $isEn ? 'Place your order' : 'Finaliser la commande' ?>
            </h1>
            <a href="/panier/" class="commande-page__back">
                ← <?= $isEn ? 'Back to cart' : 'Retour au panier' ?>
            </a>
        </div>

        <!-- Erreurs -->
        <?php if (!empty($errors)): ?>
            <div class="commande-alert commande-alert--error" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="commande-layout">

            <!-- ── Formulaire ───────────────────────── -->
            <form class="commande-form" method="POST" action="/commande/" novalidate>

                <!-- Infos client (auto-remplies) -->
                <section class="commande-section">
                    <h2 class="commande-section__title">
                        <span class="commande-section__num">1</span>
                        <?= $isEn ? 'Your details' : 'Vos coordonnées' ?>
                    </h2>

                    <div class="commande-form__row">
                        <div class="commande-form__field">
                            <label class="commande-form__label">
                                <?= $isEn ? 'First name' : 'Prénom' ?>
                            </label>
                            <input type="text" class="commande-form__input commande-form__input--readonly"
                                   value="<?= htmlspecialchars($user['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly aria-readonly="true">
                        </div>
                        <div class="commande-form__field">
                            <label class="commande-form__label">
                                <?= $isEn ? 'Last name' : 'Nom' ?>
                            </label>
                            <input type="text" class="commande-form__input commande-form__input--readonly"
                                   value="<?= htmlspecialchars($user['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly aria-readonly="true">
                        </div>
                    </div>

                    <div class="commande-form__row">
                        <div class="commande-form__field">
                            <label class="commande-form__label">Email</label>
                            <input type="email" class="commande-form__input commande-form__input--readonly"
                                   value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly aria-readonly="true">
                        </div>
                        <div class="commande-form__field">
                            <label class="commande-form__label">
                                <?= $isEn ? 'Phone' : 'Téléphone' ?>
                            </label>
                            <input type="tel" class="commande-form__input commande-form__input--readonly"
                                   value="<?= htmlspecialchars($user['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   readonly aria-readonly="true">
                        </div>
                    </div>

                    <p class="commande-form__note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <?= $isEn ? 'To update your details:' : 'Pour modifier vos informations :' ?>
                        <a href="/mon-compte/"><?= $isEn ? 'My account' : 'Mon compte' ?></a>
                    </p>
                </section>

                <!-- Infos prestation -->
                <section class="commande-section">
                    <h2 class="commande-section__title">
                        <span class="commande-section__num">2</span>
                        <?= $isEn ? 'Event details' : 'Détails de l\'événement' ?>
                    </h2>

                    <div class="commande-form__row">
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="date_evenement">
                                <?= $isEn ? 'Event date' : 'Date de l\'événement' ?>
                                <span class="form-required">*</span>
                            </label>
                            <input
                                type="date"
                                id="date_evenement"
                                name="date_evenement"
                                class="commande-form__input"
                                value="<?= htmlspecialchars($firstItem['date_evenement'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                min="<?= (new DateTime('+2 days'))->format('Y-m-d') ?>"
                                required aria-required="true">
                        </div>
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="heure_evenement">
                                <?= $isEn ? 'Preferred time' : 'Heure souhaitée' ?>
                            </label>
                            <input
                                type="time"
                                id="heure_evenement"
                                name="heure_evenement"
                                class="commande-form__input"
                                value="12:00"
                                min="08:00" max="22:00">
                        </div>
                    </div>

                    <div class="commande-form__field">
                        <label class="commande-form__label" for="adresse_livraison">
                            <?= $isEn ? 'Delivery address' : 'Adresse de livraison' ?>
                            <span class="form-required">*</span>
                        </label>
                        <input
                            type="text"
                            id="adresse_livraison"
                            name="adresse_livraison"
                            class="commande-form__input"
                            value="<?= htmlspecialchars($firstItem['adresse_livraison'] ?? $user['adresse'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="12 Rue de la Gastronomie"
                            required aria-required="true">
                    </div>

                    <div class="commande-form__row">
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="code_postal">
                                <?= $isEn ? 'Postal code' : 'Code postal' ?>
                                <span class="form-required">*</span>
                            </label>
                            <input
                                type="text"
                                id="code_postal"
                                name="code_postal"
                                class="commande-form__input"
                                value="<?= htmlspecialchars($firstItem['code_postal'] ?? $user['code_postal'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="33000"
                                maxlength="10"
                                required aria-required="true">
                        </div>
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="ville">
                                <?= $isEn ? 'City' : 'Ville' ?>
                                <span class="form-required">*</span>
                            </label>
                            <input
                                type="text"
                                id="ville"
                                name="ville"
                                class="commande-form__input"
                                value="<?= htmlspecialchars($firstItem['ville'] ?? $user['ville'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Bordeaux"
                                required aria-required="true">
                        </div>
                    </div>

                    <!-- Hint frais livraison -->
                    <p class="commande-form__delivery-hint" id="delivery-hint">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <?= $isEn
                            ? 'Free delivery in Bordeaux. Outside: 5€ + 0.59€/km.'
                            : 'Livraison offerte à Bordeaux. Hors Bordeaux : 5€ + 0,59€/km.' ?>
                    </p>

                    <div class="commande-form__field">
                        <label class="commande-form__label" for="notes">
                            <?= $isEn ? 'Special requests (optional)' : 'Demandes particulières (optionnel)' ?>
                        </label>
                        <textarea
                            id="notes"
                            name="notes"
                            class="commande-form__textarea"
                            rows="3"
                            placeholder="<?= $isEn ? 'Allergies, special setup, access info...' : 'Allergies particulières, disposition souhaitée, accès...' ?>"
                            maxlength="1000"></textarea>
                    </div>
                </section>

                <!-- Submit -->
                <button type="submit" class="btn btn--primary btn--lg commande-form__submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <?= $isEn ? 'Confirm my order' : 'Confirmer ma commande' ?>
                </button>

            </form>

            <!-- ── Récapitulatif ─────────────────────── -->
            <aside class="commande-summary" aria-label="Récapitulatif">
                <h2 class="commande-summary__title">
                    <?= $isEn ? 'Order Summary' : 'Récapitulatif' ?>
                </h2>

                <!-- Menus -->
                <div class="commande-summary__items">
                    <?php foreach ($panier as $item): ?>
                        <div class="commande-summary__item">
                            <div class="commande-summary__item-info">
                                <strong><?= htmlspecialchars($item['titre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span><?= $item['nb_personnes'] ?> <?= $isEn ? 'guests' : 'pers.' ?> × <?= number_format($item['prix_unitaire'], 2, ',', ' ') ?> €</span>
                            </div>
                            <span class="commande-summary__item-price">
                                <?= number_format($item['sous_total_brut'] ?? $item['sous_total'], 2, ',', ' ') ?> €
                            </span>
                        </div>
                        <?php if (($item['remise'] ?? 0) > 0): ?>
                            <div class="commande-summary__remise">
                                <span>Remise -10%</span>
                                <span>- <?= number_format($item['remise'], 2, ',', ' ') ?> €</span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <dl class="commande-summary__totals">
                    <div class="commande-summary__line">
                        <dt><?= $isEn ? 'Subtotal' : 'Sous-total' ?></dt>
                        <dd><?= number_format($sousNet, 2, ',', ' ') ?> €</dd>
                    </div>
                    <div class="commande-summary__line" id="summary-delivery">
                        <dt><?= $isEn ? 'Delivery' : 'Livraison' ?></dt>
                        <dd id="summary-delivery-amount">
                            <?php if (strtolower(trim($firstItem['ville'] ?? '')) === 'bordeaux'): ?>
                                <span class="commande-summary__free">Offerte 🎉</span>
                            <?php else: ?>
                                <?= $isEn ? 'Calculated on confirm' : 'Calculé à la validation' ?>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="commande-summary__line commande-summary__line--total">
                        <dt><?= $isEn ? 'Total' : 'Total TTC' ?></dt>
                        <dd id="summary-total"><?= number_format($sousNet, 2, ',', ' ') ?> €</dd>
                    </div>
                </dl>

                <!-- Conditions -->
                <div class="commande-summary__conditions">
                    <h3 class="commande-summary__conditions-title">
                        <?= $isEn ? 'Conditions' : 'Conditions du menu' ?>
                    </h3>
                    <ul>
                        <li>⏰ <?= $isEn ? 'Min. 48h notice required' : 'Commande minimum 48h à l\'avance' ?></li>
                        <li>💳 <?= $isEn ? '30% deposit on confirmation' : 'Acompte 30% à la confirmation' ?></li>
                        <li>❌ <?= $isEn ? 'Free cancellation up to "confirmed"' : 'Annulation gratuite avant "accepté"' ?></li>
                        <li>📦 <?= $isEn ? 'Service included' : 'Service et vaisselle inclus' ?></li>
                    </ul>
                </div>
            </aside>

        </div><!-- /.commande-layout -->
    </div>
</div>

<script>
(function () {
    'use strict';

    const villeInput = document.getElementById('ville');
    const hint       = document.getElementById('delivery-hint');
    const summaryAmt = document.getElementById('summary-delivery-amount');
    const sousNet    = <?= $sousNet ?>;

    function updateDelivery() {
        const ville     = villeInput?.value.trim().toLowerCase() || '';
        const isBordeaux = ville === 'bordeaux';
        const frais     = isBordeaux ? 0 : 5.00; // Estimation sans km précis
        const total     = sousNet + frais;

        if (hint) {
            hint.innerHTML = isBordeaux
                ? '🎉 <?= $isEn ? "Free delivery in Bordeaux!" : "Livraison offerte à Bordeaux !" ?>'
                : '📍 <?= $isEn ? "Delivery: 5€ + 0.59€/km (calculated on confirm)" : "Livraison : 5€ + 0,59€/km (calculé à la validation)" ?>';
        }

        if (summaryAmt) {
            summaryAmt.innerHTML = isBordeaux
                ? '<span class="commande-summary__free">Offerte 🎉</span>'
                : '~<?= $isEn ? "From 5€" : "À partir de 5€" ?>';
        }
    }

    villeInput?.addEventListener('input', updateDelivery);
    updateDelivery();
})();
</script>
