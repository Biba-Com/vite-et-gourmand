<?php

/**
 * ============================================================
 * Vite & Gourmand — Modification commande
 * ============================================================
 * Chemin : src/public/mes-commandes/modifier.php
 *
 * GET  → Affiche le formulaire pré-rempli
 * POST → Valide et met à jour la commande
 *
 * Règle énoncé page 7 : tout modifiable sauf le menu
 * Possible uniquement si statut = pending
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'mes-commandes';

$pageTitle = $isEn ? 'Edit Order — Vite & Gourmand' : 'Modifier la commande — Vite & Gourmand';

AuthController::requireAuth('/connexion/');

$idCommande = (int) ($_GET['id'] ?? $_POST['id_commande'] ?? 0);
$userId     = (int) $_SESSION['user_id'];

if ($idCommande <= 0) {
    header('Location: /mes-commandes/');
    exit;
}

$pdo = getDbConnection();

// ── Récupérer la commande ────────────────────────────────
$stmt = $pdo->prepare("
    SELECT c.*,
           GROUP_CONCAT(m.titre SEPARATOR ', ') AS menus_titres,
           SUM(lc.prix_unitaire * lc.quantite)  AS sous_total_brut
    FROM commande c
    LEFT JOIN ligne_commande lc ON c.id_commande = lc.id_commande
    LEFT JOIN menu m             ON lc.id_menu    = m.id_menu
    WHERE c.id_commande = :id AND c.id_utilisateur = :uid
    GROUP BY c.id_commande
    LIMIT 1
");
$stmt->execute([':id' => $idCommande, ':uid' => $userId]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: /mes-commandes/');
    exit;
}

// ── Vérifier que modification est possible ───────────────
if ($commande['statut'] !== 'pending') {
    $_SESSION['flash_error'] = 'Cette commande ne peut plus être modifiée.';
    header('Location: /mes-commandes/');
    exit;
}

$errors = [];

// ════════════════════════════════════════════════════════
// POST — Mise à jour
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nbPersonnes    = (int)   ($_POST['nb_personnes']    ?? 0);
    $dateEvenement  = trim(strip_tags($_POST['date_evenement']   ?? ''));
    $heureEvenement = trim(strip_tags($_POST['heure_evenement']  ?? '12:00'));
    $adresse        = trim(strip_tags($_POST['adresse_livraison'] ?? ''));
    $codePostal     = trim(strip_tags($_POST['code_postal']       ?? ''));
    $ville          = trim(strip_tags($_POST['ville']             ?? ''));

    // Validations
    if ($nbPersonnes <= 0) $errors[] = 'Le nombre de personnes est obligatoire.';

    if (empty($dateEvenement)) {
        $errors[] = 'La date est obligatoire.';
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d', $dateEvenement);
        $minDate = new DateTime('+2 days');
        if (!$dateObj || $dateObj < $minDate) {
            $errors[] = 'La date doit être au minimum 48h à l\'avance.';
        }
    }

    if (empty($adresse))    $errors[] = 'L\'adresse est obligatoire.';
    if (empty($codePostal)) $errors[] = 'Le code postal est obligatoire.';
    if (empty($ville))      $errors[] = 'La ville est obligatoire.';

    if (empty($errors)) {

        // Recalculer frais livraison
        $fraisLivraison = 0.0;
        if (strtolower(trim($ville)) !== 'bordeaux') {
            $fraisLivraison = 5.00;
        }

        // Recalculer total
        $sousTotal    = (float) $commande['sous_total_brut'];
        $remise       = $nbPersonnes >= 5 ? round($sousTotal * 0.10, 2) : 0;
        $total        = round($sousTotal - $remise + $fraisLivraison, 2);
        $dateHeure    = $dateEvenement . ' ' . $heureEvenement . ':00';

        try {
            $pdo->beginTransaction();

            // Mettre à jour la commande
            $pdo->prepare("
                UPDATE commande SET
                    date_evenement      = :date,
                    adresse_livraison   = :adresse,
                    code_postal_livraison = :cp,
                    ville_livraison     = :ville,
                    nb_personnes        = :nb,
                    frais_livraison     = :frais,
                    montant_remise      = :remise,
                    total               = :total,
                    updated_at          = NOW()
                WHERE id_commande = :id AND id_utilisateur = :uid
            ")->execute([
                ':date'   => $dateHeure,
                ':adresse'=> $adresse,
                ':cp'     => $codePostal,
                ':ville'  => $ville,
                ':nb'     => $nbPersonnes,
                ':frais'  => $fraisLivraison,
                ':remise' => $remise,
                ':total'  => $total,
                ':id'     => $idCommande,
                ':uid'    => $userId,
            ]);

            // Historique
            $pdo->prepare("
                INSERT INTO historique_statut
                    (id_commande, statut_precedent, nouveau_statut, id_utilisateur, motif, created_at)
                VALUES
                    (:id, 'pending', 'pending', :uid, 'Commande modifiée par le client', NOW())
            ")->execute([':id' => $idCommande, ':uid' => $userId]);

            $pdo->commit();

            $_SESSION['flash_success'] = 'Commande #' . str_pad($idCommande, 4, '0', STR_PAD_LEFT) . ' modifiée avec succès.';
            header('Location: /mes-commandes/');
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log('Modification commande: ' . $e->getMessage());
            $errors[] = 'Erreur technique. Veuillez réessayer.';
        }
    }
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/mes-commandes/modifier.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
