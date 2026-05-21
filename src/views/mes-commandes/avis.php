<?php

/**
 * Vue : Laisser un avis
 * Chemin : src/views/mes-commandes/avis.php
 */

$isEn     = $isEn     ?? false;
$commande = $commande ?? [];
$errors   = $errors   ?? [];
?>

<div class="avis-page">
    <div class="container">

        <div class="avis-card">

            <!-- En-tête -->
            <div class="avis-card__header">
                <a href="/mes-commandes/" class="avis-card__back">
                    ← <?= $isEn ? 'My orders' : 'Mes commandes' ?>
                </a>
                <h1 class="avis-card__title">
                    ⭐ <?= $isEn ? 'Leave a review' : 'Laisser un avis' ?>
                </h1>
                <p class="avis-card__subtitle">
                    <?= $isEn ? 'For your order:' : 'Pour votre commande :' ?>
                    <strong><?= htmlspecialchars($commande['menus_titres'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                </p>
            </div>

            <!-- Erreurs -->
            <?php if (!empty($errors)): ?>
                <div class="avis-alert" role="alert">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form class="avis-form" method="POST"
                  action="/mes-commandes/avis.php" novalidate>

                <input type="hidden" name="id_commande"
                       value="<?= $commande['id_commande'] ?>">

                <!-- Note avec étoiles -->
                <div class="avis-form__field">
                    <label class="avis-form__label">
                        <?= $isEn ? 'Your rating' : 'Votre note' ?>
                        <span class="form-required">*</span>
                    </label>

                    <div class="star-rating" role="group"
                         aria-label="<?= $isEn ? 'Rating out of 5' : 'Note sur 5' ?>">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio"
                                   id="star-<?= $i ?>"
                                   name="note"
                                   value="<?= $i ?>"
                                   class="star-rating__input"
                                   <?= $i === 5 ? 'checked' : '' ?>
                                   required>
                            <label for="star-<?= $i ?>"
                                   class="star-rating__label"
                                   aria-label="<?= $i ?> <?= $isEn ? 'stars' : 'étoiles' ?>">
                                ★
                            </label>
                        <?php endfor; ?>
                    </div>

                    <p class="avis-form__hint" id="note-hint" aria-live="polite">
                        5 <?= $isEn ? 'stars — Excellent!' : 'étoiles — Excellent !' ?>
                    </p>
                </div>

                <!-- Commentaire -->
                <div class="avis-form__field">
                    <label class="avis-form__label" for="commentaire">
                        <?= $isEn ? 'Your comment' : 'Votre commentaire' ?>
                        <span class="form-required">*</span>
                    </label>
                    <textarea
                        id="commentaire"
                        name="commentaire"
                        class="avis-form__textarea"
                        rows="5"
                        minlength="10"
                        maxlength="1000"
                        placeholder="<?= $isEn
                            ? 'Share your experience with our catering team...'
                            : 'Partagez votre expérience avec notre équipe traiteur...' ?>"
                        required
                        aria-required="true"
                        aria-describedby="commentaire-count"></textarea>
                    <span class="avis-form__count" id="commentaire-count" aria-live="polite">
                        0 / 1000
                    </span>
                </div>

                <!-- Note légale modération -->
                <p class="avis-form__legal">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?= $isEn
                        ? 'Your review will be published after moderation by our team.'
                        : 'Votre avis sera publié après validation par notre équipe.' ?>
                </p>

                <!-- Submit -->
                <button type="submit" class="btn btn--primary btn--lg avis-form__submit">
                    ⭐ <?= $isEn ? 'Submit my review' : 'Envoyer mon avis' ?>
                </button>

            </form>

        </div>
    </div>
</div>

<!-- JS étoiles interactives -->
<script>
(function () {
    'use strict';

    const labels = {
        1: '<?= $isEn ? "1 star — Poor" : "1 étoile — Médiocre" ?>',
        2: '<?= $isEn ? "2 stars — Fair" : "2 étoiles — Passable" ?>',
        3: '<?= $isEn ? "3 stars — Good" : "3 étoiles — Bien" ?>',
        4: '<?= $isEn ? "4 stars — Very good" : "4 étoiles — Très bien" ?>',
        5: '<?= $isEn ? "5 stars — Excellent!" : "5 étoiles — Excellent !" ?>',
    };

    const inputs  = document.querySelectorAll('.star-rating__input');
    const hint    = document.getElementById('note-hint');
    const textarea = document.getElementById('commentaire');
    const counter  = document.getElementById('commentaire-count');

    // Mise à jour hint étoiles
    inputs.forEach((input) => {
        input.addEventListener('change', () => {
            if (hint) hint.textContent = labels[input.value] || '';
        });
    });

    // Compteur caractères
    textarea?.addEventListener('input', () => {
        const len = textarea.value.length;
        if (counter) {
            counter.textContent = len + ' / 1000';
            counter.style.color = len > 900 ? 'var(--color-bordeaux)' : '';
        }
    });

})();
</script>
