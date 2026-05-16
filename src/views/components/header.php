<?php
/**
 * Component: Header — i18n version avec bouton X fermeture
 * Path: src/views/components/header.php
 */

$currentPage = $currentPage ?? 'accueil';
$assetsBase  = $assetsBase  ?? '/assets';
$currentLang = currentLang();

$navItems = [
    'accueil'     => ['/',             'nav.accueil'],
    'catalogue'   => ['/catalogue',   'nav.menus'],
    'prestations' => ['/prestations', 'nav.prestations'],
    'engagements' => ['/engagements', 'nav.engagements'],
    'contact'     => ['/contact',     'nav.contact'],
];
?>

<a href="#main-content" class="skip-link"><?= __('nav.skip_link') ?></a>

<div class="promo-banner" id="promo-banner" role="complementary" aria-label="<?= __('promo.text') ?>">
  <div class="container promo-banner__inner">
    <p class="promo-banner__text"><?= __('promo.text') ?></p>
    <button class="promo-banner__close" type="button" aria-label="<?= __('promo.close') ?>" data-action="close-promo">&times;</button>
  </div>
</div>

<header class="header" id="main-header" role="banner">
  <div class="container header__inner">

    <a href="/" class="header__logo" aria-label="<?= __('header.logo_label') ?>">
      <svg class="header__logo-icon" width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
        <circle cx="22" cy="22" r="21" fill="#063A1F"/>
        <circle cx="22" cy="22" r="18.5" stroke="#D4AF37" stroke-width="0.8" stroke-opacity="0.5"/>
        <text x="22" y="19" text-anchor="middle" dominant-baseline="central" fill="#D4AF37"
          font-family="'Playfair Display', Georgia, serif" font-size="12" font-weight="700" letter-spacing="0.5">V&amp;G</text>
        <text x="22" y="32" text-anchor="middle" dominant-baseline="central" fill="#F9F9F7"
          font-family="'Inter', sans-serif" font-size="5" font-weight="400" letter-spacing="2" fill-opacity="0.75">TRAITEUR</text>
      </svg>
      <span class="header__logo-text">
        Vite <em class="header__logo-amp">&amp;</em> Gourmand
      </span>
    </a>

    <nav class="header__nav" aria-label="<?= __('nav.accueil') ?>">
      <ul class="header__nav-list">
        <?php foreach ($navItems as $slug => [$url, $transKey]) :
          $isActive = $currentPage === $slug;
        ?>
        <li>
          <a href="<?= $url ?>"
            class="header__nav-link<?= $isActive ? ' header__nav-link--active' : '' ?>"
            <?= $isActive ? 'aria-current="page"' : '' ?>
          ><?= __($transKey) ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <div class="header__actions">
      <a href="/connexion" class="header__action-btn header__action-btn--account" aria-label="<?= __('header.account') ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
      </a>
      <a href="/panier" class="header__action-btn header__action-btn--cart" aria-label="<?= __('header.cart', ['count' => 0]) ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        <span class="header__cart-count" aria-hidden="true">0</span>
      </a>
      <div class="header__lang-dropdown" id="lang-dropdown" role="navigation" aria-label="<?= __('header.lang_select') ?>">
        <button class="header__lang-btn" type="button" aria-haspopup="listbox" aria-expanded="false" aria-controls="lang-listbox" data-action="toggle-lang">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
          <span class="header__lang-current"><?= strtoupper($currentLang) ?></span>
          <svg class="header__lang-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <ul class="header__lang-list" id="lang-listbox" role="listbox" aria-label="<?= __('header.lang_select') ?>">
          <li role="option" aria-selected="<?= $currentLang === 'fr' ? 'true' : 'false' ?>">
            <a href="<?= langSwitchUrl('fr') ?>" class="header__lang-option<?= $currentLang === 'fr' ? ' header__lang-option--active' : '' ?>" hreflang="fr" <?= $currentLang === 'fr' ? 'aria-current="true"' : '' ?>>
              <span class="header__lang-flag" aria-hidden="true">🇫🇷</span> <?= __('header.lang_fr') ?>
            </a>
          </li>
          <li role="option" aria-selected="<?= $currentLang === 'en' ? 'true' : 'false' ?>">
            <a href="<?= langSwitchUrl('en') ?>" class="header__lang-option<?= $currentLang === 'en' ? ' header__lang-option--active' : '' ?>" hreflang="en" <?= $currentLang === 'en' ? 'aria-current="true"' : '' ?>>
              <span class="header__lang-flag" aria-hidden="true">🇬🇧</span> <?= __('header.lang_en') ?>
            </a>
          </li>
        </ul>
      </div>
    </div>

    <button class="header__toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="<?= __('nav.open_menu') ?>" data-action="toggle-menu">
      <span class="header__toggle-bar" aria-hidden="true"></span>
      <span class="header__toggle-bar" aria-hidden="true"></span>
      <span class="header__toggle-bar" aria-hidden="true"></span>
    </button>

  </div>
  <div class="header__accent" aria-hidden="true"></div>
