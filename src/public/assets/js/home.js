/**
 * Page: Accueil — animations au scroll
 * Path: src/public/assets/js/home.js
 * IntersectionObserver sur data-animate="fade-up" et "card-up"
 */

"use strict";

document.addEventListener("DOMContentLoaded", () => {
  initScrollAnimations();
});

function initScrollAnimations() {
  /* Respect prefers-reduced-motion */
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    document
      .querySelectorAll("[data-animate]")
      .forEach((el) => el.classList.add("is-visible"));
    return;
  }

  /* Observer pour les sections */
  const sectionObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-visible");

        /* Déclenche les cards enfants en stagger */
        entry.target
          .querySelectorAll('[data-animate="card-up"]')
          .forEach((card) => card.classList.add("is-visible"));

        sectionObserver.unobserve(entry.target);
      });
    },
    { threshold: 0.1, rootMargin: "0px 0px -60px 0px" },
  );

  /* Observer toutes les sections avec data-animate */
  document
    .querySelectorAll('[data-animate="fade-up"]')
    .forEach((el) => sectionObserver.observe(el));
}
