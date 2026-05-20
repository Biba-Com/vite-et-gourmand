<?php

/**
 * Vue : Suivi de commande
 * Chemin : src/views/mes-commandes/detail.php
 */

$isEn        = $isEn        ?? false;
$commande    = $commande    ?? [];
$historique  = $historique  ?? [];
$etapes      = $etapes      ?? [];
$statutActuel = $statutActuel ?? '';

$dateEvt     = new DateTime($commande['date_evenement']);
$dateCreated = new DateTime($commande['created_at']);

// Statuts dans l'ordre normal (hors annulation)
$ordreStatuts = ['pending', 'confirmed', 'in_preparation', 'in_delivery', 'completed'];
$isCancelled  = $statutActuel === 'cancelled';

// Trouver l'index du statut actuel
$indexActuel = array_search($statutActuel, $ordreStatuts);
?>

<div class="suivi-page">
    <div class="container">

        <!-- En-tête -->
        <div class="suivi__header">
            <div>
                <a href="/mes-commandes/" class="suivi__back">
                    ← <?= $isEn ? 'My orders' : 'Mes commandes' ?>
                </a>
                <h1 class="suivi__title">
                    <?= $isEn ? 'Order tracking' : 'Suivi de commande' ?>
                    <span class="suivi__num">
                        #<?= str_pad($commande['id_commande'], 4, '0', STR_PAD_LEFT) ?>
                    </span>
                </h1>
            </div>

            <!-- Statut actuel -->
            <?php $etapeActuelle = $etapes[$statutActuel] ?? ['label' => $statutActuel, 'icon' => '•', 'color' => '#6B7280']; ?>
            <span class="suivi__statut-badge"
                  style="--statut-color: <?= $etapeActuelle['color'] ?>">
                <?= $etapeActuelle['icon'] ?> <?= $etapeActuelle['label'] ?>
            </span>
        </div>

        <div class="suivi__layout">

            <!-- ── Timeline ──────────────────────────── -->
            <div class="suivi__main">

                <!-- Barre de progression (si pas annulée) -->
                <?php if (!$isCancelled): ?>
                <div class="suivi__progress" aria-label="Progression de la commande">
                    <?php foreach ($ordreStatuts as $i => $statut):
                        $etape    = $etapes[$statut];
                        $isPast   = $indexActuel !== false && $i <= $indexActuel;
                        $isCurrent = $statut === $statutActuel;
                    ?>
                        <div class="suivi__progress-step <?= $isPast ? 'suivi__progress-step--done' : '' ?> <?= $isCurrent ? 'suivi__progress-step--current' : '' ?>">
                            <div class="suivi__progress-dot"
                                 style="<?= $isPast ? '--dot-color:' . $etape['color'] : '' ?>"
                                 aria-hidden="true">
                                <?= $isPast ? '✓' : ($i + 1) ?>
                            </div>
                            <span class="suivi__progress-label">
                                <?= $etape['icon'] ?> <?= $etape['label'] ?>
                            </span>
                            <?php if ($i < count($ordreStatuts) - 1): ?>
                                <div class="suivi__progress-line <?= ($indexActuel !== false && $i < $indexActuel) ? 'suivi__progress-line--done' : '' ?>"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Timeline historique -->
                <div class="suivi__timeline">
                    <h2 class="suivi__timeline-title">
                        📋 <?= $isEn ? 'Status history' : 'Historique des statuts' ?>
                    </h2>

                    <?php if (empty($historique)): ?>
                        <p style="color:var(--color-gray-400);font-family:var(--font-body);font-size:var(--fs-sm);">
                            <?= $isEn ? 'No history yet.' : 'Aucun historique disponible.' ?>
                        </p>
                    <?php else: ?>
                        <ol class="suivi__timeline-list" reversed>
                            <?php foreach (array_reverse($historique) as $i => $event):
                                $etapeEvt  = $etapes[$event['nouveau_statut']] ?? ['label' => $event['nouveau_statut'], 'icon' => '•', 'color' => '#6B7280'];
                                $dateEvtHist = new DateTime($event['created_at']);
                                $isFirst   = $i === 0;
                            ?>
                                <li class="suivi__timeline-item <?= $isFirst ? 'suivi__timeline-item--latest' : '' ?>">

                                    <div class="suivi__timeline-dot"
                                         style="--dot-color: <?= $etapeEvt['color'] ?>"
                                         aria-hidden="true">
                                        <?= $etapeEvt['icon'] ?>
                                    </div>

                                    <div class="suivi__timeline-content">
                                        <div class="suivi__timeline-header">
                                            <strong class="suivi__timeline-statut"
                                                    style="color: <?= $etapeEvt['color'] ?>">
                                                <?= htmlspecialchars($etapeEvt['label'], ENT_QUOTES, 'UTF-8') ?>
                                            </strong>
                                            <time class="suivi__timeline-date"
                                                  datetime="<?= $event['created_at'] ?>">
                                                <?= $dateEvtHist->format('d/m/Y à H:i') ?>
                                            </time>
                                        </div>

                                        <?php if (!empty($event['motif'])): ?>
                                            <p class="suivi__timeline-motif">
                                                <?= htmlspecialchars($event['motif'], ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        <?php endif; ?>

                                        <?php if (!empty($event['prenom'])): ?>
                                            <span class="suivi__timeline-actor">
                                                👤 <?= htmlspecialchars($event['prenom'] . ' ' . $event['nom'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>

            </div><!-- /.suivi__main -->

            <!-- ── Récapitulatif commande ────────────── -->
            <aside class="suivi__recap">
                <h2 class="suivi__recap-title">
                    <?= $isEn ? 'Order details' : 'Détails de la commande' ?>
                </h2>

                <dl class="suivi__recap-list">
                    <div class="suivi__recap-line">
                        <dt>📦 <?= $isEn ? 'Menu' : 'Menu' ?></dt>
                        <dd><?= htmlspecialchars($commande['menus_titres'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="suivi__recap-line">
                        <dt>📅 <?= $isEn ? 'Event date' : 'Date événement' ?></dt>
                        <dd><?= $dateEvt->format('d/m/Y à H:i') ?></dd>
                    </div>
                    <div class="suivi__recap-line">
                        <dt>📍 <?= $isEn ? 'Address' : 'Adresse' ?></dt>
                        <dd>
                            <?= htmlspecialchars($commande['adresse_livraison'], ENT_QUOTES, 'UTF-8') ?><br>
                            <?= htmlspecialchars($commande['code_postal_livraison'] . ' ' . $commande['ville_livraison'], ENT_QUOTES, 'UTF-8') ?>
                        </dd>
                    </div>
                    <div class="suivi__recap-line">
                        <dt>👥 <?= $isEn ? 'Guests' : 'Personnes' ?></dt>
                        <dd><?= (int) $commande['nb_personnes'] ?></dd>
                    </div>
                    <div class="suivi__recap-line suivi__recap-line--separator"></div>
                    <div class="suivi__recap-line">
                        <dt><?= $isEn ? 'Subtotal' : 'Sous-total' ?></dt>
                        <dd><?= number_format((float) $commande['sous_total'], 2, ',', ' ') ?> €</dd>
                    </div>
                    <?php if ((float) $commande['montant_remise'] > 0): ?>
                    <div class="suivi__recap-line" style="color:var(--color-success)">
                        <dt><?= $isEn ? 'Discount' : 'Remise' ?></dt>
                        <dd>- <?= number_format((float) $commande['montant_remise'], 2, ',', ' ') ?> €</dd>
                    </div>
                    <?php endif; ?>
                    <div class="suivi__recap-line">
                        <dt><?= $isEn ? 'Delivery' : 'Livraison' ?></dt>
                        <dd>
                            <?php if ((float) $commande['frais_livraison'] === 0.0): ?>
                                <span style="color:var(--color-success)">Offerte 🎉</span>
                            <?php else: ?>
                                <?= number_format((float) $commande['frais_livraison'], 2, ',', ' ') ?> €
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="suivi__recap-line suivi__recap-line--total">
                        <dt>Total TTC</dt>
                        <dd><?= number_format((float) $commande['total'], 2, ',', ' ') ?> €</dd>
                    </div>
                </dl>

                <!-- Avis si terminée -->
                <?php if ($statutActuel === 'completed'): ?>
                    <a href="/mes-commandes/avis.php?id=<?= $commande['id_commande'] ?>"
                       class="btn btn--primary" style="width:100%;justify-content:center;margin-top:var(--space-lg);">
                        ⭐ <?= $isEn ? 'Leave a review' : 'Laisser un avis' ?>
                    </a>
                <?php endif; ?>

                <!-- Contact si besoin -->
                <a href="/contact/?commande=<?= $commande['id_commande'] ?>"
                   class="btn btn--ghost" style="width:100%;justify-content:center;margin-top:var(--space-sm);">
                    <?= $isEn ? 'Contact us' : 'Nous contacter' ?>
                </a>

            </aside>

        </div><!-- /.suivi__layout -->
    </div>
</div>
