<?php

/**
 * Vue : Espace Administrateur
 * Chemin : src/views/espace-admin/index.php
 */

$tab           = $tab           ?? 'employes';
$employes      = $employes      ?? [];
$commandes     = $commandes     ?? [];
$avisEnAttente = $avisEnAttente ?? [];
$statsMenus    = $statsMenus    ?? [];
$statutLabels  = $statutLabels  ?? [];
$prochainStatut = $prochainStatut ?? [];
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
                <h1 class="employe__title">👑 Espace Administrateur</h1>
                <p class="employe__subtitle">
                    Bonjour <?= htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> !
                    Gestion complète de l'application.
                </p>
            </div>
            <a href="/espace-employe/" class="btn btn--ghost btn--sm">
                🧑‍🍳 Vue Employé
            </a>
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
            <a href="/espace-admin/?tab=employes"
               class="employe__tab <?= $tab === 'employes' ? 'employe__tab--active' : '' ?>">
                👥 Employés
                <span class="employe__tab-badge"><?= count($employes) ?></span>
            </a>
            <a href="/espace-admin/?tab=commandes"
               class="employe__tab <?= $tab === 'commandes' ? 'employe__tab--active' : '' ?>">
                📦 Commandes
                <span class="employe__tab-badge"><?= count($commandes) ?></span>
            </a>
            <a href="/espace-admin/?tab=avis"
               class="employe__tab <?= $tab === 'avis' ? 'employe__tab--active' : '' ?>">
                ⭐ Avis
                <?php if (count($avisEnAttente) > 0): ?>
                    <span class="employe__tab-badge employe__tab-badge--alert">
                        <?= count($avisEnAttente) ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="/espace-admin/?tab=stats"
               class="employe__tab <?= $tab === 'stats' ? 'employe__tab--active' : '' ?>">
                📊 Statistiques
            </a>
        </div>

        <!-- ══════════════════════════════════════════
             ONGLET EMPLOYÉS
        ══════════════════════════════════════════ -->
        <?php if ($tab === 'employes'): ?>

            <div class="admin-section">

                <!-- Créer un employé -->
                <div class="admin-create-card">
                    <h2 class="admin-create-card__title">
                        ➕ Créer un compte employé
                    </h2>
                    <form method="POST" action="/espace-admin/?tab=employes"
                          class="admin-create-form" novalidate>
                        <input type="hidden" name="action" value="create_employee">

                        <div class="admin-create-form__row">
                            <div class="admin-create-form__field">
                                <label for="emp-prenom">Prénom *</label>
                                <input type="text" id="emp-prenom" name="prenom"
                                       class="admin-create-form__input"
                                       placeholder="Julie" required>
                            </div>
                            <div class="admin-create-form__field">
                                <label for="emp-nom">Nom *</label>
                                <input type="text" id="emp-nom" name="nom"
                                       class="admin-create-form__input"
                                       placeholder="Lartigue" required>
                            </div>
                        </div>

                        <div class="admin-create-form__row">
                            <div class="admin-create-form__field">
                                <label for="emp-email">Email (identifiant) *</label>
                                <input type="email" id="emp-email" name="email"
                                       class="admin-create-form__input"
                                       placeholder="employe@viteetgourmand.fr" required>
                            </div>
                            <div class="admin-create-form__field">
                                <label for="emp-password">
                                    Mot de passe *
                                    <span style="font-weight:normal;color:var(--color-gray-400);font-size:var(--fs-xs);">
                                        (ne sera pas envoyé par mail)
                                    </span>
                                </label>
                                <input type="password" id="emp-password" name="password"
                                       class="admin-create-form__input"
                                       placeholder="Min. 8 caractères" required minlength="8">
                            </div>
                        </div>

                        <p class="admin-create-form__note">
                            ℹ️ Conformément à l'énoncé, le mot de passe ne sera pas envoyé par mail.
                            L'employé devra se rapprocher de José pour l'obtenir.
                        </p>

                        <button type="submit" class="btn btn--primary">
                            ✅ Créer le compte
                        </button>
                    </form>
                </div>

                <!-- Liste des employés -->
                <div class="employe__table-wrapper" style="margin-top:var(--space-xl)">
                    <table class="employe__table" aria-label="Liste des employés">
                        <thead>
                            <tr>
                                <th scope="col">Employé</th>
                                <th scope="col">Email</th>
                                <th scope="col">Rôle</th>
                                <th scope="col">Créé le</th>
                                <th scope="col">Statut</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employes as $emp): ?>
                            <tr class="employe__table-row">
                                <td>
                                    <strong><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </td>
                                <td><?= htmlspecialchars($emp['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="employe__statut-badge"
                                          style="--statut-color: <?= $emp['role'] === 'admin' ? '#D4AF37' : '#3B82F6' ?>">
                                        <?= $emp['role'] === 'admin' ? '👑 Admin' : '🧑‍🍳 Employé' ?>
                                    </span>
                                </td>
                                <td><?= (new DateTime($emp['created_at']))->format('d/m/Y') ?></td>
                                <td>
                                    <?php if ($emp['is_active']): ?>
                                        <span style="color:var(--color-success);font-weight:600;">✅ Actif</span>
                                    <?php else: ?>
                                        <span style="color:var(--color-bordeaux);font-weight:600;">🔴 Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($emp['role'] !== 'admin'): ?>
                                        <form method="POST" action="/espace-admin/?tab=employes"
                                              style="display:inline;">
                                            <input type="hidden" name="action" value="toggle_employe">
                                            <input type="hidden" name="id_utilisateur"
                                                   value="<?= $emp['id_utilisateur'] ?>">
                                            <input type="hidden" name="new_status"
                                                   value="<?= $emp['is_active'] ? '0' : '1' ?>">
                                            <button type="submit"
                                                    class="btn btn--sm <?= $emp['is_active'] ? 'employe__btn-cancel' : 'btn--primary' ?>"
                                                    onclick="return confirm('<?= $emp['is_active'] ? 'Désactiver ce compte ?' : 'Réactiver ce compte ?' ?>')">
                                                <?= $emp['is_active'] ? '🔴 Désactiver' : '✅ Réactiver' ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:var(--color-gray-400);font-size:var(--fs-xs);">Protégé</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

        <!-- ══════════════════════════════════════════
             ONGLET COMMANDES (identique espace employé)
        ══════════════════════════════════════════ -->
        <?php if ($tab === 'commandes'): ?>

            <form class="employe__filters" method="GET" action="/espace-admin/">
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
                <a href="/espace-admin/?tab=commandes" class="btn btn--ghost btn--sm">Réinitialiser</a>
            </form>

            <?php if (empty($commandes)): ?>
                <div class="employe__empty">Aucune commande trouvée.</div>
            <?php else: ?>
                <div class="employe__table-wrapper">
                    <table class="employe__table">
                        <thead>
                            <tr>
                                <th>#</th><th>Client</th><th>Menu</th>
                                <th>Événement</th><th>Pers.</th>
                                <th>Total</th><th>Statut</th><th>Actions</th>
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
                                <td class="employe__table-num">#<?= str_pad($commande['id_commande'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td>
                                    <div class="employe__client">
                                        <strong><?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span><?= htmlspecialchars($commande['email'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($commande['menus_titres'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= $dateEvt->format('d/m/Y') ?><br><small><?= htmlspecialchars($commande['ville_livraison'], ENT_QUOTES, 'UTF-8') ?></small></td>
                                <td><?= (int) $commande['nb_personnes'] ?></td>
                                <td class="employe__table-total"><?= number_format((float) $commande['total'], 2, ',', ' ') ?> €</td>
                                <td>
                                    <span class="employe__statut-badge" style="--statut-color: <?= $statutInfo['color'] ?>">
                                        <?= $statutInfo['label'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($nextStatut && $statut !== 'cancelled'): ?>
                                        <button type="button" class="btn btn--primary btn--sm"
                                                onclick="openStatutModal(<?= $commande['id_commande'] ?>, '<?= $nextStatut ?>', '<?= htmlspecialchars($nextLabel, ENT_QUOTES, 'UTF-8') ?>', 'admin')">
                                            ➡️ <?= $nextLabel ?>
                                        </button>
                                    <?php endif; ?>
                                    <?php if (in_array($statut, ['pending', 'confirmed'])): ?>
                                        <button type="button" class="btn btn--ghost btn--sm employe__btn-cancel"
                                                onclick="openStatutModal(<?= $commande['id_commande'] ?>, 'cancelled', 'Annulée', 'admin')">
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
                <div class="employe__empty">✅ Aucun avis en attente.</div>
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
                                <div class="employe__avis-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span style="color: <?= $i <= $avis['note'] ? '#D4AF37' : '#E5E7EB' ?>">★</span>
                                    <?php endfor; ?>
                                    <strong><?= $avis['note'] ?>/5</strong>
                                </div>
                            </div>
                            <p class="employe__avis-commentaire">"<?= htmlspecialchars($avis['commentaire'], ENT_QUOTES, 'UTF-8') ?>"</p>
                            <div class="employe__avis-actions">
                                <form method="POST" action="/espace-admin/?tab=avis" style="display:inline;">
                                    <input type="hidden" name="action" value="approve_avis">
                                    <input type="hidden" name="id_avis" value="<?= $avis['id_avis'] ?>">
                                    <button type="submit" class="btn btn--primary btn--sm">✅ Valider</button>
                                </form>
                                <form method="POST" action="/espace-admin/?tab=avis" style="display:inline;">
                                    <input type="hidden" name="action" value="reject_avis">
                                    <input type="hidden" name="id_avis" value="<?= $avis['id_avis'] ?>">
                                    <button type="submit" class="btn btn--ghost btn--sm employe__btn-cancel">❌ Refuser</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- ══════════════════════════════════════════
             ONGLET STATISTIQUES
        ══════════════════════════════════════════ -->
        <?php if ($tab === 'stats'): ?>

            <div class="admin-stats">
                <h2 class="admin-stats__title">📊 Commandes & Chiffre d'affaires par menu</h2>

                <!-- Tableau stats -->
                <div class="employe__table-wrapper">
                    <table class="employe__table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Nb commandes</th>
                                <th>CA total (€)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statsMenus as $stat): ?>
                            <tr class="employe__table-row">
                                <td><strong><?= htmlspecialchars($stat['titre'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= (int) $stat['nb_commandes'] ?></td>
                                <td class="employe__table-total">
                                    <?= number_format((float) ($stat['ca_total'] ?? 0), 2, ',', ' ') ?> €
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Graphique en barres (Canvas JS) -->
                <div class="admin-chart">
                    <h3 class="admin-chart__title">Nombre de commandes par menu</h3>
                    <canvas id="chartCommandes" height="300" aria-label="Graphique commandes par menu" role="img"></canvas>
                </div>

                <!-- CA Total -->
                <?php
                $caTotal = array_sum(array_column($statsMenus, 'ca_total'));
                ?>
                <div class="admin-stats__total">
                    <span>Chiffre d'affaires total (hors annulations)</span>
                    <strong><?= number_format($caTotal, 2, ',', ' ') ?> €</strong>
                </div>
            </div>

            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
            (function () {
                const labels = <?= json_encode(array_column($statsMenus, 'titre')) ?>;
                const data   = <?= json_encode(array_map('intval', array_column($statsMenus, 'nb_commandes'))) ?>;
                const ca     = <?= json_encode(array_map('floatval', array_column($statsMenus, 'ca_total'))) ?>;

                const ctx = document.getElementById('chartCommandes');
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nombre de commandes',
                            data: data,
                            backgroundColor: 'rgba(6, 58, 31, 0.7)',
                            borderColor: '#063A1F',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    afterLabel: (ctx) => {
                                        const caVal = ca[ctx.dataIndex] || 0;
                                        return 'CA : ' + caVal.toLocaleString('fr-FR', {
                                            minimumFractionDigits: 2
                                        }) + ' €';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 30,
                                    font: { size: 11 }
                                }
                            }
                        }
                    }
                });
            })();
            </script>

        <?php endif; ?>

    </div>
</div>

<!-- Modale statut (réutilisée depuis espace employé) -->
<div class="employe-modal" id="statut-modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="employe-modal__overlay" onclick="closeStatutModal()"></div>
    <div class="employe-modal__panel">
        <h2 class="employe-modal__title">Mettre à jour le statut</h2>
        <form method="POST" id="statut-form" action="/espace-admin/?tab=commandes">
            <input type="hidden" name="action" value="update_statut">
            <input type="hidden" name="id_commande" id="modal-id-commande">
            <input type="hidden" name="nouveau_statut" id="modal-nouveau-statut">
            <p class="employe-modal__info">
                Nouveau statut : <strong id="modal-statut-label"></strong>
            </p>
            <div class="employe-modal__field">
                <label for="modal-motif">Motif / Note interne</label>
                <textarea id="modal-motif" name="motif" rows="3"
                          class="employe-modal__textarea"
                          placeholder="Raison du changement..."></textarea>
            </div>
            <div class="employe-modal__actions">
                <button type="submit" class="btn btn--primary">✅ Confirmer</button>
                <button type="button" class="btn btn--ghost" onclick="closeStatutModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStatutModal(id, statut, label, space) {
    document.getElementById('modal-id-commande').value    = id;
    document.getElementById('modal-nouveau-statut').value = statut;
    document.getElementById('modal-statut-label').textContent = label;
    document.getElementById('modal-motif').value = '';
    if (space === 'admin') {
        document.getElementById('statut-form').action = '/espace-admin/?tab=commandes';
    }
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
