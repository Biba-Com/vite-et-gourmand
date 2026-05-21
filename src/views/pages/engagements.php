<?php

/**
 * Vue : Page Nos Engagements
 * Chemin : src/views/pages/engagements.php
 */

$isEn          = $isEn          ?? false;
$partenaires   = $partenaires   ?? [];
$equipe        = $equipe        ?? [];
$certifications = $certifications ?? [];
?>

<!-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ -->
<section class="engagements-hero">
    <div class="container">
        <h1 class="engagements-hero__title">
            <?= $isEn ? 'Our Commitments' : 'Nos Engagements' ?>
        </h1>
        <p class="engagements-hero__subtitle">
            <?= $isEn
                ? 'A responsible gastronomy, rooted in the Bordeaux terroir since 1999.'
                : 'Une gastronomie responsable, ancrée dans le terroir bordelais depuis 1999.' ?>
        </p>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     SECTION 1 — Approvisionnement Local
══════════════════════════════════════════════════════════ -->
<section class="engagement-section" aria-labelledby="local-title">
    <div class="container">

        <div class="engagement-section__header">
            <span class="engagement-section__num">01</span>
            <div>
                <h2 class="engagement-section__title" id="local-title">
                    🌍 <?= $isEn
                        ? 'Outstanding Producers, Within 100 km'
                        : 'Des Producteurs d\'Exception, à Moins de 100 km' ?>
                </h2>
                <p class="engagement-section__intro">
                    <?= $isEn
                        ? 'At Vite & Gourmand, every ingredient tells a story. Since 1999, we select our products exclusively from Gironde producers committed to sustainable or organic farming.'
                        : 'Chez Vite & Gourmand, chaque ingrédient raconte une histoire. Depuis 1999, nous sélectionnons nos produits exclusivement auprès de producteurs girondins engagés dans une agriculture raisonnée ou biologique.' ?>
                </p>
            </div>
        </div>

        <!-- Partenaires -->
        <?php if (!empty($partenaires)): ?>
        <div class="engagement-partners">
            <h3 class="engagement-partners__title">
                <?= $isEn ? 'Our partner producers' : 'Nos producteurs partenaires' ?>
            </h3>
            <div class="engagement-partners__grid">
                <?php foreach ($partenaires as $part): ?>
                    <div class="engagement-partner-card">
                        <div class="engagement-partner-card__icon">
                            <?php if (!empty($part['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($part['logo_url'], ENT_QUOTES, 'UTF-8') ?>"
                                     alt="<?= htmlspecialchars($part['nom'], ENT_QUOTES, 'UTF-8') ?>"
                                     width="60" height="60" loading="lazy">
                            <?php else: ?>
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="engagement-partner-card__content">
                            <strong><?= htmlspecialchars($part['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <p><?= htmlspecialchars($part['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            <?php if (!empty($part['site_web'])): ?>
                                <a href="<?= htmlspecialchars($part['site_web'], ENT_QUOTES, 'UTF-8') ?>"
                                   target="_blank" rel="noopener" class="engagement-partner-card__link">
                                    <?= $isEn ? 'Visit website →' : 'Visiter le site →' ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Chiffres clés -->
        <div class="engagement-stats">
            <div class="engagement-stat">
                <span class="engagement-stat__number">93%</span>
                <span class="engagement-stat__label">
                    <?= $isEn ? 'Gironde sourcing' : 'Approvisionnement girondin' ?>
                </span>
            </div>
            <div class="engagement-stat">
                <span class="engagement-stat__number">3</span>
                <span class="engagement-stat__label">
                    <?= $isEn ? 'Fresh deliveries / week' : 'Livraisons fraîches / semaine' ?>
                </span>
            </div>
            <div class="engagement-stat">
                <span class="engagement-stat__number">0</span>
                <span class="engagement-stat__label">
                    <?= $isEn ? 'Middleman' : 'Intermédiaire' ?>
                </span>
            </div>
        </div>

    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     SECTION 2 — Savoir-Faire Artisanal
══════════════════════════════════════════════════════════ -->
<section class="engagement-section engagement-section--alt" aria-labelledby="savoirfaire-title">
    <div class="container">

        <div class="engagement-section__header">
            <span class="engagement-section__num">02</span>
            <div>
                <h2 class="engagement-section__title" id="savoirfaire-title">
                    👨‍🍳 <?= $isEn
                        ? '25 Years of Culinary Excellence in Bordeaux'
                        : '25 Ans d\'Excellence Culinaire à Bordeaux' ?>
                </h2>
                <p class="engagement-section__intro">
                    <?= $isEn
                        ? 'Founded in 1999 by José Carrère and Julie Lartigue, Vite & Gourmand was born from one conviction: event gastronomy deserves the same care as a starred restaurant.'
                        : 'Fondée en 1999 par José Carrère et Julie Lartigue, Vite & Gourmand est née d\'une conviction : la gastronomie événementielle mérite le même soin qu\'un restaurant étoilé.' ?>
                </p>
            </div>
        </div>

        <!-- Équipe -->
        <?php if (!empty($equipe)): ?>
        <div class="engagement-team">
            <h3 class="engagement-team__title">
                <?= $isEn ? 'Our Team' : 'Notre Équipe' ?>
            </h3>
            <div class="engagement-team__grid">
                <?php foreach ($equipe as $membre): ?>
                    <div class="team-member-card">
                        <div class="team-member-card__photo">
                            <?php if (!empty($membre['photo_url'])): ?>
                                <img src="<?= htmlspecialchars($membre['photo_url'], ENT_QUOTES, 'UTF-8') ?>"
                                     alt="<?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom'], ENT_QUOTES, 'UTF-8') ?>"
                                     width="120" height="120" loading="lazy">
                            <?php else: ?>
                                <div class="team-member-card__initials" aria-hidden="true">
                                    <?= strtoupper(mb_substr($membre['prenom'], 0, 1) . mb_substr($membre['nom'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h4 class="team-member-card__name">
                            <?= htmlspecialchars($membre['prenom'] . ' ' . $membre['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </h4>
                        <span class="team-member-card__role">
                            <?= htmlspecialchars($membre['poste'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if (!empty($membre['bio'])): ?>
                            <p class="team-member-card__bio">
                                <?= htmlspecialchars($membre['bio'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Philosophie -->
        <div class="engagement-philosophy">
            <h3 class="engagement-philosophy__title">
                <?= $isEn ? 'Our Philosophy' : 'Notre Philosophie' ?>
            </h3>
            <ul class="engagement-philosophy__list">
                <li>
                    <strong><?= $isEn ? 'Bespoke menus' : 'Menus sur mesure' ?></strong> —
                    <?= $isEn
                        ? 'Every menu is custom-designed — never a fixed catalogue.'
                        : 'Chaque menu est conçu sur mesure — jamais de catalogue figé.' ?>
                </li>
                <li>
                    <strong><?= $isEn ? 'On-site finishing' : 'Cuisson minute sur site' ?></strong> —
                    <?= $isEn
                        ? 'Minute cooking and plating on site whenever possible.'
                        : 'Cuisson minute et dressage sur site quand possible.' ?>
                </li>
                <li>
                    <strong><?= $isEn ? 'Eco-packaging' : 'Emballages éco-responsables' ?></strong> —
                    <?= $isEn
                        ? 'Reusable tableware and compostable packaging.'
                        : 'Vaisselle réutilisable et emballages compostables.' ?>
                </li>
            </ul>
        </div>

    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     SECTION 3 — Certifié Bio & Éco-responsable
══════════════════════════════════════════════════════════ -->
<section class="engagement-section" aria-labelledby="certif-title">
    <div class="container">

        <div class="engagement-section__header">
            <span class="engagement-section__num">03</span>
            <div>
                <h2 class="engagement-section__title" id="certif-title">
                    🌿 <?= $isEn
                        ? 'Certified Organic & Eco-Responsible'
                        : 'Certifié Bio & Éco-responsable' ?>
                </h2>
                <p class="engagement-section__intro">
                    <?= $isEn
                        ? 'A certified approach with measurable results. Our commitment goes beyond words.'
                        : 'Une démarche certifiée, des résultats mesurables. Notre engagement va au-delà des mots.' ?>
                </p>
            </div>
        </div>

        <!-- Certifications -->
        <?php if (!empty($certifications)): ?>
        <div class="engagement-certifs">
            <h3 class="engagement-certifs__title">
                <?= $isEn ? 'Our Certifications' : 'Nos Certifications' ?>
            </h3>
            <div class="engagement-certifs__grid">
                <?php foreach ($certifications as $cert): ?>
                    <div class="certif-card">
                        <div class="certif-card__icon">
                            <?php if (!empty($cert['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($cert['logo_url'], ENT_QUOTES, 'UTF-8') ?>"
                                     alt="<?= htmlspecialchars($cert['nom'], ENT_QUOTES, 'UTF-8') ?>"
                                     width="48" height="48" loading="lazy">
                            <?php else: ?>
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="M9 12l2 2 4-4"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="certif-card__content">
                            <strong><?= htmlspecialchars($cert['nom'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <p><?= htmlspecialchars($cert['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Démarche zéro déchet -->
        <div class="engagement-zerodechet">
            <h3 class="engagement-zerodechet__title">
                ♻️ <?= $isEn ? 'Our Zero-Waste Approach' : 'Notre Démarche Zéro Déchet' ?>
            </h3>
            <div class="engagement-zerodechet__grid">
                <div class="zerodechet-item">
                    <span class="zerodechet-item__icon" aria-hidden="true">🍽️</span>
                    <p><?= $isEn ? 'Certified compostable tableware for all buffets' : 'Vaisselle compostable certifiée pour les buffets' ?></p>
                </div>
                <div class="zerodechet-item">
                    <span class="zerodechet-item__icon" aria-hidden="true">📦</span>
                    <p><?= $isEn ? '100% biodegradable packaging' : 'Emballages 100% biodégradables' ?></p>
                </div>
                <div class="zerodechet-item">
                    <span class="zerodechet-item__icon" aria-hidden="true">🚲</span>
                    <p><?= $isEn ? 'Cargo bike delivery in central Bordeaux (5 km radius)' : 'Livraisons en vélos-cargos dans le centre de Bordeaux (rayon 5 km)' ?></p>
                </div>
                <div class="zerodechet-item">
                    <span class="zerodechet-item__icon" aria-hidden="true">🌱</span>
                    <p><?= $isEn ? 'Organic waste sorting and composting' : 'Tri sélectif et compostage des déchets organiques' ?></p>
                </div>
            </div>
        </div>

        <!-- Chiffres -->
        <div class="engagement-stats">
            <div class="engagement-stat">
                <span class="engagement-stat__number">-85%</span>
                <span class="engagement-stat__label">
                    <?= $isEn ? 'Plastic packaging since 2021' : 'Emballages plastique depuis 2021' ?>
                </span>
            </div>
            <div class="engagement-stat">
                <span class="engagement-stat__number">200 kg</span>
                <span class="engagement-stat__label">
                    <?= $isEn ? 'Composted waste / month' : 'Déchets compostés / mois' ?>
                </span>
            </div>
            <div class="engagement-stat">
                <span class="engagement-stat__number">60%</span>
                <span class="engagement-stat__label">
                    <?= $isEn ? 'Certified organic ingredients' : 'Ingrédients certifiés bio' ?>
                </span>
            </div>
        </div>

    </div>
</section>

<!-- ══════════════════════════════════════════════════════════
     CTA FINAL
══════════════════════════════════════════════════════════ -->
<section class="engagement-cta">
    <div class="container">
        <h2 class="engagement-cta__title">
            <?= $isEn ? 'Convinced by our approach?' : 'Convaincu par notre démarche ?' ?>
        </h2>
        <p class="engagement-cta__text">
            <?= $isEn
                ? 'Contact us to plan your next eco-responsible event.'
                : 'Contactez-nous pour planifier votre prochain événement éco-responsable.' ?>
        </p>
        <div class="engagement-cta__actions">
            <a href="/catalogue/" class="btn btn--primary btn--lg">
                <?= $isEn ? 'Discover our menus' : 'Découvrir nos menus' ?>
            </a>
            <a href="/contact/" class="btn btn--ghost btn--lg">
                <?= $isEn ? 'Contact us' : 'Nous contacter' ?>
            </a>
        </div>
    </div>
</section>
