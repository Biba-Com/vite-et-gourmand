<?php

/**
 * ============================================================
 * Vite & Gourmand — Composant d'accès aux données NoSQL
 * ============================================================
 * Chemin : src/models/StatsRepository.php
 *
 * Cette classe est le SEUL endroit de l'application qui parle
 * à MongoDB. C'est un "Repository" : un composant dédié à
 * l'accès à une source de données.
 *
 * Elle gère les statistiques "nombre de commandes par menu",
 * affichées sous forme de graphique dans l'espace administrateur.
 * La source de ces données est volontairement NoSQL (MongoDB),
 * comme demandé dans le cahier des charges.
 *
 * Elle reçoit sa connexion (le Manager) par son constructeur :
 * elle ne la crée pas elle-même. Cela la rend simple à tester
 * et indépendante de la configuration.
 * ============================================================
 */

class StatsRepository
{
    /** Connexion native fournie par l'extension mongodb */
    private MongoDB\Driver\Manager $manager;

    /** Nom de la base MongoDB (ex. vite_et_gourmand_stats) */
    private string $dbName;

    /** Nom de la collection (l'équivalent NoSQL d'une "table") */
    private string $collection = 'commandes_par_menu';

    /**
     * @param MongoDB\Driver\Manager $manager Connexion MongoDB
     * @param string                 $dbName  Nom de la base
     */
    public function __construct(MongoDB\Driver\Manager $manager, string $dbName)
    {
        $this->manager = $manager;
        $this->dbName  = $dbName;
    }

    /**
     * Construit le "namespace" MongoDB : "base.collection".
     * C'est l'équivalent de "nom_base.nom_table" en SQL.
     */
    private function namespace(): string
    {
        return $this->dbName . '.' . $this->collection;
    }

    /**
     * ÉCRITURE — Enregistre (ou met à jour) les stats par menu.
     *
     * On reçoit une liste de lignes calculées depuis MySQL, par ex :
     *   [
     *     ['id_menu' => 1, 'titre' => 'Menu Cocktail', 'nb_commandes' => 12, 'ca_total' => 300.00],
     *     ...
     *   ]
     *
     * Pour chaque menu, on fait un "upsert" : si le menu existe déjà
     * dans la collection on le met à jour, sinon on le crée. Ainsi on
     * peut relancer la synchro autant de fois qu'on veut sans doublon.
     *
     * @param array $lignes Lignes de stats venant de MySQL
     * @return int Nombre de menus traités
     */
    public function saveMenuStats(array $lignes): int
    {
        $bulk = new MongoDB\Driver\BulkWrite();

        foreach ($lignes as $ligne) {
            $bulk->update(
                // Filtre : on identifie le document par l'id du menu
                ['id_menu' => (int) $ligne['id_menu']],

                // Mise à jour : on (re)met toutes les valeurs
                ['$set' => [
                    'id_menu'      => (int) $ligne['id_menu'],
                    'titre'        => (string) $ligne['titre'],
                    'nb_commandes' => (int) $ligne['nb_commandes'],
                    'ca_total'     => (float) $ligne['ca_total'],
                    'updated_at'   => new MongoDB\BSON\UTCDateTime(),
                ]],

                // upsert : crée le document s'il n'existe pas encore
                ['upsert' => true]
            );
        }

        $this->manager->executeBulkWrite($this->namespace(), $bulk);

        return count($lignes);
    }

    /**
     * LECTURE — Retourne les commandes par menu, pour le graphique.
     *
     * Renvoie un tableau de la même forme que l'ancienne requête MySQL,
     * pour que la vue du graphique n'ait presque rien à changer :
     *   [
     *     ['titre' => 'Menu Cocktail', 'nb_commandes' => 12, 'ca_total' => 300.00],
     *     ...
     *   ]
     *
     * Les menus sont triés du plus commandé au moins commandé.
     *
     * @return array
     */
    public function getCommandesParMenu(): array
    {
        // Requête NoSQL : pas de filtre ([]), tri décroissant sur nb_commandes
        $query = new MongoDB\Driver\Query(
            [],
            ['sort' => ['nb_commandes' => -1]]
        );

        $cursor = $this->manager->executeQuery($this->namespace(), $query);

        $resultats = [];
        foreach ($cursor as $document) {
            // MongoDB rend des objets : on les remet sous forme de tableau
            $resultats[] = [
                'titre'        => $document->titre        ?? '',
                'nb_commandes' => $document->nb_commandes ?? 0,
                'ca_total'     => $document->ca_total      ?? 0,
            ];
        }

        return $resultats;
    }
}
