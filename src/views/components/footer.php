<?php
/**
 * Component: Footer
 * Path: src/views/components/footer.php
 *
 * Colonnes : Logo + Horaires · Liens rapides · Contact · Réseaux + Bio
 * Fonctionnalités : tel: cliquable · Maps cliquable · i18n · RGAA AA · SEO
 */

$currentLang = function_exists('currentLang') ? currentLang() : 'fr';
$currentYear = date('Y');

/* Coordonnées Google Maps — encode l'adresse pour l'URL */
$mapsAddress = urlencode('12 Rue de la Gastronomie, 33000 Bordeaux, France');
$mapsUrl     = 'https://www.google.com/maps/search/?api=1&query=' . $mapsAddress;
?>

<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer class="footer" role="contentinfo" aria-label="<?= $currentLang === 'en' ? 'Site footer' : 'Pied de page du site' ?>">

  <!-- Gold separator -->
  <div class="footer__separator" aria-hidden="true"></div>

  <!-- ── MAIN GRID ── -->
  <div class="footer__main">
    <div class="container footer__grid">

      <!-- COL 1 — Logo + Horaires -->
      <div class="footer__col footer__col--brand" data-animate="footer-col">
        <a href="/" class="footer__logo" aria-label="Vite & Gourmand — Retour à l'accueil">
          <span class="footer__logo-text">
            Vite <em class="footer__logo-amp">&amp;</em> Gourmand
          </span>
        </a>

        <p class="footer__tagline">
          <?= $currentLang === 'en'
            ? 'Eco-responsible artisan caterer in Bordeaux since 1999.'
            : 'Traiteur artisanal éco-responsable à Bordeaux depuis 1999.' ?>
        </p>

        <!-- Horaires -->
        <div class="footer__hours">
          <h3 class="footer__hours-title">
            <svg class="footer__hours-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
            <?= $currentLang === 'en' ? 'Opening hours' : "Horaires d'ouverture" ?>
          </h3>
          <dl class="footer__hours-list">
            <div class="footer__hours-row">
              <dt><?= $currentLang === 'en' ? 'Mon – Fri' : 'Lundi – Vendredi' ?></dt>
              <dd>9h00 – 18h00</dd>
            </div>
            <div class="footer__hours-row">
              <dt><?= $currentLang === 'en' ? 'Saturday' : 'Samedi' ?></dt>
              <dd>10h00 – 16h00</dd>
            </div>
            <div class="footer__hours-row">
              <dt><?= $currentLang === 'en' ? 'Sunday' : 'Dimanche' ?></dt>
              <dd><?= $currentLang === 'en' ? 'Closed' : 'Fermé' ?></dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- COL 2 — Liens rapides -->
      <nav class="footer__col footer__col--nav" aria-label="<?= $currentLang === 'en' ? 'Quick links' : 'Liens rapides' ?>" data-animate="footer-col">
        <h3 class="footer__col-title">
          <?= $currentLang === 'en' ? 'Quick Links' : 'Liens Rapides' ?>
        </h3>
        <ul class="footer__nav-list">
          <li><a href="/" class="footer__nav-link"><?= $currentLang === 'en' ? 'Home' : 'Accueil' ?></a></li>
          <li><a href="/catalogue" class="footer__nav-link"><?= $currentLang === 'en' ? 'Our Menus' : 'Nos Menus' ?></a></li>
          <li><a href="/prestations" class="footer__nav-link"><?= $currentLang === 'en' ? 'Services' : 'Prestations' ?></a></li>
          <li><a href="/engagements" class="footer__nav-link"><?= $currentLang === 'en' ? 'Our Commitments' : 'Nos Engagements' ?></a></li>
          <li><a href="/contact" class="footer__nav-link"><?= $currentLang === 'en' ? 'Contact' : 'Contact' ?></a></li>
          <li><a href="/equipe" class="footer__nav-link"><?= $currentLang === 'en' ? 'Team & CSR' : 'Équipe & RSE' ?></a></li>
        </ul>
      </nav>

      <!-- COL 3 — Contact -->
      <address class="footer__col footer__col--contact" data-animate="footer-col">
        <h3 class="footer__col-title">Contact</h3>
        <ul class="footer__contact-list">

          <!-- Téléphone — cliquable pour appel direct -->
          <li class="footer__contact-item">
            <svg class="footer__contact-icon footer__contact-icon--phone" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.39 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6 6l.97-.97a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 21.79 16"/>
            </svg>
            <a href="tel:+33556000000" class="footer__contact-link footer__contact-link--tel"
              aria-label="<?= $currentLang === 'en' ? 'Call us: 05 56 00 00 00' : 'Nous appeler au 05 56 00 00 00' ?>">
              05 56 00 00 00
            </a>
          </li>

          <!-- Email -->
          <li class="footer__contact-item">
            <svg class="footer__contact-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <rect x="2" y="4" width="20" height="16" rx="2"/>
              <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
            </svg>
            <a href="mailto:contact@vitegourmand.fr" class="footer__contact-link"
              aria-label="<?= $currentLang === 'en' ? 'Email us' : 'Nous écrire par email' ?>">
              contact@vitegourmand.fr
            </a>
          </li>

          <!-- Adresse — ouvre Google Maps -->
          <li class="footer__contact-item footer__contact-item--address">
            <svg class="footer__contact-icon footer__contact-icon--pin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <a href="<?= $mapsUrl ?>"
              class="footer__contact-link footer__contact-link--maps"
              target="_blank"
              rel="noopener noreferrer"
              aria-label="<?= $currentLang === 'en'
                ? 'Open in Google Maps — 12 Rue de la Gastronomie, 33000 Bordeaux'
                : 'Ouvrir dans Google Maps — 12 Rue de la Gastronomie, 33000 Bordeaux' ?>"
            >
              12 Rue de la Gastronomie<br>
              33000 Bordeaux, France
              <svg class="footer__maps-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M7 17 17 7M7 7h10v10"/>
              </svg>
            </a>
          </li>
        </ul>
      </address>

      <!-- COL 4 — Réseaux + Bio -->
      <div class="footer__col footer__col--social" data-animate="footer-col">
        <h3 class="footer__col-title">
          <?= $currentLang === 'en' ? 'Follow us' : 'Suivez-nous' ?>
        </h3>

        <ul class="footer__social-list" aria-label="<?= $currentLang === 'en' ? 'Social media links' : 'Liens réseaux sociaux' ?>">
          <li>
            <a href="https://facebook.com/viteetgourmand" class="footer__social-btn"
              target="_blank" rel="noopener noreferrer"
              aria-label="Facebook — Vite & Gourmand (<?= $currentLang === 'en' ? 'opens in new tab' : 'ouvre dans un nouvel onglet' ?>)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
              </svg>
            </a>
          </li>
          <li>
            <a href="https://instagram.com/viteetgourmand" class="footer__social-btn"
              target="_blank" rel="noopener noreferrer"
              aria-label="Instagram — Vite & Gourmand (<?= $currentLang === 'en' ? 'opens in new tab' : 'ouvre dans un nouvel onglet' ?>)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
              </svg>
            </a>
          </li>
          <li>
            <a href="https://linkedin.com/company/vite-et-gourmand" class="footer__social-btn"
              target="_blank" rel="noopener noreferrer"
              aria-label="LinkedIn — Vite & Gourmand (<?= $currentLang === 'en' ? 'opens in new tab' : 'ouvre dans un nouvel onglet' ?>)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                <rect x="2" y="9" width="4" height="12"/>
                <circle cx="4" cy="4" r="2"/>
              </svg>
            </a>
          </li>
        </ul>

        <!-- Certification Bio badge -->
        <a href="/engagements#certification" class="footer__bio-badge" aria-label="<?= $currentLang === 'en' ? 'Our organic certification — learn more' : 'Notre certification bio — en savoir plus' ?>">
          <svg
  width="18"
  height="18"
  viewBox="0 0 24 24"
  fill="none"
  stroke="currentColor"
  stroke-width="2"
  stroke-linecap="round"
  stroke-linejoin="round"
  aria-hidden="true"
  class="footer__bio-icon"
