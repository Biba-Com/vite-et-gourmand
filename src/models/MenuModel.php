<?php

/**
 * ============================================================
 * Vite & Gourmand — MenuModel (v1.2.0)
 * ============================================================
 * Chemin : src/models/MenuModel.php
 *
 * Rôle : Toutes les requêtes PDO liées à la table `menu`.
 * Pattern Repository — aucune logique métier, uniquement accès données.
 *
 * Méthodes publiques :
 *  - getAll(array $filtres)        → catalogue avec filtres avancés
 *  - getBySlug(string $slug)       → détail menu par slug
 *  - getComposition(int $id)       → plats composant un menu
 *  - getAllergenes(int $id)         → allergènes d'un menu
 *  - getThemes()                   → liste des thèmes (filtres)
 *  - getRegimes()                  → liste des régimes (filtres)
 *  - getAllergenesList()            → liste allergènes (filtre exclusion)
 * ============================================================
 */

class MenuModel
{
    private PDO $pdo;

    /**
     * Injection PDO — bonne pratique : pas de couplage fort
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ══════════════════════════════════════════════════════════
    // CATALOGUE — Liste des menus actifs avec filtres avancés
    // ══════════════════════════════════════════════════════════

    /**
     * Récupère tous les menus actifs avec filtres dynamiques.
     *
     * @param array $filtres :
     *   - 'search'             string   Recherche texte (titre/description)
     *   - 'id_theme'           int      Filtrer par thème
     *   - 'id_regime'          int      Filtrer par régime alimentaire
     *   - 'prix_max'           float    Prix maximum par personne
     *   - 'nb_personnes'       int      Nombre de personnes
     *   - 'allergenes_exclus'  int[]    IDs allergènes à exclure
     *   - 'ordre'              string   'prix_asc' | 'prix_desc' | 'titre'
     *
     * @return array Menus avec thème + résumé allergènes
     */
    public function getAll(array $filtres = []): array
    {
        $sql = "
            SELECT
                m.id_menu,
                m.titre,
                m.slug,
                m.description,
                m.prix_par_personne,
                m.nb_personnes_min,
                m.nb_personnes_max,
                m.image_url,
                t.id_theme,
                t.nom        AS theme_nom,
                t.couleur    AS theme_couleur,
                (
                    SELECT GROUP_CONCAT(DISTINCT a.nom ORDER BY a.nom SEPARATOR ', ')
                    FROM composition_menu cm
                    JOIN plat_allergene pa ON cm.id_plat     = pa.id_plat
                    JOIN allergene a       ON pa.id_allergene = a.id_allergene
                    WHERE cm.id_menu = m.id_menu
                ) AS allergenes_resume
            FROM menu m
            LEFT JOIN theme t ON m.id_theme = t.id_theme
            WHERE m.is_active = 1
        ";

        $params = [];

        // ── Recherche texte ──────────────────────────────────
        if (!empty($filtres['search'])) {
            $sql .= " AND (m.titre LIKE :search1 OR m.description LIKE :search2)";
            $params[':search1'] = '%' . $filtres['search'] . '%';
            $params[':search2'] = '%' . $filtres['search'] . '%';
        }

        // ── Filtre thème ─────────────────────────────────────
        if (!empty($filtres['id_theme'])) {
            $sql .= " AND m.id_theme = :id_theme";
            $params[':id_theme'] = (int) $filtres['id_theme'];
        }

        // ── Filtre régime (EXISTS) ───────────────────────────
        if (!empty($filtres['id_regime'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM menu_regime mr
                WHERE mr.id_menu   = m.id_menu
                  AND mr.id_regime = :id_regime
            )";
            $params[':id_regime'] = (int) $filtres['id_regime'];
        }

        // ── Filtre prix max ──────────────────────────────────
        if (!empty($filtres['prix_max'])) {
            $sql .= " AND m.prix_par_personne <= :prix_max";
            $params[':prix_max'] = (float) $filtres['prix_max'];
        }

        // ── Filtre nombre de personnes ───────────────────────
        if (!empty($filtres['nb_personnes'])) {
            $sql .= " AND m.nb_personnes_min <= :nb_personnes";
            $sql .= " AND (m.nb_personnes_max IS NULL OR m.nb_personnes_max >= :nb_personnes2)";
            $params[':nb_personnes']  = (int) $filtres['nb_personnes'];
            $params[':nb_personnes2'] = (int) $filtres['nb_personnes'];
        }

        // ── Exclusion allergènes (NOT EXISTS) ────────────────
        if (!empty($filtres['allergenes_exclus']) && is_array($filtres['allergenes_exclus'])) {
            $placeholders = [];
            foreach ($filtres['allergenes_exclus'] as $i => $idAllergene) {
                $key = ":aller_excl_{$i}";
                $placeholders[] = $key;
                $params[$key]   = (int) $idAllergene;
            }
            if (!empty($placeholders)) {
                $sql .= " AND NOT EXISTS (
                    SELECT 1 FROM composition_menu cm
                    JOIN plat_allergene pa ON cm.id_plat = pa.id_plat
                    WHERE cm.id_menu = m.id_menu
                      AND pa.id_allergene IN (" . implode(',', $placeholders) . ")
                )";
            }
        }

        // ── Tri (whitelist) ──────────────────────────────────
        $ordres = [
            'prix_asc'  => 'ORDER BY m.prix_par_personne ASC',
            'prix_desc' => 'ORDER BY m.prix_par_personne DESC',
            'titre'     => 'ORDER BY m.titre ASC',
        ];
        $ordre = $filtres['ordre'] ?? 'prix_asc';
        $sql .= ' ' . ($ordres[$ordre] ?? $ordres['prix_asc']);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ══════════════════════════════════════════════════════════
    // DÉTAIL — Un menu par son slug
    // ══════════════════════════════════════════════════════════

    /**
     * Récupère un menu complet par son slug SEO.
     * Inclut régimes + badges éco via GROUP_CONCAT (évite N+1).
     *
     * @param string $slug  Ex: "menu-elegance"
     * @return array|false  Menu ou false si introuvable
     */
    public function getBySlug(string $slug): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT
                m.id_menu,
                m.titre,
                m.slug,
                m.description,
                m.prix_par_personne,
                m.nb_personnes_min,
                m.nb_personnes_max,
                m.image_url,
                m.created_at,
                t.nom        AS theme_nom,
                t.couleur    AS theme_couleur,
                GROUP_CONCAT(DISTINCT r.nom ORDER BY r.nom SEPARATOR ', ')
                             AS regimes,
                GROUP_CONCAT(DISTINCT CONCAT(b.icone, ' ', b.nom) ORDER BY b.nom SEPARATOR ' | ')
                             AS badges_eco
            FROM menu m
            LEFT JOIN theme t              ON m.id_theme   = t.id_theme
            LEFT JOIN menu_regime mr       ON m.id_menu    = mr.id_menu
            LEFT JOIN regime r             ON mr.id_regime = r.id_regime
            LEFT JOIN menu_badge_eco mbe   ON m.id_menu    = mbe.id_menu
            LEFT JOIN badge_eco b          ON mbe.id_badge = b.id_badge
            WHERE m.slug = :slug AND m.is_active = 1
            GROUP BY m.id_menu
        ");

        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }

