<?php

/**
 * ============================================================
 * Vite & Gourmand — Synchronisation MySQL → MongoDB
 * ============================================================
 * Chemin : sync_stats.php (racine du projet)
 *
 * Lit les statistiques "commandes par menu" depuis MySQL
 * (la source de vérité) et les enregistre dans MongoDB via
 * le composant StatsRepository.
 *
 * À lancer en ligne de commande :  php sync_stats.php
 * ============================================================
 */

require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/config/mongodb.php';
require_once __DIR__ . '/src/models/StatsRepository.php';

echo "=== Synchronisation des stats MySQL -> MongoDB ===\n\n";

try {
    // 1) Lire les chiffres depuis MySQL ───────────────────────
    $pdo = getDbConnection();

    $sql = "
        SELECT
            m.id_menu,
            m.titre,
            COUNT(lc.id_ligne)              AS nb_commandes,
            COALESCE(SUM(lc.sous_total), 0) AS ca_total
        FROM menu m
        LEFT JOIN ligne_commande lc ON m.id_menu = lc.id_menu
        LEFT JOIN commande c        ON lc.id_commande = c.id_commande
            AND c.statut NOT IN ('cancelled')
        GROUP BY m.id_menu, m.titre
        ORDER BY nb_commandes DESC
    ";
    $lignes = $pdo->query($sql)->fetchAll();

    echo count($lignes) . " menu(s) lu(s) dans MySQL.\n";

    // 2) Écrire dans MongoDB via le composant NoSQL ───────────
    $repo = new StatsRepository(getMongoManager(), getMongoDbName());
    $n    = $repo->saveMenuStats($lignes);

    echo $n . " menu(s) enregistre(s) dans MongoDB.\n\n";

    // 3) Relire depuis MongoDB pour vérifier ──────────────────
    echo "Verification — contenu relu depuis MongoDB :\n";
    foreach ($repo->getCommandesParMenu() as $row) {
        echo sprintf(
            "  - %-30s : %3d commande(s), %9.2f EUR\n",
            $row['titre'],
            $row['nb_commandes'],
            $row['ca_total']
        );
    }

    echo "\nOK : synchronisation terminee.\n";
} catch (Exception $e) {
    echo "ECHEC : " . $e->getMessage() . "\n";
}
