<?php

/**
 * ============================================================
 * Vite & Gourmand — Commande repository
 * ============================================================
 * Path: src/repositories/CommandeRepository.php
 *
 * The "archive" of the org chart: the ONLY place that talks to
 * the database for orders. It reads and writes rows, nothing
 * else — no business rules, no display.
 *
 * It works with Commande entity objects: it receives a Commande
 * and stores it. The price calculations happened earlier, in the
 * service; here we only persist the result.
 * ============================================================
 */

require_once __DIR__ . '/../entities/Commande.php';

class CommandeRepository
{
    /** Database connection (PDO), received from outside. */
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Persist a new order, its lines and its first history entry.
     *
     * Everything happens inside ONE transaction: either the three
     * inserts all succeed, or nothing is written at all. This avoids
     * a half-saved order (e.g. an order with no lines).
     *
     * @param Commande    $commande     Order to save (amounts already computed).
     * @param array       $lignesPanier Cart items (one per menu).
     * @param string|null $notes        Optional note applied to each line.
     * @return int The id of the newly created order.
     * @throws PDOException If anything fails (the caller decides what to do).
     */
    public function create(Commande $commande, array $lignesPanier, ?string $notes = null): int
    {
        try {
            $this->pdo->beginTransaction();

            // 1) Insert the order itself ─────────────────────────
            $stmt = $this->pdo->prepare("
                INSERT INTO commande (
                    id_utilisateur, date_evenement,
                    adresse_livraison, code_postal_livraison, ville_livraison,
                    distance_km, nb_personnes,
                    sous_total, montant_remise, frais_livraison, total,
                    statut, created_at
                ) VALUES (
                    :id_utilisateur, :date_evenement,
                    :adresse, :code_postal, :ville,
                    :distance_km, :nb_personnes,
                    :sous_total, :montant_remise, :frais_livraison, :total,
                    :statut, NOW()
                )
            ");

            $stmt->execute([
                ':id_utilisateur'  => $commande->getIdUtilisateur(),
                ':date_evenement'  => $commande->getDateEvenement(),
                ':adresse'         => $commande->getAdresseLivraison(),
                ':code_postal'     => $commande->getCodePostalLivraison(),
                ':ville'           => $commande->getVilleLivraison(),
                ':distance_km'     => $commande->getDistanceKm(),
                ':nb_personnes'    => $commande->getNbPersonnes(),
                ':sous_total'      => $commande->getSousTotal(),
                ':montant_remise'  => $commande->getMontantRemise(),
                ':frais_livraison' => $commande->getFraisLivraison(),
                ':total'           => $commande->getTotal(),
                ':statut'          => $commande->getStatut(),
            ]);

            // The database generated the id: store it back in the entity.
            $idCommande = (int) $this->pdo->lastInsertId();
            $commande->setIdCommande($idCommande);

            // 2) Insert one line per cart item ───────────────────
            $stmtLigne = $this->pdo->prepare("
                INSERT INTO ligne_commande (
                    id_commande, id_menu, quantite,
                    prix_unitaire, sous_total, notes
                ) VALUES (
                    :id_commande, :id_menu, :quantite,
                    :prix_unitaire, :sous_total, :notes
                )
            ");

            foreach ($lignesPanier as $item) {
                $stmtLigne->execute([
                    ':id_commande'   => $idCommande,
                    ':id_menu'       => (int) $item['id_menu'],
                    ':quantite'      => (int) $item['nb_personnes'],
                    ':prix_unitaire' => (float) $item['prix_unitaire'],
                    ':sous_total'    => (float) $item['sous_total'],
                    ':notes'         => $notes ?: null,
                ]);
            }

            // 3) Insert the initial status history ───────────────
            $stmtHist = $this->pdo->prepare("
                INSERT INTO historique_statut (
                    id_commande, statut_precedent, nouveau_statut,
                    id_utilisateur, motif, created_at
                ) VALUES (
                    :id_commande, 'pending', 'pending',
                    :id_utilisateur, 'Order created by the customer', NOW()
                )
            ");

            $stmtHist->execute([
                ':id_commande'    => $idCommande,
                ':id_utilisateur' => $commande->getIdUtilisateur(),
            ]);

            $this->pdo->commit();

            return $idCommande;
        } catch (PDOException $e) {
            // Undo everything if any insert failed, then let the
            // caller (service/controller) decide how to react.
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Return the distance from Bordeaux to a city, in km.
     *
     * Reads a cached value first; if missing, falls back to a small
     * built-in table (and caches it for 30 days). In production this
     * would call a real distance API.
     *
     * @param string $ville Delivery city.
     * @return float Distance in km.
     */
    public function getDistanceForCity(string $ville): float
    {
        $villeClean = mb_strtolower(trim($ville));

        // 1) Try the cache (non-expired) ─────────────────────
        $stmt = $this->pdo->prepare("
            SELECT distance_km FROM cache_distance
            WHERE LOWER(ville) = :ville AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([':ville' => $villeClean]);
        $row = $stmt->fetch();

        if ($row) {
            return (float) $row['distance_km'];
        }

        // 2) Fallback: estimated distances for main cities ───
        $distancesDefaut = [
            'merignac' => 8.0,
            'mérignac' => 8.0,
            'pessac' => 6.0,
            'talence'  => 4.0,
            'begles'   => 5.0,
            'bègles' => 5.0,
            'le bouscat' => 5.0,
            'libourne' => 30.0,
            'arcachon' => 65.0,
            'cap ferret' => 75.0,
            'blaye' => 50.0,
            'pauillac' => 55.0,
            'saint-émilion' => 35.0,
            'le taillan-médoc' => 14.0,
            'paris' => 580.0,
        ];
        $distance = $distancesDefaut[$villeClean] ?? 25.0;

        // 3) Cache the result for 30 days ────────────────────
        try {
            $stmtInsert = $this->pdo->prepare("
                INSERT INTO cache_distance (ville, distance_km, cached_at, expires_at)
                VALUES (:ville, :distance, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY))
                ON DUPLICATE KEY UPDATE
                    distance_km = :distance2,
                    cached_at   = NOW(),
                    expires_at  = DATE_ADD(NOW(), INTERVAL 30 DAY)
            ");
            $stmtInsert->execute([
                ':ville'     => $villeClean,
                ':distance'  => $distance,
                ':distance2' => $distance,
            ]);
        } catch (PDOException $e) {
            error_log('cache_distance insert: ' . $e->getMessage());
        }

        return $distance;
    }
}
