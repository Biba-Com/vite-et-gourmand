<?php

/**
 * Vue : Réinitialiser le mot de passe
 * Chemin : src/views/pages/reinitialiser-mot-de-passe.php
 */

$isEn         = $isEn         ?? false;
$errors       = $errors       ?? [];
$success      = $success      ?? false;
$tokenInvalid = $tokenInvalid ?? false;
$token        = $token        ?? '';
$user         = $user         ?? null;
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

            <?php if ($tokenInvalid): ?>
                <!-- Token invalide ou expiré -->
                <div class="auth-card__header">
                    <div class="auth-success-icon" aria-hidden="true">⛔</div>
                    <h1 class="auth-card__title">Lien invalide</h1>
                    <p class="auth-card__subtitle">
                        Ce lien de réinitialisation est invalide ou a expiré (validité 1 heure).
                    </p>
                </div>
                <a href="/mot-de-passe-oublie/" class="btn btn--primary" style="width:100%;justify-content:center;">
                    Redemander un lien
                </a>

            <?php elseif ($success): ?>
                <!-- Succès -->
                <div class="auth-card__header">
                    <div class="auth-success-icon" aria-hidden="true">✅</div>
                    <h1 class="auth-card__title">Mot de passe mis à jour !</h1>
                    <p class="auth-card__subtitle">
                        Votre mot de passe a été réinitialisé avec succès.
                        Vous allez être redirigé vers la connexion dans 3 secondes...
                    </p>
                </div>
                <a href="/connexion/" class="btn btn--primary" style="width:100%;justify-content:center;">
                    Se connecter maintenant
                </a>

            <?php else: ?>
                <!-- Formulaire nouveau mot de passe -->
                <div class="auth-card__header">
                    <h1 class="auth-card__title">
                        <?= $isEn ? 'New password' : 'Nouveau mot de passe' ?>
                    </h1>
                    <p class="auth-card__subtitle">
                        <?= $isEn
                            ? 'Hello ' . htmlspecialchars($user['prenom'] ?? '', ENT_QUOTES, 'UTF-8') . '! Choose a strong password.'
                            : 'Bonjour ' . htmlspecialchars($user['prenom'] ?? '', ENT_QUOTES, 'UTF-8') . ' ! Choisissez un mot de passe sécurisé.' ?>
                    </p>
                </div>

                <!-- Règles mot de passe -->
                <div class="auth-password-rules" aria-label="Règles du mot de passe">
                    <p class="auth-password-rules__title">Le mot de passe doit contenir :</p>
                    <ul>
                        <li id="rule-length">✗ Au moins 10 caractères</li>
                        <li id="rule-upper">✗ Une majuscule</li>
                        <li id="rule-lower">✗ Une minuscule</li>
                        <li id="rule-digit">✗ Un chiffre</li>
                        <li id="rule-special">✗ Un caractère spécial</li>
                    </ul>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="auth-alert auth-alert--error" role="alert">
                        <?php foreach ($errors as $err): ?>
                            <p><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="POST"
                    action="/reinitialiser-mot-de-passe/" novalidate>

                    <input type="hidden" name="token"
                        value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

                    <!-- Nouveau mot de passe -->
                    <div class="auth-form__field <?= !empty($errors['password']) ? 'auth-form__field--error' : '' ?>">
                        <label class="auth-form__label" for="password">
                            <?= $isEn ? 'New password' : 'Nouveau mot de passe' ?>
                            <span class="form-required">*</span>
                        </label>
                        <div class="auth-form__input-wrapper">
                            <svg class="auth-form__input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="auth-form__input"
                                placeholder="••••••••••"
                                minlength="10"
                                required
                                aria-required="true"
                                aria-describedby="rule-length rule-upper rule-lower rule-digit rule-special">
                            <button type="button" class="auth-form__eye"
                                aria-label="Afficher/masquer le mot de passe"
                                onclick="togglePassword('password', this)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmation -->
                    <div class="auth-form__field <?= !empty($errors['password_confirm']) ? 'auth-form__field--error' : '' ?>">
                        <label class="auth-form__label" for="password_confirm">
                            <?= $isEn ? 'Confirm password' : 'Confirmer le mot de passe' ?>
                            <span class="form-required">*</span>
                        </label>
                        <div class="auth-form__input-wrapper">
                            <svg class="auth-form__input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input
                                type="password"
                                id="password_confirm"
                                name="password_confirm"
                                class="auth-form__input"
                                placeholder="••••••••••"
                                required
                                aria-required="true">
                        </div>
                        <?php if (!empty($errors['password_confirm'])): ?>
                            <span class="auth-form__error" role="alert">
                                <?= htmlspecialchars($errors['password_confirm'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn--primary btn--lg auth-form__submit">
                        🔑 <?= $isEn ? 'Reset my password' : 'Réinitialiser mon mot de passe' ?>
                    </button>

                </form>

                <!-- JS validation temps réel -->
                <script>
                    (function() {
                        const input = document.getElementById('password');
                        const confirm = document.getElementById('password_confirm');
                        const rules = {
                            length: {
                                el: document.getElementById('rule-length'),
                                test: v => v.length >= 10
                            },
                            upper: {
                                el: document.getElementById('rule-upper'),
                                test: v => /[A-Z]/.test(v)
                            },
                            lower: {
                                el: document.getElementById('rule-lower'),
                                test: v => /[a-z]/.test(v)
                            },
                            digit: {
                                el: document.getElementById('rule-digit'),
                                test: v => /[0-9]/.test(v)
                            },
                            special: {
                                el: document.getElementById('rule-special'),
                                test: v => /[\W_]/.test(v)
                            },
                        };

                        const labels = {
                            length: 'Au moins 10 caractères',
                            upper: 'Une majuscule',
                            lower: 'Une minuscule',
                            digit: 'Un chiffre',
                            special: 'Un caractère spécial',
                        };

                        input?.addEventListener('input', () => {
                            const val = input.value;
                            Object.entries(rules).forEach(([key, rule]) => {
                                if (!rule.el) return;
                                const ok = rule.test(val);
                                rule.el.textContent = (ok ? '✓ ' : '✗ ') + labels[key];
                                rule.el.style.color = ok ? 'var(--color-success)' : '';
                            });
                        });
                    })();

                    function togglePassword(id, btn) {
                        const input = document.getElementById(id);
                        if (!input) return;
                        input.type = input.type === 'password' ? 'text' : 'password';
                    }
                </script>

            <?php endif; ?>

        </div>
    </div>
</div>