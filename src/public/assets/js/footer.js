/**
 * Component: Footer animations
 * Path: src/public/assets/js/footer.js
 *
 * IntersectionObserver : anime les colonnes au scroll (stagger)
 * Fonctionne sans JS (opacity: 1 en CSS si pas de JS)
 */

'use strict';

document.addEventListener('DOMContentLoaded', () => {
  initFooterAnimations();
});

function initFooterAnimations() {
  const cols = document.querySelectorAll('[data-animate="footer-col"]');
  if (!cols.length) return;

  /* Respect prefers-reduced-motion */
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    cols.forEach(col => col.classList.add('is-visible'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target); /* Lance une seule fois */
        }
      });
    },
    {
      threshold: 0.15,   /* Déclenche quand 15% de la col est visible */
      rootMargin: '0px 0px -40px 0px'
    }
  );

  cols.forEach(col => observer.observe(col));
}

