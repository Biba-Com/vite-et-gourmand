<?php

/**
 * Vue : Modifier une commande
 * Chemin : src/views/mes-commandes/modifier.php
 */

$isEn     = $isEn     ?? false;
$commande = $commande ?? [];
$errors   = $errors   ?? [];

$dateEvt = !empty($commande['date_evenement'])
    ? (new DateTime($commande['date_evenement']))->format('Y-m-d')
    : '';
$heureEvt = !empty($commande['date_evenement'])
    ? (new DateTime($commande['date_evenement']))->format('H:i')
    : '12:00';
?>

<div class="commande-page">
    <div class="container">

        <!-- En-tête -->
        <div class="commande-page__header">
            <h1 class="commande-page__title">
                <?= $isEn ? 'Edit order' : 'Modifier la commande' ?>
                <span style="color:var(--color-bordeaux)">
                    #<?= str_pad($commande['id_commande'], 4, '0', STR_PAD_LEFT) ?>
                </span>
            </h1>
            <a href="/mes-commandes/" class="commande-page__back">
                ← <?= $isEn ? 'My orders' : 'Mes commandes' ?>
            </a>
        </div>

        <!-- Erreurs -->
        <?php if (!empty($errors)): ?>
            <div class="commande-alert commande-alert--error" role="alert">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="commande-layout">

            <form class="commande-form" method="POST"
                  action="/mes-commandes/modifier.php" novalidate>

                <input type="hidden" name="id_commande"
                       value="<?= $commande['id_commande'] ?>">

                <!-- Menu (non modifiable — énoncé page 7) -->
                <section class="commande-section">
                    <h2 class="commande-section__title">
                        <span class="commande-section__num">1</span>
                        <?= $isEn ? 'Menu ordered' : 'Menu commandé' ?>
                    </h2>
                    <div class="commande-form__field">
                        <label class="commande-form__label">
                            <?= $isEn ? 'Menu (cannot be changed)' : 'Menu (non modifiable)' ?>
                        </label>
                        <input type="text"
                               class="commande-form__input commande-form__input--readonly"
                               value="<?= htmlspecialchars($commande['menus_titres'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               readonly aria-readonly="true">
                        <span style="font-size:var(--fs-xs);color:var(--color-gray-400);">
                            ℹ️ <?= $isEn
                                ? 'To change the menu, cancel this order and place a new one.'
                                : 'Pour changer de menu, annulez et passez une nouvelle commande.' ?>
                        </span>
                    </div>
                </section>

                <!-- Infos modifiables -->
                <section class="commande-section">
                    <h2 class="commande-section__title">
                        <span class="commande-section__num">2</span>
                        <?= $isEn ? 'Event details' : 'Détails de l\'événement' ?>
                    </h2>

                    <!-- Nombre de personnes -->
                    <div class="commande-form__field">
                        <label class="commande-form__label" for="nb_personnes">
                            <?= $isEn ? 'Number of guests' : 'Nombre de personnes' ?>
                            <span class="form-required">*</span>
                        </label>
                        <input type="number"
                               id="nb_personnes"
                               name="nb_personnes"
                               class="commande-form__input"
                               value="<?= (int) $commande['nb_personnes'] ?>"
                               min="1" required aria-required="true">
                    </div>

                    <div class="commande-form__row">
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="date_evenement">
                                <?= $isEn ? 'Event date' : 'Date de l\'événement' ?>
                                <span class="form-required">*</span>
                            </label>
                            <input type="date"
                                   id="date_evenement"
                                   name="date_evenement"
                                   class="commande-form__input"
                                   value="<?= $dateEvt ?>"
                                   min="<?= (new DateTime('+2 days'))->format('Y-m-d') ?>"
                                   required aria-required="true">
                        </div>
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="heure_evenement">
                                <?= $isEn ? 'Time' : 'Heure' ?>
                            </label>
                            <input type="time"
                                   id="heure_evenement"
                                   name="heure_evenement"
                                   class="commande-form__input"
                                   value="<?= $heureEvt ?>"
                                   min="08:00" max="22:00">
                        </div>
                    </div>

                    <div class="commande-form__field">
                        <label class="commande-form__label" for="adresse_livraison">
                            <?= $isEn ? 'Delivery address' : 'Adresse de livraison' ?>
                            <span class="form-required">*</span>
                        </label>
                        <input type="text"
                               id="adresse_livraison"
                               name="adresse_livraison"
                               class="commande-form__input"
                               value="<?= htmlspecialchars($commande['adresse_livraison'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               required aria-required="true">
                    </div>

                    <div class="commande-form__row">
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="code_postal">
                                <?= $isEn ? 'Postal code' : 'Code postal' ?>
                                <span class="form-required">*</span>
                            </label>
                            <input type="text"
                                   id="code_postal"
                                   name="code_postal"
                                   class="commande-form__input"
                                   value="<?= htmlspecialchars($commande['code_postal_livraison'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   maxlength="10"
                                   required aria-required="true">
                        </div>
                        <div class="commande-form__field">
                            <label class="commande-form__label" for="ville">
                                <?= $isEn ? 'City' : 'Ville' ?>
                                <span class="form-required">*</span>
                            </label>
                            <input type="text"
                                   id="ville"
                                   name="ville"
                                   class="commande-form__input"
                                   value="<?= htmlspecialchars($commande['ville_livraison'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                   required aria-required="true">
                        </div>
                    </div>
                </section>

                <!-- Actions -->
                <div style="display:flex;gap:var(--space-md);flex-wrap:wrap;">
                    <button type="submit" class="btn btn--primary btn--lg" style="flex:1;justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        <?= $isEn ? 'Save changes' : 'Enregistrer les modifications' ?>
                    </button>
                    <a href="/mes-commandes/" class="btn btn--ghost btn--lg">
                        <?= $isEn ? 'Cancel' : 'Annuler' ?>
                    </a>
                </div>

            </form>

            <!-- Récapitulatif actuel -->
            <aside class="commande-summary">
                <h2 class="commande-summary__title">
                    <?= $isEn ? 'Current order' : 'Commande actuelle' ?>
                </h2>
                <dl class="commande-summary__totals">
                    <div class="commande-summary__line">
                        <dt><?= $isEn ? 'Menu' : 'Menu' ?></dt>
                        <dd><?= htmlspecialchars($commande['menus_titres'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="commande-summary__line">
                        <dt><?= $isEn ? 'Guests' : 'Personnes' ?></dt>
                        <dd><?= (int) $commande['nb_personnes'] ?></dd>
                    </div>
                    <div class="commande-summary__line">
                        <dt><?= $isEn ? 'Date' : 'Date' ?></dt>
                        <dd><?= !empty($commande['date_evenement'])
                            ? (new DateTime($commande['date_evenement']))->format('d/m/Y')
                            : '—' ?></dd>
                    </div>
                    <div class="commande-summary__line commande-summary__line--total">
                        <dt>Total actuel</dt>
                        <dd><?= number_format((float) $commande['total'], 2, ',', ' ') ?> €</dd>
                    </div>
                </dl>

                <div class="commande-summary__conditions">
                    <p style="font-family:var(--font-body);font-size:var(--fs-xs);color:var(--color-gray-500);margin:0;">
                        ⚠️ <?= $isEn
                            ? 'Modification possible only while status is "Pending".'
                            : 'Modification possible uniquement tant que le statut est "En attente".' ?>
                    </p>
                </div>
            </aside>

        </div>
    </div>
</div>
