<?php

/**
 * ============================================================
 * Vite & Gourmand — Database Configuration
 * ============================================================
 * Connexion PDO MySQL sécurisée
 * Chemin : src/config/database.php
 * 
 * Utilisation :
 *   require_once __DIR__ . '/../config/database.php';
 *   $pdo = getDbConnection();
 * ============================================================
 */

/**
 * Retourne une instance PDO configurée pour MySQL
 * 
 * Options de sécurité :
 * - ERRMODE_EXCEPTION : lève des exceptions au lieu de warnings silencieux
 * - FETCH_ASSOC : retourne des tableaux associatifs par défaut
 * - EMULATE_PREPARES false : utilise les vraies requêtes préparées MySQL
 *   (protection injection SQL native côté serveur)
 * 
 * @return PDO Instance de connexion à la base de données
 * @throws PDOException En cas d'échec de connexion
 */
function getDbConnection(): PDO
{
    // ── Paramètres de connexion ──────────────────────────────
    $host    = 'localhost';
    $dbname  = 'vite_et_gourmand';
    $user    = 'root';
    $pass    = '';          // Laragon : mot de passe vide par défaut
    $port    = 3306;
    $charset = 'utf8mb4';

    // ── DSN (Data Source Name) ───────────────────────────────
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

    // ── Options PDO sécurisées ───────────────────────────────
    $options = [
        // Lève une PDOException en cas d'erreur SQL
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

        // Retourne les résultats en tableau associatif ['colonne' => 'valeur']
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // Désactive l'émulation : MySQL gère les types nativement
        // Sécurité : empêche les injections SQL au niveau du driver
        PDO::ATTR_EMULATE_PREPARES   => false,

        // Timeout de connexion en secondes
        PDO::ATTR_TIMEOUT            => 5,
    ];

    // ── Connexion ────────────────────────────────────────────
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (PDOException $e) {
        // En production : logger l'erreur, ne JAMAIS afficher le message brut
        // error_log('Database connection failed: ' . $e->getMessage());
        // throw new RuntimeException('Service temporairement indisponible.');

        // En développement : message explicite pour debug
        throw new PDOException(
            'Erreur de connexion à la base de données : ' . $e->getMessage(),
            (int) $e->getCode(),
            $e
        );
    }
}
