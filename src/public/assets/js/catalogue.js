/**
 * ============================================================
 * Vite & Gourmand — Catalogue JS
 * ============================================================
 * Chemin : src/public/assets/js/catalogue.js
 *
 * Fonctionnalités :
 *  1. Toggle filtres avancés (aria-expanded + animation CSS)
 *  2. Pills allergènes (changement couleur au clic)
 *  3. Compteur de filtres actifs sur le bouton toggle
 *  4. Auto-submit sur changement de select
 *  5. Accessibilité : focus trap + keyboard nav
 * ============================================================
 */

"use strict";

document.addEventListener("DOMContentLoaded", () => {
  // ── Éléments DOM ─────────────────────────────────────────
  const toggleBtn = document.getElementById("filterToggle");
  const filterPanel = document.getElementById("advancedFilters");
  const filterForm = document.querySelector(".catalogue__filters-wrapper");
  const pillInputs = document.querySelectorAll(".pill__input");
  const pillLabels = document.querySelectorAll(".pill__label");
  const selects = document.querySelectorAll(".filter-select");

  if (!toggleBtn || !filterPanel) return;

  // ══════════════════════════════════════════════════════════
  // 1. TOGGLE FILTRES AVANCÉS
  // ══════════════════════════════════════════════════════════

  const openFilters = () => {
    filterPanel.classList.add("filter-advanced--open");
    toggleBtn.setAttribute("aria-expanded", "true");
    filterPanel.setAttribute("aria-hidden", "false");
    // Focus sur le premier select après ouverture
    const firstSelect = filterPanel.querySelector("select");
    if (firstSelect) {
      setTimeout(() => firstSelect.focus(), 350); // attend la fin de l'animation
    }
  };

  const closeFilters = () => {
    filterPanel.classList.remove("filter-advanced--open");
    toggleBtn.setAttribute("aria-expanded", "false");
    filterPanel.setAttribute("aria-hidden", "true");
  };

  const toggleFilters = () => {
    const isOpen = toggleBtn.getAttribute("aria-expanded") === "true";
    isOpen ? closeFilters() : openFilters();
    updateToggleBadge();
  };

  toggleBtn.addEventListener("click", toggleFilters);

  // Fermer avec Escape
  document.addEventListener("keydown", (e) => {
    if (
      e.key === "Escape" &&
      toggleBtn.getAttribute("aria-expanded") === "true"
    ) {
      closeFilters();
      toggleBtn.focus(); // remettre le focus sur le bouton
    }
  });

  // ══════════════════════════════════════════════════════════
  // 2. PILLS ALLERGÈNES — Changement de couleur au clic
  // ══════════════════════════════════════════════════════════

  pillInputs.forEach((input) => {
    const pill = input.closest(".pill");
    const label = pill?.querySelector(".pill__label");

    if (!pill || !label) return;

    const updatePillState = () => {
      if (input.checked) {
        pill.classList.add("pill--active");
        label.style.background = "var(--color-bordeaux)";
        label.style.color = "#FFFFFF";
      } else {
        pill.classList.remove("pill--active");
        label.style.background = "";
        label.style.color = "";
      }
    };

    // Initialiser l'état au chargement
    updatePillState();

    // Écouter le changement
    input.addEventListener("change", () => {
      updatePillState();
      updateToggleBadge();
    });
  });

  // ══════════════════════════════════════════════════════════
  // 3. COMPTEUR DE FILTRES ACTIFS SUR LE BOUTON
  // ══════════════════════════════════════════════════════════

  /**
   * Compte combien de filtres avancés sont actifs
   * et affiche un badge sur le bouton toggle
   */
  const updateToggleBadge = () => {
    let count = 0;

    // Compter les selects avec une valeur non vide
    selects.forEach((select) => {
      if (select.value && select.id !== "ordre") count++;
    });

    // Compter les allergènes cochés
    pillInputs.forEach((input) => {
      if (input.checked) count++;
    });

    // Mettre à jour ou supprimer le badge
    let badge = toggleBtn.querySelector(".filter-toggle__badge");

    if (count > 0) {
      if (!badge) {
        badge = document.createElement("span");
        badge.className = "filter-toggle__badge";
        badge.setAttribute(
          "aria-label",
          `${count} filtre${count > 1 ? "s" : ""} actif${count > 1 ? "s" : ""}`,
        );
        toggleBtn.appendChild(badge);
      }
      badge.textContent = count;
    } else if (badge) {
      badge.remove();
    }
  };

  // ══════════════════════════════════════════════════════════
  // 4. AUTO-SUBMIT sur changement de select
  //    (optionnel — commente si tu préfères le bouton manuel)
  // ══════════════════════════════════════════════════════════

  selects.forEach((select) => {
    select.addEventListener("change", () => {
      updateToggleBadge();
      // Décommenter la ligne suivante pour auto-submit :
      // filterForm?.submit();
    });
  });

  // ══════════════════════════════════════════════════════════
  // 5. ANIMATION DE LA RECHERCHE — Feedback visuel
  // ══════════════════════════════════════════════════════════

  const searchInput = document.querySelector(".filter-search__input");
  const submitBtn = document.querySelector(".filter-submit");

  if (searchInput && submitBtn) {
    // Activer/désactiver visuellement le bouton selon la saisie
    searchInput.addEventListener("input", () => {
      if (searchInput.value.trim().length > 0) {
        submitBtn.style.opacity = "1";
        submitBtn.style.fontWeight = "700";
      }
    });
  }

  // ══════════════════════════════════════════════════════════
  // 6. INITIALISATION AU CHARGEMENT
  // ══════════════════════════════════════════════════════════

  // Calculer le badge au chargement (si filtres déjà actifs via URL)
  updateToggleBadge();

  // Si le panel est déjà ouvert (filtres actifs depuis URL),
  // s'assurer que l'aria-expanded est correct
  if (filterPanel.classList.contains("filter-advanced--open")) {
    toggleBtn.setAttribute("aria-expanded", "true");
    filterPanel.setAttribute("aria-hidden", "false");
  }
});
