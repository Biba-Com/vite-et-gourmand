<?php

/**
 * Vue : Page Contact
 * Chemin : src/views/pages/contact.php
 */

$isEn    = $isEn    ?? false;
$errors  = $errors  ?? [];
$success = $success ?? false;
$old     = $old     ?? [];

// Pré-remplir depuis session si connecté
$userPrenom = $_SESSION['user_name'] ?? '';
$userNom    = $_SESSION['user_nom']  ?? '';
$userEmail  = $_SESSION['user_email'] ?? '';
$nomDefaut  = !empty($userPrenom) ? $userPrenom . ' ' . $userNom : ($old['nom'] ?? '');
$emailDefaut = $userEmail ?: ($old['email'] ?? '');

// Sujet pré-rempli si vient d'un menu
$menuSlug  = $_GET['menu']     ?? '';
$commandeId = $_GET['commande'] ?? '';
$sujetDefaut = '';
if ($menuSlug)    $sujetDefaut = 'Demande de devis — menu ' . htmlspecialchars($menuSlug, ENT_QUOTES, 'UTF-8');
if ($commandeId)  $sujetDefaut = 'Question sur ma commande #' . (int) $commandeId;
if (!empty($old['sujet'])) $sujetDefaut = $old['sujet'];
?>

<div class="contact-page">

    <!-- Hero compact -->
    <div class="contact-hero">
        <div class="container">
            <h1 class="contact-hero__title">
                <?= $isEn ? 'Contact us' : 'Nous contacter' ?>
            </h1>
            <p class="contact-hero__subtitle">
                <?= $isEn
                    ? 'A question, a quote request, an event to plan? Our team replies within 24 hours.'
                    : 'Une question, un devis, un événement à planifier ? Notre équipe vous répond sous 24h.' ?>
            </p>
        </div>
    </div>

    <div class="container">
        <div class="contact-layout">

            <!-- ── Formulaire ───────────────────────── -->
            <div class="contact-form-wrapper">

                <!-- Message succès -->
                <?php if ($success): ?>
                    <div class="contact-alert contact-alert--success" role="status">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <!-- Erreur globale -->
                <?php if (!empty($errors['global'])): ?>
                    <div class="contact-alert contact-alert--error" role="alert">
                        <?= htmlspecialchars($errors['global'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form class="contact-form" method="POST" action="/contact/" novalidate
                      aria-label="<?= $isEn ? 'Contact form' : 'Formulaire de contact' ?>">

                    <!-- Nom -->
                    <div class="contact-form__field <?= !empty($errors['nom']) ? 'contact-form__field--error' : '' ?>">
                        <label class="contact-form__label" for="nom_expediteur">
                            <?= $isEn ? 'Your name' : 'Votre nom' ?>
                            <span class="form-required">*</span>
                        </label>
                        <input
                            type="text"
                            id="nom_expediteur"
                            name="nom_expediteur"
                            class="contact-form__input"
                            value="<?= htmlspecialchars($nomDefaut, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Julie Dupont"
                            required aria-required="true"
                            aria-invalid="<?= !empty($errors['nom']) ? 'true' : 'false' ?>">
                        <?php if (!empty($errors['nom'])): ?>
                            <span class="contact-form__error" role="alert">
                                <?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="contact-form__field <?= !empty($errors['email']) ? 'contact-form__field--error' : '' ?>">
                        <label class="contact-form__label" for="email_expediteur">
                            Email
                            <span class="form-required">*</span>
                        </label>
                        <input
                            type="email"
                            id="email_expediteur"
                            name="email_expediteur"
                            class="contact-form__input"
                            value="<?= htmlspecialchars($emailDefaut, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="votre@email.fr"
                            required aria-required="true"
                            autocomplete="email"
                            aria-invalid="<?= !empty($errors['email']) ? 'true' : 'false' ?>">
                        <?php if (!empty($errors['email'])): ?>
                            <span class="contact-form__error" role="alert">
                                <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Téléphone (optionnel) -->
                    <div class="contact-form__field <?= !empty($errors['telephone']) ? 'contact-form__field--error' : '' ?>">
                        <label class="contact-form__label" for="telephone">
                            <?= $isEn ? 'Phone' : 'Téléphone' ?>
                            <span class="contact-form__optional">(<?= $isEn ? 'optional' : 'optionnel' ?>)</span>
                        </label>
                        <input
                            type="tel"
                            id="telephone"
                            name="telephone"
                            class="contact-form__input"
                            value="<?= htmlspecialchars($old['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="06 12 34 56 78"
                            autocomplete="tel">
                        <?php if (!empty($errors['telephone'])): ?>
                            <span class="contact-form__error" role="alert">
                                <?= htmlspecialchars($errors['telephone'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Sujet -->
                    <div class="contact-form__field <?= !empty($errors['sujet']) ? 'contact-form__field--error' : '' ?>">
                        <label class="contact-form__label" for="sujet">
                            <?= $isEn ? 'Subject' : 'Sujet' ?>
                            <span class="form-required">*</span>
                        </label>
                        <input
                            type="text"
                            id="sujet"
                            name="sujet"
                            class="contact-form__input"
                            value="<?= htmlspecialchars($sujetDefaut, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="<?= $isEn ? 'Quote request for a wedding' : 'Demande de devis pour un mariage' ?>"
                            maxlength="200"
                            required aria-required="true"
                            aria-invalid="<?= !empty($errors['sujet']) ? 'true' : 'false' ?>">
                        <?php if (!empty($errors['sujet'])): ?>
                            <span class="contact-form__error" role="alert">
                                <?= htmlspecialchars($errors['sujet'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Message -->
                    <div class="contact-form__field <?= !empty($errors['message']) ? 'contact-form__field--error' : '' ?>">
                        <label class="contact-form__label" for="message">
                            <?= $isEn ? 'Message' : 'Message' ?>
                            <span class="form-required">*</span>
                        </label>
                        <textarea
                            id="message"
                            name="message"
                            class="contact-form__textarea"
                            rows="6"
                            minlength="10"
                            maxlength="2000"
                            placeholder="<?= $isEn
                                ? 'Describe your event, the number of guests, the date...'
                                : 'Décrivez votre événement, le nombre de convives, la date...' ?>"
                            required aria-required="true"
                            aria-invalid="<?= !empty($errors['message']) ? 'true' : 'false' ?>"
                            aria-describedby="message-count"><?= htmlspecialchars($old['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        <span class="contact-form__count" id="message-count" aria-live="polite">
                            0 / 2000
                        </span>
                        <?php if (!empty($errors['message'])): ?>
                            <span class="contact-form__error" role="alert">
                                <?= htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- RGPD -->
                    <p class="contact-form__rgpd">
                        🔒 <?= $isEn
                            ? 'Your data is used solely to respond to your message. See our'
                            : 'Vos données sont utilisées uniquement pour répondre à votre message. Voir notre' ?>
                        <a href="/confidentialite/" target="_blank" rel="noopener">
                            <?= $isEn ? 'privacy policy' : 'politique de confidentialité' ?>
                        </a>.
                    </p>

                    <!-- Submit -->
                    <button type="submit" class="btn btn--primary btn--lg contact-form__submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                        <?= $isEn ? 'Send my message' : 'Envoyer mon message' ?>
                    </button>

                </form>
            </div>

            <!-- ── Infos pratiques ───────────────────── -->
            <aside class="contact-info">

                <div class="contact-info__card">
                    <h2 class="contact-info__title">
                        <?= $isEn ? 'Our details' : 'Nos coordonnées' ?>
                    </h2>

                    <ul class="contact-info__list">
                        <li class="contact-info__item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div>
                                <strong><?= $isEn ? 'Address' : 'Adresse' ?></strong>
                                <span>12 Rue de la Gastronomie<br>33000 Bordeaux, France</span>
                            </div>
                        </li>
                        <li class="contact-info__item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.39 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6 6z"/>
                            </svg>
                            <div>
                                <strong><?= $isEn ? 'Phone' : 'Téléphone' ?></strong>
                                <a href="tel:+33556000001">05 56 00 00 01</a>
                            </div>
                        </li>
                        <li class="contact-info__item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="2" y="4" width="20" height="16" rx="2"/>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                            <div>
                                <strong>Email</strong>
                                <a href="mailto:contact@viteetgourmand.fr">contact@viteetgourmand.fr</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Horaires -->
                <div class="contact-info__card">
                    <h2 class="contact-info__title">
                        <?= $isEn ? 'Opening hours' : 'Horaires' ?>
                    </h2>
                    <ul class="contact-info__hours">
                        <li><span><?= $isEn ? 'Monday–Friday' : 'Lundi–Vendredi' ?></span><span>09h – 18h</span></li>
                        <li><span><?= $isEn ? 'Friday' : 'Vendredi' ?></span><span>09h – 19h</span></li>
                        <li><span><?= $isEn ? 'Saturday' : 'Samedi' ?></span><span>10h – 16h</span></li>
                        <li class="contact-info__hours-closed"><span><?= $isEn ? 'Sunday' : 'Dimanche' ?></span><span><?= $isEn ? 'Closed' : 'Fermé' ?></span></li>
                    </ul>
                </div>

            </aside>

        </div>
    </div>
</div>

<script>
(function () {
    const textarea = document.getElementById('message');
    const counter  = document.getElementById('message-count');

    textarea?.addEventListener('input', () => {
        const len = textarea.value.length;
        if (counter) {
            counter.textContent = len + ' / 2000';
            counter.style.color = len > 1800 ? 'var(--color-bordeaux)' : '';
        }
    });
})();
</script>