</header>

<aside class="mobile-menu" id="mobile-menu" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Menu de navigation">
  <div class="mobile-menu__inner">

    <!-- Bouton X fermeture — onclick inline comme backup -->
    <button 
      class="mobile-menu__close" 
      id="mobile-menu-close"
      aria-label="<?= $currentLang === 'en' ? 'Close menu' : 'Fermer le menu' ?>"
      type="button"
      onclick="document.getElementById('mobile-menu').classList.remove('mobile-menu--open');document.getElementById('menu-overlay').classList.remove('mobile-menu__overlay--visible');document.querySelector('.header__toggle').classList.remove('header__toggle--open');document.querySelector('.header__toggle').setAttribute('aria-expanded','false');document.body.style.overflow='';this.closest('.mobile-menu').setAttribute('aria-hidden','true');"
    >
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="18" y1="6" x2="6" y2="18"></line>
        <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
    </button>

    <div class="mobile-menu__account">
      <div class="mobile-menu__avatar" aria-hidden="true">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#D4AF37" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
        </svg>
      </div>
      <div class="mobile-menu__account-info">
        <span class="mobile-menu__account-title"><?= __('account.title') ?></span>
        <a href="/connexion" class="mobile-menu__account-link"><?= __('account.login_register') ?></a>
      </div>
    </div>

    <nav class="mobile-menu__nav" aria-label="Navigation mobile">
      <ul class="mobile-menu__list">
        <?php foreach ($navItems as $slug => [$url, $transKey]) : ?>
        <li>
          <a href="<?= $url ?>" class="mobile-menu__link<?= $currentPage === $slug ? ' mobile-menu__link--active' : '' ?>"><?= __($transKey) ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <a href="/panier" class="mobile-menu__cta">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
      </svg>
      <?= __('cart.order_cta') ?>
    </a>

    <div class="mobile-menu__lang" aria-label="<?= __('header.lang_select') ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
      </svg>
      <a href="<?= langSwitchUrl('fr') ?>" class="<?= $currentLang === 'fr' ? 'mobile-menu__lang-active' : 'mobile-menu__lang-option' ?>" hreflang="fr" <?= $currentLang === 'fr' ? 'aria-current="true"' : '' ?>>FR</a>
      <span class="mobile-menu__lang-sep" aria-hidden="true">|</span>
      <a href="<?= langSwitchUrl('en') ?>" class="<?= $currentLang === 'en' ? 'mobile-menu__lang-active' : 'mobile-menu__lang-option' ?>" hreflang="en" <?= $currentLang === 'en' ? 'aria-current="true"' : '' ?>>EN</a>
    </div>

  </div>
</aside>

<div class="mobile-menu__overlay" id="menu-overlay" aria-hidden="true"></div>
