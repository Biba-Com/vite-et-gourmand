/**
 * ============================================================
 * Vite & Gourmand — Catalogue JS
 * ============================================================
 * Path: src/public/assets/js/catalogue.js
 *
 * Live AJAX filtering of the menu catalogue.
 * The dynamic content is rendered without innerHTML for user
 * data: every value goes through _esc() (see bottom), and the
 * static blocks are built with DOM methods (createElement).
 * ============================================================
 */

"use strict";

// ── Module-level state ────────────────────────────────────
let _fetchAbortController = null;
let _debounceTimer        = null;

// ── DOM helpers ───────────────────────────────────────────
const _getEl  = (id)  => document.getElementById(id);
const _getAll = (sel) => document.querySelectorAll(sel);

// ══════════════════════════════════════════════════════════
// RESET — exposed globally so it can be called from buttons
// ══════════════════════════════════════════════════════════
window.resetFilters = function () {
    const form = _getEl("catalogue-filter-form");
    if (!form) return;

    // Native form reset
    form.reset();

    // Turn every allergen pill back to inactive
    _getAll(".pill__input").forEach((input) => {
        input.checked = false;
        _updatePillState(input);
    });

    // Update the badge
    _updateToggleBadge();

    // Run the AJAX request
    _fetchMenus();
};

// ══════════════════════════════════════════════════════════
// INIT — once the DOM is ready
// ══════════════════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", () => {

    const toggleBtn   = _getEl("filterToggle");
    const filterPanel = _getEl("advancedFilters");
    const filterForm  = _getEl("catalogue-filter-form");
    const searchInput = document.querySelector(".filter-search__input");

    if (!toggleBtn || !filterPanel || !filterForm) return;

    // ── 1. Toggle the advanced filters panel ──────────────
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

    // Initialise the panel state
    if (filterPanel.classList.contains("filter-advanced--open")) {
        toggleBtn.setAttribute("aria-expanded", "true");
        filterPanel.setAttribute("aria-hidden", "false");
    }

    // ── 2. Allergen pills → immediate AJAX ────────────────
    _getAll(".pill__input").forEach((input) => {
        _updatePillState(input);
        input.addEventListener("change", () => {
            _updatePillState(input);
            _updateToggleBadge();
            _fetchMenus();
        });
    });

    // ── 3. Selects → real-time AJAX ───────────────────────
    _getAll(".filter-select").forEach((select) => {
        select.addEventListener("change", () => {
            _updateToggleBadge();
            _fetchMenus();
        });
    });

    // ── 4. Search field → AJAX with debounce ──────────────
    searchInput?.addEventListener("input", () => {
        _updateToggleBadge();
        clearTimeout(_debounceTimer);
        _debounceTimer = setTimeout(_fetchMenus, 400);
    });

    // ── 5. Submit → intercept + AJAX ──────────────────────
    filterForm.addEventListener("submit", (e) => {
        e.preventDefault();
        _updateToggleBadge();
        _fetchMenus();
    });

    // ── 6. Initial badge state ────────────────────────────
    _updateToggleBadge();
});

// ══════════════════════════════════════════════════════════
// INTERNAL FUNCTIONS
// ══════════════════════════════════════════════════════════

/**
 * Update the visual state of one allergen pill.
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
 * Count active filters and update the toggle badge.
 */
function _updateToggleBadge() {
    const toggleBtn   = _getEl("filterToggle");
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
 * Run the AJAX request to /api/catalogue.php and render the result.
 */
function _fetchMenus() {
    const form          = _getEl("catalogue-filter-form");
    const gridContainer = _getEl("menus-grid");
    const resultsCount  = _getEl("results-count");

    if (!form || !gridContainer) return;

    // Cancel the previous request, if any
    if (_fetchAbortController) _fetchAbortController.abort();
    _fetchAbortController = new AbortController();

    // Collect the form parameters
    const params = new URLSearchParams();

    const search = form.querySelector('[name="search"]')?.value.trim();
    if (search) params.set("search", search);

    const theme = form.querySelector('[name="theme"]')?.value;
    if (theme) params.set("theme", theme);

    const regime = form.querySelector('[name="regime"]')?.value;
    if (regime) params.set("regime", regime);

    const prixMax = form.querySelector('[name="prix_max"]')?.value;
    if (prixMax) params.set("prix_max", prixMax);

    const nbPersonnes = form.querySelector('[name="nb_personnes"]')?.value;
    if (nbPersonnes) params.set("nb_personnes", nbPersonnes);

    const ordre = form.querySelector('[name="ordre"]')?.value;
    if (ordre) params.set("ordre", ordre);

    form.querySelectorAll('[name="allergenes[]"]:checked').forEach((cb) => {
        params.append("allergenes[]", cb.value);
    });

    // Show skeleton cards while loading
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
            // Update the URL without reloading
            const newUrl = window.location.pathname + (params.toString() ? "?" + params.toString() : "");
            window.history.replaceState({}, "", newUrl);
        })
        .catch((err) => {
            if (err.name !== "AbortError") {
                console.error("Catalogue AJAX:", err);
                // Error message, built with DOM methods (no innerHTML)
                gridContainer.replaceChildren();
                const errorBox = document.createElement("div");
                errorBox.className = "catalogue__empty";
                const errorMsg = document.createElement("p");
                errorMsg.textContent = "⚠️ Erreur. Veuillez réessayer.";
                errorBox.appendChild(errorMsg);
                gridContainer.appendChild(errorBox);            }
        });
}

