/**
 * ============================================================
 * Vite & Gourmand — Catalogue JS v2.1 (AJAX corrigé)
 * ============================================================
 * Chemin : src/public/assets/js/catalogue.js
 *
 * Corrections v2.1 :
 *  - resetFilters() exposée globalement (window.)
 *  - Selects déclenchent fetchMenus() en temps réel
 *  - Noms de paramètres alignés avec l'API PHP
 * ============================================================
 */

"use strict";

// ── Variables globales module ─────────────────────────────
let _fetchAbortController = null;
let _debounceTimer        = null;

// ── Éléments DOM ─────────────────────────────────────────
const _getEl  = (id)  => document.getElementById(id);
const _getAll = (sel) => document.querySelectorAll(sel);

// ══════════════════════════════════════════════════════════
// RESET — exposée globalement pour onclick HTML
// ══════════════════════════════════════════════════════════
window.resetFilters = function () {
    const form = _getEl("catalogue-filter-form");
    if (!form) return;

    // Reset natif du formulaire
    form.reset();

    // Remettre toutes les pills à l'état inactif
    _getAll(".pill__input").forEach((input) => {
        input.checked = false;
        _updatePillState(input);
    });

    // Mettre à jour le badge
    _updateToggleBadge();

    // Lancer la requête AJAX
    _fetchMenus();
};

// ══════════════════════════════════════════════════════════
// INIT — au chargement du DOM
// ══════════════════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", () => {

    const toggleBtn    = _getEl("filterToggle");
    const filterPanel  = _getEl("advancedFilters");
    const filterForm   = _getEl("catalogue-filter-form");
    const searchInput  = document.querySelector(".filter-search__input");

    if (!toggleBtn || !filterPanel || !filterForm) return;

    // ── 1. TOGGLE FILTRES AVANCÉS ─────────────────────────
    const openFilters = () => {
        filterPanel.classList.add("filter-advanced--open");
        toggleBtn.setAttribute("aria-expanded", "true");
        filterPanel.setAttribute("aria-hidden", "false");
        const firstSelect = filterPanel.querySelector("select");
        if (firstSelect) setTimeout(() => firstSelect.focus(), 350);
    };

    const closeFilters = () => {
        filterPanel.classList.remove("filter-advanced--open");
        toggleBtn.setAttribute("aria-expanded", "false");
        filterPanel.setAttribute("aria-hidden", "true");
    };

    toggleBtn.addEventListener("click", () => {
        const isOpen = toggleBtn.getAttribute("aria-expanded") === "true";
        isOpen ? closeFilters() : openFilters();
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && toggleBtn.getAttribute("aria-expanded") === "true") {
            closeFilters();
            toggleBtn.focus();
        }
    });

    // Initialiser l'état du panel
    if (filterPanel.classList.contains("filter-advanced--open")) {
        toggleBtn.setAttribute("aria-expanded", "true");
        filterPanel.setAttribute("aria-hidden", "false");
    }

    // ── 2. PILLS ALLERGÈNES ───────────────────────────────
    _getAll(".pill__input").forEach((input) => {
        _updatePillState(input);
        input.addEventListener("change", () => {
            _updatePillState(input);
            _updateToggleBadge();
            _fetchMenus(); // AJAX immédiat
        });
    });

    // ── 3. SELECTS → AJAX temps réel ─────────────────────
    _getAll(".filter-select").forEach((select) => {
        select.addEventListener("change", () => {
            _updateToggleBadge();
            _fetchMenus(); // AJAX immédiat sans attendre submit
        });
    });

    // ── 4. CHAMP RECHERCHE → AJAX avec debounce ───────────
    searchInput?.addEventListener("input", () => {
        _updateToggleBadge();
        clearTimeout(_debounceTimer);
        _debounceTimer = setTimeout(_fetchMenus, 400);
    });

    // ── 5. SUBMIT → intercepter + AJAX ───────────────────
    filterForm.addEventListener("submit", (e) => {
        e.preventDefault();
        _updateToggleBadge();
        _fetchMenus();
    });

    // ── 6. INITIALISATION ─────────────────────────────────
    _updateToggleBadge();
});

