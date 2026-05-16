/**
 * Header JavaScript — Menu burger + Bouton X + Language + Promo + Scroll
 * Path: src/public/assets/js/header.js
 */

document.addEventListener('DOMContentLoaded', function() {

  // ============================================================
  // ÉLÉMENTS
  // ============================================================
  var toggleBtn  = document.querySelector('.header__toggle');
  var mobileMenu = document.getElementById('mobile-menu');
  var overlay    = document.getElementById('menu-overlay');
  var closeBtn   = document.getElementById('mobile-menu-close');

  // ============================================================
  // FONCTIONS HELPER
  // ============================================================
  function openMenu() {
    if (!mobileMenu || !overlay || !toggleBtn) return;
    mobileMenu.classList.add('mobile-menu--open');
    overlay.classList.add('mobile-menu__overlay--visible');
    toggleBtn.classList.add('header__toggle--open');
    toggleBtn.setAttribute('aria-expanded', 'true');
    mobileMenu.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    if (!mobileMenu || !overlay || !toggleBtn) return;
    mobileMenu.classList.remove('mobile-menu--open');
    overlay.classList.remove('mobile-menu__overlay--visible');
    toggleBtn.classList.remove('header__toggle--open');
    toggleBtn.setAttribute('aria-expanded', 'false');
    mobileMenu.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // ============================================================
  // MENU BURGER — Toggle
  // ============================================================
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      var isOpen = mobileMenu && mobileMenu.classList.contains('mobile-menu--open');
      if (isOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    });
  }

  // ============================================================
  // BOUTON X — Fermeture
  // ============================================================
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      closeMenu();
    });
  }

  // ============================================================
  // OVERLAY — Fermeture au clic en dehors
  // ============================================================
  if (overlay) {
    overlay.addEventListener('click', function() {
      closeMenu();
    });
  }

  // ============================================================
  // TOUCHE ESCAPE — Fermeture
  // ============================================================
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('mobile-menu--open')) {
      closeMenu();
      if (toggleBtn) toggleBtn.focus();
    }
  });

  // ============================================================
  // DROPDOWN LANGUE (desktop)
  // ============================================================
  var langDropdown = document.getElementById('lang-dropdown');
  var langBtn = langDropdown ? langDropdown.querySelector('.header__lang-btn') : null;

  if (langBtn && langDropdown) {
    langBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      var isOpen = langDropdown.classList.contains('is-open');
      langDropdown.classList.toggle('is-open', !isOpen);
      langBtn.setAttribute('aria-expanded', String(!isOpen));
    });

    document.addEventListener('click', function(e) {
      if (!langDropdown.contains(e.target)) {
        langDropdown.classList.remove('is-open');
        langBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ============================================================
  // PROMO BANNER — Fermeture
  // ============================================================
  var promoBanner = document.getElementById('promo-banner');
  var promoClose  = promoBanner ? promoBanner.querySelector('[data-action="close-promo"]') : null;

  if (promoClose && promoBanner) {
    promoClose.addEventListener('click', function() {
      promoBanner.classList.add('promo-banner--hidden');
      try { sessionStorage.setItem('promoBannerClosed', 'true'); } catch(e) {}
    });

    try {
      if (sessionStorage.getItem('promoBannerClosed') === 'true') {
        promoBanner.classList.add('promo-banner--hidden');
      }
    } catch(e) {}
  }

  // ============================================================
  // HEADER SCROLL — Ombre au scroll
  // ============================================================
  var header = document.querySelector('.header');
  if (header) {
    window.addEventListener('scroll', function() {
      if (window.pageYOffset > 50) {
        header.classList.add('header--scrolled');
      } else {
        header.classList.remove('header--scrolled');
      }
    });
  }

});
