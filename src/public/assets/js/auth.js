/**
 * Auth Pages — Connexion + Inscription
 * Path: src/public/assets/js/auth.js
 *
 * Modules :
 *  1. initAuthCard()         — Animation d'entrée de la carte
 *  2. initPasswordToggle()   — Afficher/masquer le mot de passe
 *  3. initPasswordStrength() — Barre de force du mot de passe
 *  4. initPasswordMatch()    — Validation confirmation mot de passe
 */

"use strict";

document.addEventListener("DOMContentLoaded", () => {
  initAuthCard();
  initPasswordToggle();
  initPasswordStrength();
  initPasswordMatch();
});

/* ============================================================
   1. Animation d'entrée de la carte
   ============================================================ */
function initAuthCard() {
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  const card = document.querySelector(".auth-card");
  if (!card) return;

  /* Légère pause pour que l'animation soit visible */
  requestAnimationFrame(() => {
    setTimeout(() => card.classList.add("is-visible"), 80);
  });
}

/* ============================================================
   2. Afficher / Masquer le mot de passe
   ============================================================ */
function initPasswordToggle() {
  document.querySelectorAll("[data-toggle-password]").forEach((btn) => {
    const targetId = btn.getAttribute("data-toggle-password");
    const input = document.getElementById(targetId);
    if (!input) return;

    const iconShow = btn.querySelector(".toggle-icon--show");
    const iconHide = btn.querySelector(".toggle-icon--hide");

    btn.addEventListener("click", () => {
      const isPassword = input.type === "password";

      /* Basculer le type */
      input.type = isPassword ? "text" : "password";

      /* Basculer les icônes */
      if (iconShow) iconShow.style.display = isPassword ? "none" : "";
      if (iconHide) iconHide.style.display = isPassword ? "" : "none";

      /* Mettre à jour aria-label */
      btn.setAttribute(
        "aria-label",
        isPassword ? "Masquer le mot de passe" : "Afficher le mot de passe",
      );

      /* Remettre le focus sur l'input */
      input.focus();
    });
  });
}

/* ============================================================
   3. Barre de force du mot de passe
   ============================================================ */
function initPasswordStrength() {
  const input = document.querySelector("[data-password-strength]");
  if (!input) return;

  const strengthContainer = document.querySelector(".password-strength");
  const label = document.getElementById("password-strength-desc");
  if (!strengthContainer || !label) return;

  const levels = {
    fr: ["", "Faible", "Moyen", "Bon", "Fort"],
    en: ["", "Weak", "Fair", "Good", "Strong"],
  };

  /* Détecte la langue depuis l'attribut lang de <html> */
  const lang = document.documentElement.lang?.startsWith("en") ? "en" : "fr";
  const texts = levels[lang];

  input.addEventListener("input", () => {
    const value = input.value;
    const score = calculateStrength(value);

    strengthContainer.setAttribute("data-level", score);
    label.textContent = texts[score] || "";
  });
}

/**
 * Calcule un score de 0 à 4 pour la force du mot de passe
 * @param {string} password
 * @returns {number} 0-4
 */
function calculateStrength(password) {
  if (!password) return 0;
  let score = 0;

  if (password.length >= 8) score++;
  if (password.length >= 12) score++;
  if (/[A-Z]/.test(password) && /[a-z]/.test(password)) score++;
  if (/[0-9]/.test(password)) score++;
  if (/[^A-Za-z0-9]/.test(password)) score++;

  /* Normalise à 4 */
  return Math.min(4, Math.max(1, Math.round((score * 4) / 5)));
}

/* ============================================================
   4. Validation confirmation mot de passe
   ============================================================ */
function initPasswordMatch() {
  const confirmInput = document.querySelector("[data-match]");
  if (!confirmInput) return;

  const sourceId = confirmInput.getAttribute("data-match");
  const sourceInput = document.getElementById(sourceId);
  if (!sourceInput) return;

  function checkMatch() {
    const match = confirmInput.value === sourceInput.value;
    const group = confirmInput.closest(".form-group");

    if (confirmInput.value.length === 0) {
      /* Pas encore saisi — pas de feedback */
      group?.classList.remove("form-group--error");
      confirmInput.setAttribute("aria-invalid", "false");
      return;
    }

    if (!match) {
      group?.classList.add("form-group--error");
      confirmInput.setAttribute("aria-invalid", "true");
    } else {
      group?.classList.remove("form-group--error");
      confirmInput.setAttribute("aria-invalid", "false");
    }
  }

  confirmInput.addEventListener("input", checkMatch);
  sourceInput.addEventListener("input", checkMatch);
}
