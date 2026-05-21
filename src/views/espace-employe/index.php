<?php

/**
 * Vue : Espace Employé
 * Chemin : src/views/espace-employe/index.php
 */

$commandes     = $commandes     ?? [];
$avisEnAttente = $avisEnAttente ?? [];
$statutLabels  = $statutLabels  ?? [];
$prochainStatut = $prochainStatut ?? [];
$tab           = $tab           ?? 'commandes';
$filtreStatut  = $filtreStatut  ?? '';
$filtreClient  = $filtreClient  ?? '';
$flashSuccess  = $flashSuccess  ?? null;
$flashError    = $flashError    ?? null;
?>

<div class="employe-page">
    <div class="container">

        <!-- En-tête -->
        <div class="employe__header">
            <div>
                <h1 class="employe__title">
                    🧑‍🍳 Espace Employé
                </h1>
                <p class="employe__subtitle">
                    Bonjour <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> !
                    Gérez les commandes et les avis clients.
                </p>
            </div>
        </div>

        <!-- Alertes -->
        <?php if ($flashSuccess): ?>
            <div class="employe__alert employe__alert--success" role="status">
                ✅ <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="employe__alert employe__alert--error" role="alert">
                ❌ <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Onglets -->
        <div class="employe__tabs" role="tablist">
            <a href="/espace-employe/?tab=commandes"
               class="employe__tab <?= $tab === 'commandes' ? 'employe__tab--active' : '' ?>"
               role="tab" aria-selected="<?= $tab === 'commandes' ? 'true' : 'false' ?>">
                📦 Commandes
                <span class="employe__tab-badge"><?= count($commandes) ?></span>
            </a>
            <a href="/espace-employe/?tab=avis"
               class="employe__tab <?= $tab === 'avis' ? 'employe__tab--active' : '' ?>"
               role="tab" aria-selected="<?= $tab === 'avis' ? 'true' : 'false' ?>">
                ⭐ Avis à modérer
                <?php if (count($avisEnAttente) > 0): ?>
                    <span class="employe__tab-badge employe__tab-badge--alert">
                        <?= count($avisEnAttente) ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>

        <!-- ══════════════════════════════════════════
             ONGLET COMMANDES
        ══════════════════════════════════════════ -->
        <?php if ($tab === 'commandes'): ?>

            <!-- Filtres -->
            <form class="employe__filters" method="GET" action="/espace-employe/">
                <input type="hidden" name="tab" value="commandes">

                <div class="employe__filter-group">
                    <label class="employe__filter-label" for="filtre-statut">Statut</label>
                    <select id="filtre-statut" name="statut" class="employe__filter-select">
                        <option value="">Tous les statuts</option>
                        <?php foreach ($statutLabels as $val => $info): ?>
                            <option value="<?= $val ?>" <?= $filtreStatut === $val ? 'selected' : '' ?>>
                                <?= $info['label'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="employe__filter-group">
                    <label class="employe__filter-label" for="filtre-client">Client</label>
                    <input type="text" id="filtre-client" name="client"
                           class="employe__filter-input"
                           value="<?= htmlspecialchars($filtreClient, ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Nom, prénom ou email...">
                </div>

                <button type="submit" class="btn btn--primary btn--sm">🔍 Filtrer</button>
                <a href="/espace-employe/?tab=commandes" class="btn btn--ghost btn--sm">Réinitialiser</a>
            </form>

            <!-- Tableau commandes -->
            <?php if (empty($commandes)): ?>
                <div class="employe__empty">
                    Aucune commande trouvée avec ces filtres.
                </div>
            <?php else: ?>
                <div class="employe__table-wrapper">
                    <table class="employe__table" aria-label="Liste des commandes">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Client</th>
                                <th scope="col">Menu</th>
                                <th scope="col">Événement</th>
                                <th scope="col">Personnes</th>
                                <th scope="col">Total</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $commande):
                                $statut     = $commande['statut'];
                                $statutInfo = $statutLabels[$statut] ?? ['label' => $statut, 'color' => '#6B7280'];
                                $nextStatut = $prochainStatut[$statut] ?? null;
                                $nextLabel  = $nextStatut ? ($statutLabels[$nextStatut]['label'] ?? $nextStatut) : null;
                                $dateEvt    = new DateTime($commande['date_evenement']);
                            ?>
                            <tr class="employe__table-row">
                                <td class="employe__table-num">
                                    #<?= str_pad($commande['id_commande'], 4, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td>
                                    <div class="employe__client">
                                        <strong><?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span><?= htmlspecialchars($commande['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if (!empty($commande['telephone'])): ?>
                                            <span><?= htmlspecialchars($commande['telephone'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($commande['menus_titres'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= $dateEvt->format('d/m/Y') ?><br>
                                    <small><?= htmlspecialchars($commande['ville_livraison'], ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td><?= (int) $commande['nb_personnes'] ?></td>
                                <td class="employe__table-total">
                                    <?= number_format((float) $commande['total'], 2, ',', ' ') ?> €
                                </td>
                                <td>
                                    <span class="employe__statut-badge"
                                          style="--statut-color: <?= $statutInfo['color'] ?>">
                                        <?= $statutInfo['label'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($nextStatut && $statut !== 'cancelled'): ?>
                                        <!-- Bouton avancer statut -->
                                        <button type="button"
                                                class="btn btn--primary btn--sm"
                                                onclick="openStatutModal(
                                                    <?= $commande['id_commande'] ?>,
                                                    '<?= $nextStatut ?>',
                                                    '<?= htmlspecialchars($nextLabel, ENT_QUOTES, 'UTF-8') ?>'
                                                )">
                                            ➡️ <?= $nextLabel ?>
                                        </button>
                                    <?php endif; ?>

                                    <?php if (in_array($statut, ['pending', 'confirmed']) ): ?>
                                        <!-- Annuler (avec motif obligatoire) -->
                                        <button type="button"
                                                class="btn btn--ghost btn--sm employe__btn-cancel"
                                                onclick="openStatutModal(
                                                    <?= $commande['id_commande'] ?>,
                                                    'cancelled',
                                                    'Annulée'
                                                )">
                                            ❌ Annuler
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <!-- ══════════════════════════════════════════
             ONGLET AVIS
        ══════════════════════════════════════════ -->
        <?php if ($tab === 'avis'): ?>

            <?php if (empty($avisEnAttente)): ?>
                <div class="employe__empty">
                    ✅ Aucun avis en attente de modération.
                </div>
            <?php else: ?>
                <div class="employe__avis-list">
                    <?php foreach ($avisEnAttente as $avis): ?>
                        <div class="employe__avis-card">
                            <div class="employe__avis-header">
                                <div>
                                    <strong><?= htmlspecialchars($avis['prenom'] . ' ' . $avis['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <span class="employe__avis-menu"><?= htmlspecialchars($avis['menu_titre'] ?? '—', ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="employe__avis-date"><?= (new DateTime($avis['created_at']))->format('d/m/Y') ?></span>
                                </div>
                                <!-- Étoiles -->
                                <div class="employe__avis-stars" aria-label="<?= $avis['note'] ?> étoiles sur 5">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span style="color: <?= $i <= $avis['note'] ? '#D4AF37' : '#E5E7EB' ?>">★</span>
                                    <?php endfor; ?>
                                    <strong><?= $avis['note'] ?>/5</strong>
                                </div>
                            </div>

                            <p class="employe__avis-commentaire">
                                "<?= htmlspecialchars($avis['commentaire'], ENT_QUOTES, 'UTF-8') ?>"
                            </p>

                            <div class="employe__avis-actions">
                                <form method="POST" action="/espace-employe/?tab=avis" style="display:inline;">
                                    <input type="hidden" name="action" value="approve_avis">
                                    <input type="hidden" name="id_avis" value="<?= $avis['id_avis'] ?>">
                                    <button type="submit" class="btn btn--primary btn--sm">
                                        ✅ Valider
                                    </button>
                                </form>
                                <form method="POST" action="/espace-employe/?tab=avis" style="display:inline;">
                                    <input type="hidden" name="action" value="reject_avis">
                                    <input type="hidden" name="id_avis" value="<?= $avis['id_avis'] ?>">
                                    <button type="submit" class="btn btn--ghost btn--sm employe__btn-cancel">
                                        ❌ Refuser
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

<!-- ── Modale changement statut ──────────────────────────── -->
<div class="employe-modal" id="statut-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="employe-modal__overlay" onclick="closeStatutModal()"></div>
    <div class="employe-modal__panel">
        <h2 class="employe-modal__title" id="modal-title">Mettre à jour le statut</h2>

        <form method="POST" action="/espace-employe/?tab=commandes">
            <input type="hidden" name="action" value="update_statut">
            <input type="hidden" name="id_commande" id="modal-id-commande" value="">
            <input type="hidden" name="nouveau_statut" id="modal-nouveau-statut" value="">

            <p class="employe-modal__info">
                Nouveau statut : <strong id="modal-statut-label"></strong>
            </p>

            <div class="employe-modal__field">
                <label for="modal-motif">Motif / Note interne</label>
                <textarea id="modal-motif" name="motif" rows="3"
                          placeholder="Ex: Client contacté par téléphone, livraison confirmée..."
                          class="employe-modal__textarea"></textarea>
            </div>

            <div class="employe-modal__actions">
                <button type="submit" class="btn btn--primary">✅ Confirmer</button>
                <button type="button" class="btn btn--ghost" onclick="closeStatutModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatutModal(idCommande, nouveauStatut, label) {
    document.getElementById('modal-id-commande').value    = idCommande;
    document.getElementById('modal-nouveau-statut').value = nouveauStatut;
    document.getElementById('modal-statut-label').textContent = label;
    document.getElementById('modal-motif').value          = '';

    const modal = document.getElementById('statut-modal');
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('employe-modal--open');
    document.body.style.overflow = 'hidden';
    document.getElementById('modal-motif').focus();
}

function closeStatutModal() {
    const modal = document.getElementById('statut-modal');
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('employe-modal--open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeStatutModal();
});
</script>
