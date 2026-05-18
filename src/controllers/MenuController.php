<?php

/**
 * ============================================================
 * Vite & Gourmand — MenuController (v1.2.0)
 * ============================================================
 * Chemin : src/controllers/MenuController.php
 *
 * Nouveautés v1.2.0 :
 *  + Validation recherche texte (max 100 char, strip_tags)
 *  + Validation id_regime (int positif)
 *  + Validation allergenes_exclus[] (array d'int, max 14 items)
 * ============================================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/MenuModel.php';

class MenuController
{
    private MenuModel $menuModel;

    public function __construct()
    {
        $pdo = getDbConnection();
        $this->menuModel = new MenuModel($pdo);
    }

    // ══════════════════════════════════════════════════════════
    // PAGE CATALOGUE
    // ══════════════════════════════════════════════════════════

    public function catalogue(): void
    {
        $filtres = $this->getFiltresCatalogue();
        $menus   = $this->menuModel->getAll($filtres);
        $themes  = $this->menuModel->getThemes();
        $regimes = $this->menuModel->getRegimes();
        $allergenesList = $this->menuModel->getAllergenesList();

        require_once __DIR__ . '/../views/catalogue/index.php';
    }

    // ══════════════════════════════════════════════════════════
    // PAGE DÉTAIL MENU
    // ══════════════════════════════════════════════════════════

    public function detail(): void
    {
        $slug = $this->getSlugFromRequest();

        if ($slug === null) {
            $this->redirect404('Slug manquant ou invalide.');
        }

        $menu = $this->menuModel->getBySlug($slug);

        if ($menu === false) {
            $this->redirect404("Menu « {$slug} » introuvable.");
        }

        $composition = $this->menuModel->getComposition($menu['id_menu']);
        $allergenes  = $this->menuModel->getAllergenes($menu['id_menu']);
        $platsParCategorie = $this->grouperParCategorie($composition);

        require_once __DIR__ . '/../views/catalogue/detail.php';
    }

    // ══════════════════════════════════════════════════════════
    // MÉTHODES PUBLIQUES — Helpers exposés au point d'entrée
    // ══════════════════════════════════════════════════════════

    /**
     * Récupère et nettoie tous les filtres depuis $_GET.
     * Sécurité : cast strict + whitelist + limites de taille.
     */
    public function getFiltresCatalogue(): array
    {
        $filtres = [];

        // ── Recherche texte (XSS protection) ─────────────────
        if (!empty($_GET['search']) && is_string($_GET['search'])) {
            $search = trim(strip_tags($_GET['search']));
            if (mb_strlen($search) > 0 && mb_strlen($search) <= 100) {
                $filtres['search'] = $search;
            }
        }

        // ── Thème ────────────────────────────────────────────
        if (!empty($_GET['theme']) && is_numeric($_GET['theme'])) {
            $filtres['id_theme'] = (int) $_GET['theme'];
        }

        // ── Régime alimentaire ───────────────────────────────
        if (!empty($_GET['regime']) && is_numeric($_GET['regime'])) {
            $filtres['id_regime'] = (int) $_GET['regime'];
        }

        // ── Prix max ─────────────────────────────────────────
        if (!empty($_GET['prix_max']) && is_numeric($_GET['prix_max'])) {
            $prixMax = (float) $_GET['prix_max'];
            if ($prixMax > 0 && $prixMax <= 1000) {
                $filtres['prix_max'] = $prixMax;
            }
        }

        // ── Nombre de personnes ──────────────────────────────
        if (!empty($_GET['nb_personnes']) && is_numeric($_GET['nb_personnes'])) {
            $nb = (int) $_GET['nb_personnes'];
            if ($nb > 0 && $nb <= 10000) {
                $filtres['nb_personnes'] = $nb;
            }
        }

        // ── Allergènes à exclure (array d'IDs) ───────────────
        if (!empty($_GET['allergenes']) && is_array($_GET['allergenes'])) {
            $ids = array_filter(
                array_map('intval', $_GET['allergenes']),
                fn($id) => $id > 0
            );
            $ids = array_slice(array_unique($ids), 0, 14);
            if (!empty($ids)) {
                $filtres['allergenes_exclus'] = array_values($ids);
            }
        }

        // ── Tri (whitelist stricte) ──────────────────────────
        $ordresAutorisés = ['prix_asc', 'prix_desc', 'titre'];
        if (!empty($_GET['ordre']) && in_array($_GET['ordre'], $ordresAutorisés, true)) {
            $filtres['ordre'] = $_GET['ordre'];
        }

        return $filtres;
    }

    // ══════════════════════════════════════════════════════════
    // MÉTHODES PRIVÉES
    // ══════════════════════════════════════════════════════════

    private function getSlugFromRequest(): ?string
    {
        if (empty($_GET['slug'])) {
            return null;
        }
        $slug = trim(strip_tags($_GET['slug']));
        if (!preg_match('/^[a-z0-9\-]{1,180}$/', $slug)) {
            return null;
        }
        return $slug;
    }

    private function grouperParCategorie(array $plats): array
    {
        $groupes = [
            'starter' => [],
            'main'    => [],
            'dessert' => [],
            'drink'   => [],
            'other'   => [],
        ];
        foreach ($plats as $plat) {
            $cat = $plat['categorie'] ?? 'other';
            if (isset($groupes[$cat])) {
                $groupes[$cat][] = $plat;
            }
        }
        return array_filter($groupes, fn($g) => !empty($g));
    }

    private function redirect404(string $message = ''): never
    {
        http_response_code(404);
        require_once __DIR__ . '/../views/errors/404.php';
        exit;
    }
}