>
  <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19.2 2.96c1.4 9.3 -3.6 15.7 -8.2 17.04"/>
  <path d="M2 21c0 -3 1.85 -5.36 5.08 -6"/>
</svg>
          <?= $currentLang === 'en' ? 'Organic Certified' : 'Certification Bio' ?>
        </a>
      </div>

    </div><!-- /.footer__grid -->
  </div><!-- /.footer__main -->

  <!-- ── BOTTOM BAR ── -->
  <div class="footer__bottom">
    <div class="container footer__bottom-inner">
      <p class="footer__copyright">
        &copy; <?= $currentYear ?> Vite &amp; Gourmand.
        <?= $currentLang === 'en' ? 'All rights reserved.' : 'Tous droits réservés.' ?>
      </p>
      <nav class="footer__legal-nav" aria-label="<?= $currentLang === 'en' ? 'Legal links' : 'Liens légaux' ?>">
        <a href="/mentions-legales" class="footer__legal-link">
          <?= $currentLang === 'en' ? 'Legal Notice' : 'Mentions légales' ?>
        </a>
        <span class="footer__legal-sep" aria-hidden="true">|</span>
        <a href="/confidentialite" class="footer__legal-link">
          <?= $currentLang === 'en' ? 'Privacy Policy' : 'Politique de confidentialité' ?>
        </a>
        <span class="footer__legal-sep" aria-hidden="true">|</span>
        <a href="/accessibilite" class="footer__legal-link">
          <?= $currentLang === 'en' ? 'Accessibility' : 'Accessibilité' ?>
        </a>
      </nav>
    </div>
  </div>

</footer>
