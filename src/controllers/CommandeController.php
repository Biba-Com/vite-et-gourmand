<?php

/**
 * ============================================================
 * Vite & Gourmand — Commande controller
 * ============================================================
 * Path: src/controllers/CommandeController.php
 *
 * The "front desk" of the org chart: it receives the user's
 * request and returns a response. It does NOT calculate prices
 * (that is the service) and does NOT run SQL (that is the
 * repository). It only coordinates and reports back.
 * ============================================================
 */

require_once __DIR__ . '/../services/CommandeService.php';
require_once __DIR__ . '/../repositories/CommandeRepository.php';

class CommandeController
{
    private CommandeService    $service;
    private CommandeRepository $repository;

    /**
     * @param PDO $pdo Database connection, passed to the repository.
     */
    public function __construct(PDO $pdo)
    {
        $this->service    = new CommandeService();
        $this->repository = new CommandeRepository($pdo);
    }

    /**
     * Handle the submission of the order form.
     *
     * @param array $post   The raw $_POST data.
     * @param int   $userId The logged-in customer id.
     * @param array $panier The cart items from the session.
     * @return array ['success' => bool, 'errors' => string[], 'orderId' => ?int]
     */
    public function submitOrder(array $post, int $userId, array $panier): array
    {
        // 1) Collect and clean the form fields ───────────────
        $data = [
            'adresse'         => trim(strip_tags($post['adresse_livraison'] ?? '')),
            'code_postal'     => trim(strip_tags($post['code_postal']       ?? '')),
            'ville'           => trim(strip_tags($post['ville']             ?? '')),
            'date_evenement'  => trim(strip_tags($post['date_evenement']    ?? '')),
            'heure_evenement' => trim(strip_tags($post['heure_evenement']   ?? '12:00')),
            'notes'           => trim(strip_tags($post['notes']             ?? '')),
        ];

        // 2) Ask the service to validate ─────────────────────
        $errors = $this->service->validateOrderInput($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'orderId' => null];
        }

        // 3) Ask the service to place the order ──────────────
        try {
            $orderId = $this->service->placeOrder($this->repository, $userId, $data, $panier);
            return ['success' => true, 'errors' => [], 'orderId' => $orderId];

        } catch (PDOException $e) {
            error_log('CommandeController::submitOrder — ' . $e->getMessage());
            return [
                'success' => false,
                'errors'  => ['Erreur technique. Veuillez réessayer.'],
                'orderId' => null,
            ];
        }
    }

    /**
     * Load an order that the customer is still allowed to modify.
     *
     * @param int $idCommande Order id.
     * @param int $userId     The logged-in customer id.
     * @return array|null The order row, or null when it does not exist,
     *                    belongs to someone else or is no longer pending.
     */
    public function getEditableOrder(int $idCommande, int $userId): ?array
    {
        $order = $this->repository->findByIdForUser($idCommande, $userId);

        if ($order === null || !$this->service->canBeModified($order)) {
            return null;
        }

        $order['nb_personnes_min'] = $this->minimumGuests($idCommande);

        return $order;
    }

    /**
     * Handle the submission of the "modify my order" form.
     *
     * @param int   $idCommande Order id.
     * @param array $post       The raw $_POST data.
     * @param int   $userId     The logged-in customer id.
     * @return array ['success' => bool, 'errors' => string[]]
     */
    public function updateOrder(int $idCommande, array $post, int $userId): array
    {
        // 1) Collect and clean the form fields
        $data = [
            'nb_personnes'    => (int) ($post['nb_personnes'] ?? 0),
            'adresse'         => trim(strip_tags($post['adresse_livraison'] ?? '')),
            'code_postal'     => trim(strip_tags($post['code_postal']       ?? '')),
            'ville'           => trim(strip_tags($post['ville']             ?? '')),
            'date_evenement'  => trim(strip_tags($post['date_evenement']    ?? '')),
            'heure_evenement' => trim(strip_tags($post['heure_evenement']   ?? '12:00')),
        ];

        // 2) The order must still exist, belong to the user and be pending
        if ($this->getEditableOrder($idCommande, $userId) === null) {
            return ['success' => false, 'errors' => ['Cette commande ne peut plus être modifiée.']];
        }

        // 3) Ask the service to validate
        $lines  = $this->repository->findLines($idCommande);
        $errors = $this->service->validateUpdateInput($data, $lines);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // 4) Ask the service to recompute and save
        try {
            $updated = $this->service->updateOrder($this->repository, $idCommande, $userId, $data, $lines);

            return $updated
                ? ['success' => true, 'errors' => []]
                : ['success' => false, 'errors' => ['Cette commande ne peut plus être modifiée.']];

        } catch (PDOException $e) {
            error_log('CommandeController::updateOrder — ' . $e->getMessage());
            return ['success' => false, 'errors' => ['Erreur technique. Veuillez réessayer.']];
        }
    }

    /**
     * Highest menu minimum among the lines of an order (used by the view
     * for the "min" attribute of the guest count field).
     */
    private function minimumGuests(int $idCommande): int
    {
        $minimum = 1;
        foreach ($this->repository->findLines($idCommande) as $line) {
            $minimum = max($minimum, (int) $line['nb_personnes_min']);
        }

        return $minimum;
    }
}