// ══════════════════════════════════════════════════════════
// FONCTIONS INTERNES
// ══════════════════════════════════════════════════════════

/**
 * Met à jour l'état visuel d'une pill allergène
 */
function _updatePillState(input) {
    const pill  = input.closest(".pill");
    const label = pill?.querySelector(".pill__label");
    if (!pill || !label) return;

    if (input.checked) {
        pill.classList.add("pill--active");
        label.style.background = "var(--color-bordeaux)";
        label.style.color      = "#FFFFFF";
    } else {
        pill.classList.remove("pill--active");
        label.style.background = "";
        label.style.color      = "";
    }
}

/**
 * Compte les filtres actifs et met à jour le badge du toggle
 */
function _updateToggleBadge() {
    const toggleBtn  = _getEl("filterToggle");
    const searchInput = document.querySelector(".filter-search__input");
    if (!toggleBtn) return;

    let count = 0;
    _getAll(".filter-select").forEach((s) => {
        if (s.value && s.id !== "ordre") count++;
    });
    _getAll(".pill__input").forEach((i) => { if (i.checked) count++; });
    if (searchInput?.value.trim()) count++;

    let badge = toggleBtn.querySelector(".filter-toggle__badge");
    if (count > 0) {
        if (!badge) {
            badge = document.createElement("span");
            badge.className = "filter-toggle__badge";
            toggleBtn.appendChild(badge);
        }
        badge.textContent = count;
        badge.setAttribute("aria-label", `${count} filtre${count > 1 ? "s" : ""} actif${count > 1 ? "s" : ""}`);
    } else if (badge) {
        badge.remove();
    }
}

/**
 * Lance la requête AJAX vers /api/catalogue.php
 */
function _fetchMenus() {
    const form        = _getEl("catalogue-filter-form");
    const gridContainer = _getEl("menus-grid");
    const resultsCount  = _getEl("results-count");

    if (!form || !gridContainer) return;

    // Annuler la requête précédente
    if (_fetchAbortController) _fetchAbortController.abort();
    _fetchAbortController = new AbortController();

    // Collecter les paramètres du formulaire
    const params = new URLSearchParams();

    // Champ recherche
    const search = form.querySelector('[name="search"]')?.value.trim();
    if (search) params.set("search", search);

    // Thème
    const theme = form.querySelector('[name="theme"]')?.value;
    if (theme) params.set("theme", theme);

    // Régime
    const regime = form.querySelector('[name="regime"]')?.value;
    if (regime) params.set("regime", regime);

    // Prix max
    const prixMax = form.querySelector('[name="prix_max"]')?.value;
    if (prixMax) params.set("prix_max", prixMax);

    // Nb personnes
    const nbPersonnes = form.querySelector('[name="nb_personnes"]')?.value;
    if (nbPersonnes) params.set("nb_personnes", nbPersonnes);

    // Ordre
    const ordre = form.querySelector('[name="ordre"]')?.value;
    if (ordre) params.set("ordre", ordre);

    // Allergènes cochés
    form.querySelectorAll('[name="allergenes[]"]:checked').forEach((cb) => {
        params.append("allergenes[]", cb.value);
    });

    // Skeleton
    _showSkeleton(gridContainer);

    fetch("/api/catalogue.php?" + params.toString(), {
        signal: _fetchAbortController.signal,
        headers: { "X-Requested-With": "XMLHttpRequest" },
    })
        .then((res) => {
            if (!res.ok) throw new Error("Erreur réseau " + res.status);
            return res.json();
        })
        .then((data) => {
            _renderMenus(gridContainer, data.menus || []);
            if (resultsCount) {
                const total = data.total || 0;
                resultsCount.textContent = `${total} menu${total > 1 ? "s" : ""} trouvé${total > 1 ? "s" : ""}`;
            }
            // Mettre à jour l'URL sans rechargement
            const newUrl = window.location.pathname + (params.toString() ? "?" + params.toString() : "");
            window.history.replaceState({}, "", newUrl);
        })
        .catch((err) => {
            if (err.name !== "AbortError") {
                console.error("Catalogue AJAX:", err);
                gridContainer.innerHTML = `<div class="catalogue__empty"><p>⚠️ Erreur. Veuillez réessayer.</p></div>`;
            }
        });
}

