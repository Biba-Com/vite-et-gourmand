<?php

/**
 * ============================================================
 * Vite & Gourmand — Commande entity
 * ============================================================
 * Path: src/entities/Commande.php
 *
 * A plain PHP object that represents ONE customer order.
 * It holds data only: no SQL, no business rules, no display.
 *
 * Encapsulation: the properties are private. The outside world
 * reads them through getter methods and never touches them
 * directly. This keeps a Commande object always consistent.
 * ============================================================
 */

class Commande
{
    // ── Identity ─────────────────────────────────────────────
    /** Primary key. Null until the order is saved in the database. */
    private ?int $idCommande = null;

    /** Id of the customer who placed the order. */
    private int $idUtilisateur;

    // ── Delivery details ─────────────────────────────────────
    /** Event date and time, format 'Y-m-d H:i:s'. */
    private string $dateEvenement;
    private string $adresseLivraison;
    private string $codePostalLivraison;
    private string $villeLivraison;

    /** Distance from Bordeaux in km (0 when inside Bordeaux). */
    private float $distanceKm;

    /** Total number of guests for the order. */
    private int $nbPersonnes;

    // ── Amounts (in euros) ───────────────────────────────────
    private float $sousTotal;       // before discount and delivery
    private float $montantRemise;   // total discount
    private float $fraisLivraison;  // delivery fee
    private float $total;           // final amount to pay

    // ── Lifecycle ────────────────────────────────────────────
    /** Order status: pending, confirmed, ... Default: pending. */
    private string $statut;

    /**
     * Build a Commande from its values.
     * Called by the service once all amounts have been computed.
     */
    public function __construct(
        int $idUtilisateur,
        string $dateEvenement,
        string $adresseLivraison,
        string $codePostalLivraison,
        string $villeLivraison,
        float $distanceKm,
        int $nbPersonnes,
        float $sousTotal,
        float $montantRemise,
        float $fraisLivraison,
        float $total,
        string $statut = 'pending'
    ) {
        $this->idUtilisateur       = $idUtilisateur;
        $this->dateEvenement       = $dateEvenement;
        $this->adresseLivraison    = $adresseLivraison;
        $this->codePostalLivraison = $codePostalLivraison;
        $this->villeLivraison      = $villeLivraison;
        $this->distanceKm          = $distanceKm;
        $this->nbPersonnes         = $nbPersonnes;
        $this->sousTotal           = $sousTotal;
        $this->montantRemise       = $montantRemise;
        $this->fraisLivraison      = $fraisLivraison;
        $this->total               = $total;
        $this->statut              = $statut;
    }

    // ── Getters: read the data ───────────────────────────────
    public function getIdCommande(): ?int
    {
        return $this->idCommande;
    }
    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }
    public function getDateEvenement(): string
    {
        return $this->dateEvenement;
    }
    public function getAdresseLivraison(): string
    {
        return $this->adresseLivraison;
    }
    public function getCodePostalLivraison(): string
    {
        return $this->codePostalLivraison;
    }
    public function getVilleLivraison(): string
    {
        return $this->villeLivraison;
    }
    public function getDistanceKm(): float
    {
        return $this->distanceKm;
    }
    public function getNbPersonnes(): int
    {
        return $this->nbPersonnes;
    }
    public function getSousTotal(): float
    {
        return $this->sousTotal;
    }
    public function getMontantRemise(): float
    {
        return $this->montantRemise;
    }
    public function getFraisLivraison(): float
    {
        return $this->fraisLivraison;
    }
    public function getTotal(): float
    {
        return $this->total;
    }
    public function getStatut(): string
    {
        return $this->statut;
    }

    // ── The only setter: the database id, known after insert ─
    /** Called once, right after the order is inserted, to store its new id. */
    public function setIdCommande(int $idCommande): void
    {
        $this->idCommande = $idCommande;
    }
}
