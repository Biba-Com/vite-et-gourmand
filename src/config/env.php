<?php

/**
 * ============================================================
 * Vite & Gourmand — Chargeur de variables d'environnement
 * ============================================================
 * Chemin : src/config/env.php
 *
 * PHP ne lit pas les fichiers .env de lui-même : ce sont de
 * simples fichiers texte. Ce petit chargeur lit le .env situé
 * à la racine du projet et met chaque variable à disposition
 * via getenv(), sans aucune dépendance externe (pas de Composer).
 *
 * En production (Railway), les variables sont déjà fournies par
 * l'hébergeur : on ne les écrase donc jamais.
 * ============================================================
 */

/**
 * Lit un fichier .env et charge ses variables dans l'environnement.
 *
 * @param string $path Chemin absolu vers le fichier .env
 * @return void
 */
function loadEnv(string $path): void
{
    // Si le fichier n'existe pas (ex. en production), on ne fait rien
    if (!is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // On ignore les lignes vides et les commentaires (#)
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        // On découpe sur le PREMIER signe '=' uniquement
        // (important : l'URI MongoDB contient un '=' dans "?appName=ECF")
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $name  = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        // On retire les guillemets autour de la valeur, s'il y en a
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // En production, la vraie variable d'environnement a priorité :
        // si elle existe déjà, on ne l'écrase pas avec le .env
        if (getenv($name) !== false) {
            continue;
        }

        putenv("{$name}={$value}");
        $_ENV[$name]    = $value;
        $_SERVER[$name] = $value;
    }
}

// ── Chargement automatique du .env à la racine du projet ──
// env.php est dans src/config/, le .env est deux niveaux au-dessus
loadEnv(__DIR__ . '/../../.env');