/**
 * Show 6 skeleton cards while the menus are loading.
 * Built with DOM methods (no innerHTML).
 */
function _showSkeleton(container) {
    container.replaceChildren();

    for (let i = 0; i < 6; i++) {
        const card = document.createElement("article");
        card.className = "menu-card menu-card--skeleton";
        card.setAttribute("aria-hidden", "true");

        const imageWrapper = document.createElement("div");
        imageWrapper.className = "menu-card__image-wrapper skeleton-box";

        const body = document.createElement("div");
        body.className = "menu-card__body";

        const lineTitle = document.createElement("div");
        lineTitle.className = "skeleton-line skeleton-line--title";

        const lineText = document.createElement("div");
        lineText.className = "skeleton-line skeleton-line--text";

        const lineShort = document.createElement("div");
        lineShort.className = "skeleton-line skeleton-line--short";

        body.appendChild(lineTitle);
        body.appendChild(lineText);
        body.appendChild(lineShort);

        card.appendChild(imageWrapper);
        card.appendChild(body);

        container.appendChild(card);
    }
}

/**
 * Render the menu cards into the grid.
 * The empty state is built with DOM methods (no innerHTML).
 * The cards use a template string, but every user value is
 * escaped with _esc() first, which neutralises any XSS.
 */
function _renderMenus(container, menus) {
    // Empty state
    if (menus.length === 0) {
        container.replaceChildren();

        const emptyBox = document.createElement("div");
        emptyBox.className = "catalogue__empty";
        emptyBox.setAttribute("role", "status");

        const svgNS = "http://www.w3.org/2000/svg";
        const svg = document.createElementNS(svgNS, "svg");
        svg.setAttribute("width", "48");
        svg.setAttribute("height", "48");
        svg.setAttribute("viewBox", "0 0 24 24");
        svg.setAttribute("fill", "none");
        svg.setAttribute("stroke", "currentColor");
        svg.setAttribute("stroke-width", "1.2");
        svg.setAttribute("aria-hidden", "true");

        const circle = document.createElementNS(svgNS, "circle");
        circle.setAttribute("cx", "11");
        circle.setAttribute("cy", "11");
        circle.setAttribute("r", "8");

        const line = document.createElementNS(svgNS, "line");
        line.setAttribute("x1", "21");
        line.setAttribute("y1", "21");
        line.setAttribute("x2", "16.65");
        line.setAttribute("y2", "16.65");

        svg.appendChild(circle);
        svg.appendChild(line);

        const message = document.createElement("p");
        message.textContent = "Aucun menu ne correspond à vos critères.";

        const resetBtn = document.createElement("button");
        resetBtn.className = "btn btn--ghost btn--sm";
        resetBtn.textContent = "Réinitialiser les filtres";
        resetBtn.addEventListener("click", resetFilters);

        emptyBox.appendChild(svg);
        emptyBox.appendChild(message);
        emptyBox.appendChild(resetBtn);
        container.appendChild(emptyBox);

        return;
    }

    // Cards (user values escaped via _esc)
    container.innerHTML = menus.map((menu, i) => {
        const prix    = parseFloat(menu.prix_par_personne || 0).toFixed(0);
        const titre   = _esc(menu.titre || "");
        const desc    = _esc(menu.description || "");
        const slug    = _esc(menu.slug || "");
        const imgUrl  = _esc(menu.image_url || "");
        const theme   = _esc(menu.theme_nom || "");
        const color   = _esc(menu.theme_couleur || "#D4AF37");
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
 * Escape HTML to prevent XSS in JS-rendered content.
 */
function _esc(str) {
    const d = document.createElement("div");
    d.textContent = String(str);
    return d.innerHTML;
}