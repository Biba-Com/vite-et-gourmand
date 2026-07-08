<?php

/**
 * ============================================================
 * Vite & Gourmand — Commande service
 * ============================================================
 * Path: src/services/CommandeService.php
 *
 * The "back office" of the org chart: it holds the business
 * rules and calculations for orders. No SQL here (that is the
 * repository's job) and no HTML (that is the controller's job).
 * ============================================================
 */

require_once __DIR__ . '/../repositories/CommandeRepository.php';

class CommandeService
{
    /** Flat delivery fee outside Bordeaux, in euros. */
    private const DELIVERY_BASE_FEE = 5.00;

    /** Extra cost per kilometre outside Bordeaux, in euros. */
    private const DELIVERY_PER_KM = 0.59;

    /** Minimum lead time before the event, in days. */
    private const MIN_LEAD_DAYS = 2;

    /**
     * Check the data submitted for an order.
     * Returns a list of error messages (empty array = all good).
     *
     * @param array $data Raw fields: adresse, code_postal, ville, date_evenement.
     * @return string[] Error messages, ready to show to the user.
     */
    public function validateOrderInput(array $data): array
    {
        $errors = [];

        if (empty($data['adresse'])) {
            $errors[] = "L'adresse de livraison est obligatoire.";
        }
        if (empty($data['code_postal'])) {
            $errors[] = "Le code postal est obligatoire.";
        }
        if (empty($data['ville'])) {
            $errors[] = "La ville est obligatoire.";
        }

        if (empty($data['date_evenement'])) {
            $errors[] = "La date de l'événement est obligatoire.";
        } else {
            $dateObj = DateTime::createFromFormat('Y-m-d', $data['date_evenement']);
            $minDate = new DateTime('+' . self::MIN_LEAD_DAYS . ' days');
            if (!$dateObj || $dateObj < $minDate) {
                $errors[] = "La date doit être au minimum 48h à l'avance.";
            }
        }

        return $errors;
    }

    /**
     * Compute the delivery fee.
     * Business rule: free inside Bordeaux; otherwise a flat fee
     * plus a per-kilometre cost.
     *
     * @param string $ville      Delivery city.
     * @param float  $distanceKm Distance from Bordeaux in km.
     * @return float Delivery fee in euros (0 inside Bordeaux).
     */
    public function computeDeliveryFee(string $ville, float $distanceKm): float
    {
        if (mb_strtolower(trim($ville)) === 'bordeaux') {
            return 0.0;
        }

        return round(self::DELIVERY_BASE_FEE + ($distanceKm * self::DELIVERY_PER_KM), 2);
    }

    /**
     * Add up the cart lines into order totals.
     *
     * @param array $panier Cart items (each with sous_total_brut, remise, nb_personnes).
     * @return array ['sousTotal' => float, 'montantRemise' => float, 'nbPersonnes' => int]
     */
    public function computeTotals(array $panier): array
    {
        $sousTotal     = 0.0;
        $montantRemise = 0.0;
        $nbPersonnes   = 0;

        foreach ($panier as $item) {
            $sousTotal     += $item['sous_total_brut'];
            $montantRemise += $item['remise'];
            $nbPersonnes   += $item['nb_personnes'];
        }

        return [
            'sousTotal'     => round($sousTotal, 2),
            'montantRemise' => round($montantRemise, 2),
            'nbPersonnes'   => $nbPersonnes,
        ];
    }

    /**
     * Place a new order: compute everything, build the entity,
     * and ask the repository to save it.
     *
     * This is the "conductor": it coordinates the other pieces but
     * delegates the details (calculations to itself, persistence to
     * the repository).
     *
     * @param CommandeRepository $repository    The data-access component.
     * @param int                $idUtilisateur Customer id.
     * @param array              $data          Validated form fields.
     * @param array              $panier        Cart items.
     * @return int The new order id.
     * @throws PDOException If saving fails.
     */
    public function placeOrder(
        CommandeRepository $repository,
        int $idUtilisateur,
        array $data,
        array $panier
    ): int {
        // 1) Distance + delivery fee (business decision) ─────
        $ville = $data['ville'];
        $distanceKm = 0.0;
        if (mb_strtolower(trim($ville)) !== 'bordeaux') {
            $distanceKm = $repository->getDistanceForCity($ville);
        }
        $fraisLivraison = $this->computeDeliveryFee($ville, $distanceKm);

        // 2) Totals from the cart ────────────────────────────
        $totals = $this->computeTotals($panier);
        $total  = round($totals['sousTotal'] - $totals['montantRemise'] + $fraisLivraison, 2);

        // 3) Build the order entity ──────────────────────────
        $dateHeure = $data['date_evenement'] . ' ' . ($data['heure_evenement'] ?? '12:00') . ':00';

        $commande = new Commande(
            idUtilisateur:       $idUtilisateur,
            dateEvenement:       $dateHeure,
            adresseLivraison:    $data['adresse'],
            codePostalLivraison: $data['code_postal'],
            villeLivraison:      $ville,
            distanceKm:          $distanceKm,
            nbPersonnes:         $totals['nbPersonnes'],
            sousTotal:           $totals['sousTotal'],
            montantRemise:       $totals['montantRemise'],
            fraisLivraison:      $fraisLivraison,
            total:               $total
        );

        // 4) Persist it through the repository ───────────────
        return $repository->create($commande, $panier, $data['notes'] ?? null);
    }
}