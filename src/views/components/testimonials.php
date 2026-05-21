<?php

/**
 * Section: Avis de nos Clients (dynamique BDD)
 * Path: src/views/components/testimonials.php
 *
 * Affiche les avis approuvés depuis la table `avis`
 * Énoncé page 3 : "Les avis clients qui sont validés"
 */
$currentLang = function_exists('currentLang') ? currentLang() : 'fr';

// ── Récupérer les avis approuvés depuis la BDD ───────────
$avisValides = [];
$globalRating = 0;
$reviewCount  = 0;

try {
    $pdo = getDbConnection();

    // Récupérer les 3 derniers avis approuvés
    $stmt = $pdo->prepare("
        SELECT
            a.note,
            a.commentaire,
            a.created_at,
            u.prenom,
            u.nom,
            GROUP_CONCAT(m.titre ORDER BY lc.id_ligne SEPARATOR ', ') AS menu_titre,
            c.date_evenement
        FROM avis a
        JOIN utilisateur u          ON a.id_utilisateur = u.id_utilisateur
        JOIN commande c             ON a.id_commande    = c.id_commande
        LEFT JOIN ligne_commande lc ON c.id_commande    = lc.id_commande
        LEFT JOIN menu m            ON lc.id_menu       = m.id_menu
        WHERE a.statut = 'approved'
        GROUP BY
            a.id_avis, a.note, a.commentaire, a.created_at,
            u.prenom, u.nom, c.date_evenement
        ORDER BY a.created_at DESC
        LIMIT 3
    ");
    $stmt->execute();
    $avisValides = $stmt->fetchAll();

    // Note globale (tous les avis approuvés)
    $stmtStats = $pdo->prepare("
        SELECT
            COUNT(*)        AS total,
            AVG(note)       AS moyenne
        FROM avis
        WHERE statut = 'approved'
    ");
    $stmtStats->execute();
    $stats = $stmtStats->fetch();

    $reviewCount  = (int) ($stats['total'] ?? 0);
    $globalRating = $reviewCount > 0
        ? round((float) $stats['moyenne'], 1)
        : 0;
} catch (PDOException $e) {
    error_log('Testimonials: ' . $e->getMessage());
}

// Couleurs avatar cycliques
$avatarColors = ['#800020', '#063A1F', '#D4AF37', '#1565C0', '#8B5CF6'];

// Initiales depuis prénom + nom
function getInitials(string $prenom, string $nom): string
{
    return strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}
?>

<?php if ($reviewCount > 0): ?>

    <!-- Schema.org AggregateRating -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "LocalBusiness",
            "name": "Vite & Gourmand",
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "<?= $globalRating ?>",
                "reviewCount": "<?= $reviewCount ?>",
                "bestRating": "5",
                "worstRating": "1"
            }
        }
    </script>

    <section class="testimonials" aria-labelledby="testimonials-title" data-animate="fade-up">
        <div class="container">

            <div class="section-header section-header--center">
                <h2 id="testimonials-title" class="section-header__title">
                    <?= $currentLang === 'en' ? 'Client Reviews' : 'Avis de nos Clients' ?>
                </h2>
                <p class="section-header__subtitle">
                    <?= $currentLang === 'en'
                        ? 'Discover the testimonials of those who trusted us.'
                        : 'Découvrez les témoignages de ceux qui nous ont fait confiance.' ?>
                </p>
            </div>

            <!-- Cartes avis -->
            <div class="testimonials__grid">
                <?php foreach ($avisValides as $i => $avis):
                    $initials = getInitials($avis['prenom'], $avis['nom']);
                    $color    = $avatarColors[$i % count($avatarColors)];
                    $dateAvis = new DateTime($avis['created_at']);
                    $moisFr = [
                        '',
                        'Janvier',
                        'Février',
                        'Mars',
                        'Avril',
                        'Mai',
                        'Juin',
                        'Juillet',
                        'Août',
                        'Septembre',
                        'Octobre',
                        'Novembre',
                        'Décembre'
                    ];
                    $mois = $currentLang === 'en'
                        ? $dateAvis->format('F Y')
                        : $moisFr[(int)$dateAvis->format('n')] . ' ' . $dateAvis->format('Y');
                    $context  = htmlspecialchars($avis['menu_titre'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                    <article class="testimonial-card"
                        itemscope itemtype="https://schema.org/Review"
                        data-animate="card-up">

                        <!-- Étoiles -->
                        <div class="testimonial-card__stars"
                            aria-label="<?= $avis['note'] ?> <?= $currentLang === 'en' ? 'stars out of 5' : 'étoiles sur 5' ?>">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                                <svg width="16" height="16" viewBox="0 0 24 24"
                                    fill="<?= $s <= $avis['note'] ? '#D4AF37' : 'none' ?>"
                                    stroke="#D4AF37" stroke-width="1.5"
                                    aria-hidden="true">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                            <?php endfor; ?>
                        </div>

                        <!-- Commentaire -->
                        <blockquote class="testimonial-card__quote" itemprop="reviewBody">
                            "<?= htmlspecialchars($avis['commentaire'], ENT_QUOTES, 'UTF-8') ?>"
                        </blockquote>

                        <!-- Auteur -->
                        <footer class="testimonial-card__footer">
                            <div class="testimonial-card__avatar"
                                style="background-color: <?= $color ?>;"
                                aria-hidden="true">
                                <?= $initials ?>
                            </div>
                            <div class="testimonial-card__author">
                                <span class="testimonial-card__name" itemprop="author">
                                    <?= htmlspecialchars($avis['prenom'] . ' ' . mb_substr($avis['nom'], 0, 1) . '.', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="testimonial-card__context">
                                    <?php if ($context): ?>
                                        <?= $context ?>
                                    <?php endif; ?>
                                    — <?= ucfirst($mois) ?>
                                </span>
                            </div>
                        </footer>

                        <!-- Meta Schema.org -->
                        <meta itemprop="datePublished" content="<?= $dateAvis->format('Y-m-d') ?>">
                        <meta itemprop="ratingValue" content="<?= $avis['note'] ?>">

                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Note globale -->
            <div class="testimonials__global"
                aria-label="<?= $currentLang === 'en'
                                ? "Global rating: {$globalRating} out of 5 based on {$reviewCount} verified reviews"
                                : "Note globale : {$globalRating} sur 5 basée sur {$reviewCount} avis vérifiés" ?>">
                <svg width="20" height="20" viewBox="0 0 24 24"
                    fill="#D4AF37" stroke="#D4AF37" stroke-width="1"
                    aria-hidden="true">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
                <strong class="testimonials__score"><?= $globalRating ?>/5</strong>
                <span class="testimonials__count">
                    (<?= $reviewCount ?> <?= $currentLang === 'en' ? 'verified reviews' : 'avis vérifiés' ?>)
                </span>
            </div>

        </div>
    </section>

<?php else: ?>

    <!-- Pas encore d'avis — section masquée -->
    <section class="testimonials testimonials--empty" aria-labelledby="testimonials-title">
        <div class="container">
            <div class="section-header section-header--center">
                <h2 id="testimonials-title" class="section-header__title">
                    <?= $currentLang === 'en' ? 'Client Reviews' : 'Avis de nos Clients' ?>
                </h2>
                <p class="section-header__subtitle">
                    <?= $currentLang === 'en'
                        ? 'Be the first to share your experience!'
                        : 'Soyez le premier à partager votre expérience !' ?>
                </p>
            </div>
        </div>
    </section>

<?php endif; ?>