<?php

/**
 * View: Page Inscription
 * Path: src/views/pages/inscription.php
 */
$isEn = ($currentLang ?? 'fr') === 'en';
?>

<div class="auth-page">
    <div class="container auth-page__container">

        <div class="auth-card auth-card--register" data-animate="auth-card">

            <!-- Icône -->
            <div class="auth-card__icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="19" y1="8" x2="19" y2="14" />
                    <line x1="22" y1="11" x2="16" y2="11" />
                </svg>
            </div>

            <!-- Titre -->
            <div class="auth-card__header">
                <h1 class="auth-card__title">
                    <?= $isEn ? 'Create an account' : 'Créer un compte' ?>
                </h1>
                <p class="auth-card__subtitle">
                    <?= $isEn
                        ? 'Join Vite & Gourmand and manage your orders'
                        : 'Rejoignez Vite & Gourmand et gérez vos commandes' ?>
                </p>
            </div>

            <!-- Erreur globale -->
            <?php if (!empty($errors['global'])): ?>
                <div class="auth-alert auth-alert--error" role="alert" aria-live="assertive">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <?= htmlspecialchars($errors['global'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form
                class="auth-form"
                method="POST"
                action="/inscription"
                novalidate
                aria-label="<?= $isEn ? 'Registration form' : 'Formulaire d\'inscription' ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <!-- Prénom + Nom (2 colonnes) -->
                <div class="form-row">
                    <div class="form-group <?= !empty($errors['prenom']) ? 'form-group--error' : '' ?>">
                        <label class="form-label" for="prenom">
                            <?= $isEn ? 'First name' : 'Prénom' ?>
                            <span class="form-required" aria-label="obligatoire">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input
                                type="text"
                                id="prenom"
                                name="prenom"
                                class="form-input"
                                placeholder="Marie"
                                value="<?= htmlspecialchars($oldData['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                autocomplete="given-name"
                                required
                                aria-required="true"
                                aria-invalid="<?= !empty($errors['prenom']) ? 'true' : 'false' ?>">
                        </div>
                        <?php if (!empty($errors['prenom'])): ?>
                            <p class="form-error" role="alert"><?= htmlspecialchars($errors['prenom'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?= !empty($errors['nom']) ? 'form-group--error' : '' ?>">
                        <label class="form-label" for="nom">
                            <?= $isEn ? 'Last name' : 'Nom' ?>
                            <span class="form-required" aria-label="obligatoire">*</span>
                        </label>
                        <div class="form-input-wrapper">
                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                class="form-input"
                                placeholder="Dupont"
                                value="<?= htmlspecialchars($oldData['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                autocomplete="family-name"
                                required
                                aria-required="true"
                                aria-invalid="<?= !empty($errors['nom']) ? 'true' : 'false' ?>">
                        </div>
                        <?php if (!empty($errors['nom'])): ?>
                            <p class="form-error" role="alert"><?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group <?= !empty($errors['email']) ? 'form-group--error' : '' ?>">
                    <label class="form-label" for="reg-email">
                        Email
                        <span class="form-required" aria-label="obligatoire">*</span>
                    </label>
                    <div class="form-input-wrapper">
                        <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                        <input
                            type="email"
                            id="reg-email"
                            name="email"
                            class="form-input"
                            placeholder="votre@email.fr"
                            value="<?= htmlspecialchars($oldData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="email"
                            required
                            aria-required="true"
                            aria-invalid="<?= !empty($errors['email']) ? 'true' : 'false' ?>">
                    </div>
                    <?php if (!empty($errors['email'])): ?>
                        <p class="form-error" role="alert"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Téléphone (optionnel) -->
                <div class="form-group">
                    <label class="form-label" for="telephone">
                        <?= $isEn ? 'Phone' : 'Téléphone' ?>
                        <span class="form-optional">(<?= $isEn ? 'optional' : 'optionnel' ?>)</span>
                    </label>
                    <div class="form-input-wrapper">
                        <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.39 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6 6z" />
                        </svg>
                        <input
                            type="tel"
                            id="telephone"
                            name="telephone"
                            class="form-input"
                            placeholder="06 12 34 56 78"
                            value="<?= htmlspecialchars($oldData['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="tel"
                            pattern="[0-9\s\+\-\(\)]{10,15}">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="form-group <?= !empty($errors['password']) ? 'form-group--error' : '' ?>">
                    <label class="form-label" for="reg-password">
                        <?= $isEn ? 'Password' : 'Mot de passe' ?>
                        <span class="form-required" aria-label="obligatoire">*</span>
                    </label>
                    <div class="form-input-wrapper">
                        <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="11" width="18" height="11" rx="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input
                            type="password"
                            id="reg-password"
                            name="password"
                            class="form-input form-input--password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            required
                            aria-required="true"
                            aria-describedby="password-strength-desc"
                            data-password-strength>
                        <button type="button" class="form-input-toggle" aria-label="Afficher le mot de passe" data-toggle-password="reg-password">
                            <svg class="toggle-icon toggle-icon--show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg class="toggle-icon toggle-icon--hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>

                    <!-- Barre de force du mot de passe -->
                    <div class="password-strength" aria-live="polite">
                        <div class="password-strength__bar" id="strength-bar" aria-hidden="true">
                            <span class="password-strength__segment"></span>
                            <span class="password-strength__segment"></span>
                            <span class="password-strength__segment"></span>
                            <span class="password-strength__segment"></span>
                        </div>
                        <span class="password-strength__label" id="password-strength-desc"></span>
                    </div>

                    <?php if (!empty($errors['password'])): ?>
                        <p class="form-error" role="alert"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Confirmation mot de passe -->
                <div class="form-group <?= !empty($errors['password_confirm']) ? 'form-group--error' : '' ?>">
                    <label class="form-label" for="password-confirm">
                        <?= $isEn ? 'Confirm password' : 'Confirmer le mot de passe' ?>
                        <span class="form-required" aria-label="obligatoire">*</span>
                    </label>
                    <div class="form-input-wrapper">
                        <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        <input
                            type="password"
                            id="password-confirm"
                            name="password_confirm"
                            class="form-input form-input--password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            required
                            aria-required="true"
                            data-match="reg-password">
                    </div>
                    <?php if (!empty($errors['password_confirm'])): ?>
                        <p class="form-error" role="alert"><?= htmlspecialchars($errors['password_confirm'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <!-- CGV -->
                <div class="form-group <?= !empty($errors['cgv']) ? 'form-group--error' : '' ?>">
                    <label class="form-checkbox">
                        <input
                            type="checkbox"
                            name="cgv"
                            value="1"
                            class="form-checkbox__input"
                            required
                            aria-required="true"
                            aria-invalid="<?= !empty($errors['cgv']) ? 'true' : 'false' ?>">
                        <span class="form-checkbox__box" aria-hidden="true"></span>
                        <span class="form-checkbox__label">
                            <?= $isEn ? 'I accept the' : "J'accepte les" ?>
                            <a href="/mentions-legales" class="form-link" target="_blank" rel="noopener">
                                <?= $isEn ? 'Terms of Service' : 'Conditions Générales' ?>
                            </a>
                            <?= $isEn ? 'and the' : 'et la' ?>
                            <a href="/confidentialite" class="form-link" target="_blank" rel="noopener">
                                <?= $isEn ? 'Privacy Policy' : 'Politique de confidentialité' ?>
                            </a>
                        </span>
                    </label>
                    <?php if (!empty($errors['cgv'])): ?>
                        <p class="form-error" role="alert"><?= htmlspecialchars($errors['cgv'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn--primary auth-form__submit">
                    <?= $isEn ? 'Create my account' : 'Créer mon compte' ?>
                </button>

            </form>

            <!-- Lien connexion -->
            <p class="auth-card__switch">
                <?= $isEn ? 'Already have an account?' : 'Déjà un compte ?' ?>
                <a href="/connexion" class="auth-card__switch-link">
                    <?= $isEn ? 'Sign in' : 'Se connecter' ?>
                </a>
            </p>

        </div><!-- /.auth-card -->

        <p class="auth-page__back">
            <a href="/" class="auth-page__back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                <?= $isEn ? 'Back to home' : "Retour à l'accueil" ?>
            </a>
        </p>

    </div>
</div>