    // ══════════════════════════════════════════════════════════
    // COMPOSITION — Plats d'un menu
    // ══════════════════════════════════════════════════════════

    /**
     * Récupère les plats composant un menu, triés par ordre d'affichage.
     *
     * @param int $id_menu  Identifiant du menu
     * @return array        Plats avec catégorie (starter/main/dessert/drink/other)
     */
    public function getComposition(int $id_menu): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                p.id_plat,
                p.nom,
                p.description,
                p.categorie,
                p.image_url,
                cm.quantite,
                cm.ordre_affichage
            FROM composition_menu cm
            JOIN plat p ON cm.id_plat = p.id_plat
            WHERE cm.id_menu = :id_menu
              AND p.is_active = 1
            ORDER BY cm.ordre_affichage ASC, p.nom ASC
        ");

        $stmt->execute([':id_menu' => $id_menu]);
        return $stmt->fetchAll();
    }

    // ══════════════════════════════════════════════════════════
    // ALLERGÈNES D'UN MENU
    // ══════════════════════════════════════════════════════════

    /**
     * Allergènes présents dans un menu (via ses plats).
     * DISTINCT évite les doublons si plusieurs plats partagent un allergène.
     * Obligatoire : règlement UE n°1169/2011.
     *
     * @param int $id_menu  Identifiant du menu
     * @return array        [id_allergene, nom, icone]
     */
    public function getAllergenes(int $id_menu): array
    {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT
                a.id_allergene,
                a.nom,
                a.icone
            FROM composition_menu cm
            JOIN plat_allergene pa ON cm.id_plat     = pa.id_plat
            JOIN allergene a       ON pa.id_allergene = a.id_allergene
            WHERE cm.id_menu = :id_menu
            ORDER BY a.nom ASC
        ");

        $stmt->execute([':id_menu' => $id_menu]);
        return $stmt->fetchAll();
    }

    // ══════════════════════════════════════════════════════════
    // RÉFÉRENTIELS — Pour les filtres du catalogue
    // ══════════════════════════════════════════════════════════

    /**
     * Liste des thèmes pour le filtre catalogue.
     * @return array [id_theme, nom, couleur]
     */
    public function getThemes(): array
    {
        return $this->pdo
            ->query("SELECT id_theme, nom, couleur FROM theme ORDER BY nom ASC")
            ->fetchAll();
    }

    /**
     * Liste des régimes alimentaires pour le filtre catalogue.
     * @return array [id_regime, nom]
     */
    public function getRegimes(): array
    {
        return $this->pdo
            ->query("SELECT id_regime, nom FROM regime ORDER BY nom ASC")
            ->fetchAll();
    }

    /**
     * Liste de tous les allergènes pour le filtre d'exclusion.
     * @return array [id_allergene, nom, icone]
     */
    public function getAllergenesList(): array
    {
        return $this->pdo
            ->query("SELECT id_allergene, nom, icone FROM allergene ORDER BY nom ASC")
            ->fetchAll();
    }
}
