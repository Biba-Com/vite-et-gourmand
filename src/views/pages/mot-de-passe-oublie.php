<?php

/**
 * Vue : Mot de passe oublié
 * Chemin : src/views/pages/mot-de-passe-oublie.php
 */

$isEn    = $isEn    ?? false;
$errors  = $errors  ?? [];
$success = $success ?? false;
$old     = $old     ?? [];

// Lien dev (affiché uniquement en local)
$resetLinkDev = $_SESSION['reset_link_dev'] ?? null;
if ($resetLinkDev) unset($_SESSION['reset_link_dev']);
?>

<div class="auth-page">
    <div class="container auth-page__container">
        <div class="auth-card">

            <!-- Logo -->
            <div class="auth-card__logo" aria-hidden="true">
                <svg width="56" height="56" viewBox="0 0 44 44" fill="none">
                    <circle cx="22" cy="22" r="21" fill="#063A1F" />
                    <text x="22" y="19" text-anchor="middle" dominant-baseline="central"
                        fill="#D4AF37" font-family="Georgia,serif" font-size="12" font-weight="700">V&amp;G</text>
                    <text x="22" y="32" text-anchor="middle" dominant-baseline="central"
                        fill="#F9F9F7" font-family="sans-serif" font-size="5" letter-spacing="2" fill-opacity="0.75">TRAITEUR</text>
                </svg>
            </div>

            <?php if (!$success): ?>

                <!-- Formulaire -->
                <div class="auth-card__header">
                    <h1 class="auth-card__title">
                        <?= $isEn ? 'Forgot password' : 'Mot de passe oublié' ?>
                    </h1>
                    <p class="auth-card__subtitle">
                        <?= $isEn
                            ? 'Enter your email and we\'ll send you a reset link.'
                            : 'Saisissez votre email et nous vous enverrons un lien de réinitialisation.' ?>
                    </p>
                </div>

                <?php if (!empty($errors['email'])): ?>
                    <div class="auth-alert auth-alert--error" role="alert">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST"
                    action="/mot-de-passe-oublie/" novalidate>

                    <div class="auth-form__field <?= !empty($errors['email']) ? 'auth-form__field--error' : '' ?>">
                        <label class="auth-form__label" for="email">
                            Email
                            <span class="form-required" aria-label="obligatoire">*</span>
                        </label>
                        <div class="auth-form__input-wrapper">
                            <svg class="auth-form__input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="auth-form__input"
                                value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="votre@email.fr"
                                required
                                aria-required="true"
                                autocomplete="email">
                        </div>
                    </div>

                    <button type="submit" class="btn btn--primary btn--lg auth-form__submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                        <?= $isEn ? 'Send reset link' : 'Envoyer le lien' ?>
                    </button>

                </form>

                <div class="auth-card__footer">
                    <a href="/connexion/" class="auth-card__link">
                        ← <?= $isEn ? 'Back to login' : 'Retour à la connexion' ?>
                    </a>
                </div>

            <?php else: ?>

                <!-- Message succès -->
                <div class="auth-card__header">
                    <div class="auth-success-icon" aria-hidden="true">📧</div>
                    <h1 class="auth-card__title">
                        <?= $isEn ? 'Check your inbox!' : 'Vérifiez votre boîte mail !' ?>
                    </h1>
                    <p class="auth-card__subtitle">
                        <?= $isEn
                            ? 'If this email is registered, a reset link has been sent. Check your spam folder.'
                            : 'Si cet email est enregistré, un lien de réinitialisation a été envoyé. Vérifiez vos spams.' ?>
                    </p>
                </div>

                <!-- Lien dev uniquement (en local) -->
                <?php if ($resetLinkDev): ?>
                    <div class="auth-alert auth-alert--info" role="status">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <div>
                            <strong>Mode développement</strong> — Lien de réinitialisation :
                            <br>
                            <a href="<?= htmlspecialchars($resetLinkDev, ENT_QUOTES, 'UTF-8') ?>"
                                class="auth-reset-link">
                                <?= htmlspecialchars($resetLinkDev, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <br>
                            <small>(Ce lien n'apparaîtra pas en production)</small>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="auth-card__footer">
                    <a href="/connexion/" class="btn btn--ghost">
                        <?= $isEn ? 'Back to login' : 'Retour à la connexion' ?>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </div>
</div>