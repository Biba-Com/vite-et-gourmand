<?php

/**
 * ============================================================
 * Vite & Gourmand — Connexion MongoDB (NoSQL)
 * ============================================================
 * Chemin : src/config/mongodb.php
 *
 * Retourne un MongoDB\Driver\Manager : l'objet natif fourni
 * par l'extension mongodb, qui gère le dialogue avec la base.
 * Aucune dépendance Composer n'est nécessaire.
 *
 * L'adresse de connexion (avec le mot de passe) n'est jamais
 * écrite ici : elle vient du fichier .env via getenv().
 * ============================================================
 */

require_once __DIR__ . '/env.php';

/**
 * Retourne une connexion (Manager) vers MongoDB Atlas.
 *
 * @return MongoDB\Driver\Manager
 * @throws RuntimeException Si l'URI n'est pas configurée
 */
function getMongoManager(): MongoDB\Driver\Manager
{
    $uri = getenv('MONGO_URI');

    if ($uri === false || $uri === '') {
        // Message clair si le .env n'a pas été chargé ou la variable manque
        throw new RuntimeException(
            "MONGO_URI introuvable. Vérifiez le fichier .env à la racine du projet."
        );
    }

    // Le Manager n'ouvre pas la connexion tout de suite :
    // il se connectera réellement à la première requête.
    return new MongoDB\Driver\Manager($uri);
}

/**
 * Retourne le nom de la base de statistiques (depuis .env).
 *
 * @return string
 */
function getMongoDbName(): string
{
    return getenv('MONGO_DB') ?: 'vite_et_gourmand_stats';
}
