<?php

/**
 * Vue : Mes commandes
 * Chemin : src/views/mes-commandes/index.php
 */

$isEn          = $isEn          ?? false;
$commandes     = $commandes     ?? [];
$flashSuccess  = $flashSuccess  ?? null;
$flashError    = $flashError    ?? null;
$statutLabels  = $statutLabels  ?? [];
?>

<div class="mes-commandes-page">
    <div class="container">

        <!-- En-tête -->
        <div class="mes-commandes__header">
            <h1 class="mes-commandes__title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                <?= $isEn ? 'My Orders' : 'Mes commandes' ?>
            </h1>
            <a href="/catalogue/" class="btn btn--ghost">
                <?= $isEn ? 'Browse menus' : 'Voir les menus' ?>
            </a>
        </div>

        <!-- Message flash succès -->
        <?php if (!empty($flashSuccess)): ?>
            <div class="mes-commandes__alert mes-commandes__alert--success" role="status">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Message flash erreur -->
        <?php if (!empty($flashError)): ?>
            <div class="mes-commandes__alert mes-commandes__alert--error" role="alert">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (empty($commandes)): ?>
        <!-- Aucune commande -->
        <div class="mes-commandes__empty">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
            <h2><?= $isEn ? 'No orders yet' : 'Aucune commande pour l\'instant' ?></h2>
            <p><?= $isEn ? 'Discover our menus and place your first order.' : 'Découvrez nos menus et passez votre première commande.' ?></p>
            <a href="/catalogue/" class="btn btn--primary btn--lg">
                <?= $isEn ? 'Browse our menus' : 'Voir nos menus' ?>
            </a>
        </div>

        <?php else: ?>
        <!-- Liste des commandes -->
        <div class="mes-commandes__list" role="list">
            <?php foreach ($commandes as $commande): ?>

                <?php
                $statut      = $commande['statut'];
                $statutInfo  = $statutLabels[$statut] ?? ['label' => $statut, 'color' => '#6B7280'];
                $dateEvt     = new DateTime($commande['date_evenement']);
                $dateCreated = new DateTime($commande['created_at']);
                $peutModifier = $statut === 'pending';
                $peutAnnuler  = $statut === 'pending';
                $peutSuivre   = in_array($statut, ['confirmed', 'in_preparation', 'in_delivery', 'completed']);
                $peutAvis     = $statut === 'completed';
                ?>

                <article class="commande-card" role="listitem">

                    <!-- Header carte -->
                    <div class="commande-card__header">
                        <div class="commande-card__ref">
                            <span class="commande-card__num">
                                #<?= str_pad($commande['id_commande'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                            <span class="commande-card__date-created">
                                <?= $isEn ? 'Placed on' : 'Passée le' ?>
                                <?= $dateCreated->format('d/m/Y à H:i') ?>
                            </span>
                        </div>
                        <span class="commande-card__statut"
                              style="--statut-color: <?= $statutInfo['color'] ?>">
                            <?= htmlspecialchars($statutInfo['label'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>

                    <!-- Corps carte -->
                    <div class="commande-card__body">

                        <!-- Menus commandés -->
                        <div class="commande-card__menus">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                            </svg>
                            <span><?= htmlspecialchars($commande['menus_titres'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <!-- Infos événement -->
                        <div class="commande-card__details">
                            <div class="commande-card__detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <?= $dateEvt->format('d/m/Y à H:i') ?>
                            </div>
                            <div class="commande-card__detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <?= htmlspecialchars($commande['adresse_livraison'], ENT_QUOTES, 'UTF-8') ?>,
                                <?= htmlspecialchars($commande['ville_livraison'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <div class="commande-card__detail">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                </svg>
                                <?= (int) $commande['nb_personnes'] ?>
                                <?= $isEn ? 'guests' : 'personnes' ?>
                            </div>
                        </div>

                    </div>

                    <!-- Footer carte -->
                    <div class="commande-card__footer">

                        <!-- Total -->
                        <div class="commande-card__total">
                            <span class="commande-card__total-label">Total</span>
                            <span class="commande-card__total-amount">
                                <?= number_format((float) $commande['total'], 2, ',', ' ') ?> €
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="commande-card__actions">

                            <!-- ✏️ Modifier (si pending) -->
                            <?php if ($peutModifier): ?>
                                <a href="/mes-commandes/modifier.php?id=<?= $commande['id_commande'] ?>"
                                   class="btn btn--ghost btn--sm">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    <?= $isEn ? 'Edit' : 'Modifier' ?>
                                </a>
                            <?php endif; ?>

                            <!-- 🕐 Suivre (si accepted ou plus) -->
                            <?php if ($peutSuivre): ?>
                                <a href="/mes-commandes/detail.php?id=<?= $commande['id_commande'] ?>"
                                   class="btn btn--ghost btn--sm">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <?= $isEn ? 'Track' : 'Suivre' ?>
                                </a>
                            <?php endif; ?>

                            <!-- ❌ Annuler (si pending uniquement) -->
                            <?php if ($peutAnnuler): ?>
                                <form method="POST" action="/mes-commandes/annuler.php" style="display:inline;">
                                    <input type="hidden" name="id_commande"
                                           value="<?= $commande['id_commande'] ?>">
                                    <button type="submit"
                                            class="btn btn--ghost btn--sm commande-card__btn-annuler"
                                            onclick="return confirm('<?= $isEn
                                                ? 'Cancel this order?'
                                                : 'Annuler cette commande ?' ?>')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="15" y1="9" x2="9" y2="15"/>
                                            <line x1="9" y1="9" x2="15" y2="15"/>
                                        </svg>
                                        <?= $isEn ? 'Cancel' : 'Annuler' ?>
                                    </button>
                                </form>
                            <?php endif; ?>

                            <!-- ⭐ Avis (si completed) -->
                            <?php if ($peutAvis): ?>
                                <a href="/mes-commandes/avis.php?id=<?= $commande['id_commande'] ?>"
                                   class="btn btn--primary btn--sm">
                                    ⭐ <?= $isEn ? 'Leave a review' : 'Laisser un avis' ?>
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>

                </article>

            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
