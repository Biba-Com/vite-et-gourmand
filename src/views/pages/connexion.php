<?php

/**
 * View: Page Connexion (v2.0)
 * Path: src/views/pages/connexion.php
 *
 * Variables disponibles :
 *  - $error     string|null  Message d'erreur
 *  - $success   string|null  Message flash succès (après inscription)
 *  - $oldData   array        ['email' => '...']
 *  - $csrfToken string       Token CSRF
 *  - $isEn      bool         Langue active
 */
$isEn = ($currentLang ?? 'fr') === 'en';
?>

<div class="auth-page">
    <div class="container auth-page__container">

        <div class="auth-card" data-animate="auth-card">

            <!-- Icône -->
            <div class="auth-card__icon" aria-hidden="true">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
            </div>

            <!-- Titre -->
            <div class="auth-card__header">
                <h1 class="auth-card__title">
                    <?= $isEn ? 'Sign In' : 'Connexion' ?>
                </h1>
                <p class="auth-card__subtitle">
                    <?= $isEn ? 'Access your personal account' : 'Accédez à votre espace personnel' ?>
                </p>
            </div>

            <!-- ✅ Message succès (après inscription réussie) -->
            <?php if (!empty($success)): ?>
                <div class="auth-alert auth-alert--success" role="status" aria-live="polite">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- ❌ Erreur globale (identifiants incorrects, etc.) -->
            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-alert--error" role="alert" aria-live="assertive">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form
                class="auth-form"
                method="POST"
                action="/connexion/"
                novalidate
                aria-label="<?= $isEn ? 'Sign in form' : 'Formulaire de connexion' ?>">

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? $_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <!-- Email -->
                <div class="form-group">
                    <label class="form-label" for="email">
                        Email
                        <span class="form-required" aria-label="<?= $isEn ? 'required' : 'obligatoire' ?>">*</span>
                    </label>
                    <div class="form-input-wrapper">
                        <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="votre@email.fr"
                            value="<?= htmlspecialchars($oldData['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            autocomplete="email"
                            required
                            aria-required="true">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div class="form-group">
                    <label class="form-label" for="password">
                        <?= $isEn ? 'Password' : 'Mot de passe' ?>
                        <span class="form-required" aria-label="<?= $isEn ? 'required' : 'obligatoire' ?>">*</span>
                    </label>
                    <div class="form-input-wrapper">
                        <svg class="form-input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input form-input--password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                            aria-required="true">
                        <button type="button" class="form-input-toggle" aria-label="<?= $isEn ? 'Show password' : 'Afficher le mot de passe' ?>" data-toggle-password="password">
                            <svg class="toggle-icon toggle-icon--show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="toggle-icon toggle-icon--hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember me + Mot de passe oublié -->
                <div class="auth-form__options">
                    <label class="form-checkbox">
                        <input type="checkbox" name="remember" value="1" class="form-checkbox__input">
                        <span class="form-checkbox__box" aria-hidden="true"></span>
                        <span class="form-checkbox__label">
                            <?= $isEn ? 'Remember me' : 'Se souvenir de moi' ?>
                        </span>
                    </label>
                    <a href="/mot-de-passe-oublie/" class="auth-form__forgot">
                        <?= $isEn ? 'Forgot password?' : 'Mot de passe oublié ?' ?>
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit" class="auth-form__submit">
                    <?= $isEn ? 'Sign In' : 'Se connecter' ?>
                </button>

            </form>

            <!-- Séparateur -->
            <div class="auth-card__divider" aria-hidden="true">
                <?= $isEn ? 'or' : 'ou' ?>
            </div>

            <!-- Lien inscription -->
            <p class="auth-card__switch">
                <?= $isEn ? "Don't have an account?" : 'Pas encore de compte ?' ?>
                <a href="/inscription/" class="auth-card__switch-link">
                    <?= $isEn ? 'Create an account' : 'Créer un compte' ?>
                </a>
            </p>

        </div><!-- /.auth-card -->

        <p class="auth-page__back">
            <a href="/" class="auth-page__back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                <?= $isEn ? 'Back to home' : "Retour à l'accueil" ?>
            </a>
        </p>

    </div>
</div>