/**
 * Affiche des cartes skeleton pendant le chargement
 */
function _showSkeleton(container) {
    container.innerHTML = Array(6).fill(`
        <article class="menu-card menu-card--skeleton" aria-hidden="true">
            <div class="menu-card__image-wrapper skeleton-box"></div>
            <div class="menu-card__body">
                <div class="skeleton-line skeleton-line--title"></div>
                <div class="skeleton-line skeleton-line--text"></div>
                <div class="skeleton-line skeleton-line--short"></div>
            </div>
        </article>
    `).join("");
}

/**
 * Génère le HTML d'une carte menu et l'injecte dans la grille
 */
function _renderMenus(container, menus) {
    if (menus.length === 0) {
        container.innerHTML = `
            <div class="catalogue__empty" role="status">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <p>Aucun menu ne correspond à vos critères.</p>
                <button class="btn btn--ghost btn--sm" onclick="resetFilters()">
                    Réinitialiser les filtres
                </button>
            </div>
        `;
        return;
    }

    container.innerHTML = menus.map((menu, i) => {
        const prix   = parseFloat(menu.prix_par_personne || 0).toFixed(0);
        const titre  = _esc(menu.titre || "");
        const desc   = _esc(menu.description || "");
        const slug   = _esc(menu.slug || "");
        const imgUrl = _esc(menu.image_url || "");
        const theme  = _esc(menu.theme_nom || "");
        const color  = _esc(menu.theme_couleur || "#D4AF37");
        const minPers = parseInt(menu.nb_personnes_min || 1);

        return `
        <article class="menu-card menu-card--animate"
                 role="listitem"
                 style="animation-delay:${i * 0.05}s"
                 itemscope itemtype="https://schema.org/Product">

            <a href="/catalogue/detail.php?slug=${slug}"
               class="menu-card__image-link" tabindex="-1" aria-hidden="true">
                <div class="menu-card__image-wrapper">
                    ${imgUrl
                        ? `<img class="menu-card__image" src="${imgUrl}" alt="" loading="lazy" width="400" height="280">`
                        : `<div class="menu-card__image menu-card__image--placeholder" aria-hidden="true">
                               <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                   <path d="M3 11l19-9-9 19-2-8-8-2z"/>
                               </svg>
                           </div>`}
                    ${theme ? `<span class="menu-card__theme-badge" style="--theme-color:${color}">${theme}</span>` : ""}
                </div>
            </a>

            <div class="menu-card__body">
                <div class="menu-card__header">
                    <h2 class="menu-card__title" itemprop="name">
                        <a href="/catalogue/detail.php?slug=${slug}" class="menu-card__title-link">
                            ${titre}
                        </a>
                    </h2>
                    <span class="menu-card__price">${prix}€</span>
                </div>
                ${desc ? `<p class="menu-card__description">${desc.substring(0, 100)}${desc.length > 100 ? "…" : ""}</p>` : ""}
                <div class="menu-card__meta">
                    <span class="menu-card__meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                        </svg>
                        Min. ${minPers} pers.
                    </span>
                    ${theme ? `<span class="menu-card__meta-item">${theme}</span>` : ""}
                </div>
                <a href="/catalogue/detail.php?slug=${slug}"
                   class="menu-card__cta btn btn--primary btn--sm"
                   aria-label="Voir le détail du menu ${titre}">
                    Voir le détail
                </a>
            </div>
        </article>
        `;
    }).join("");
}

/**
 * Échappe le HTML pour éviter les XSS côté rendu JS
 */
function _esc(str) {
    const d = document.createElement("div");
    d.textContent = String(str);
    return d.innerHTML;
}

