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
}