<?php

/**
 * ============================================================
 * Vite & Gourmand — Espace Administrateur
 * ============================================================
 * Chemin : src/public/espace-admin/index.php
 * ============================================================
 */

session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/lang.php';
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../models/UserModel.php';

$currentLang = currentLang();
$isEn        = $currentLang === 'en';
$assetsBase  = '/assets';
$currentPage = 'espace-admin';
$pageTitle   = 'Espace Administrateur — Vite & Gourmand';
$pageDesc    = '';

// ── Accès réservé admin uniquement ──────────────────────
AuthController::requireAuth('/connexion/');
if (!AuthController::hasRole('admin')) {
    header('Location: /');
    exit;
}

$pdo       = getDbConnection();
$userModel = new UserModel($pdo);

// ── Messages flash ───────────────────────────────────────
$flashSuccess = null;
$flashError   = null;
if (!empty($_SESSION['flash_success'])) { $flashSuccess = $_SESSION['flash_success']; unset($_SESSION['flash_success']); }
if (!empty($_SESSION['flash_error']))   { $flashError   = $_SESSION['flash_error'];   unset($_SESSION['flash_error']); }

// ── Onglet actif ─────────────────────────────────────────
$tab  = $_GET['tab'] ?? 'employes';
$tabs = ['employes', 'commandes', 'avis', 'stats'];
if (!in_array($tab, $tabs)) $tab = 'employes';

// ── Liste des employés ───────────────────────────────────
$stmtEmployes = $pdo->prepare("
    SELECT id_utilisateur, prenom, nom, email, telephone,
           role, is_active, created_at
    FROM utilisateur
    WHERE role IN ('employee', 'admin')
    ORDER BY role DESC, nom ASC
");
$stmtEmployes->execute();
$employes = $stmtEmployes->fetchAll();

// ── Stats commandes par menu ─────────────────────────────
$stmtStats = $pdo->prepare("
    SELECT
        m.titre,
        COUNT(lc.id_ligne)   AS nb_commandes,
        SUM(lc.sous_total)   AS ca_total
    FROM menu m
    LEFT JOIN ligne_commande lc ON m.id_menu = lc.id_menu
    LEFT JOIN commande c        ON lc.id_commande = c.id_commande
        AND c.statut NOT IN ('cancelled')
    GROUP BY m.id_menu, m.titre
    ORDER BY nb_commandes DESC
");
$stmtStats->execute();
$statsMenus = $stmtStats->fetchAll();

// ── Commandes + Avis (réutiliser logique employé) ────────
$filtreStatut = trim(strip_tags($_GET['statut'] ?? ''));
$filtreClient = trim(strip_tags($_GET['client'] ?? ''));
$statutsValides = ['pending','confirmed','in_preparation','in_delivery','completed','cancelled'];

$sqlCmd = "
    SELECT
        c.id_commande, c.date_evenement, c.nb_personnes,
        c.ville_livraison, c.total, c.statut, c.created_at,
        u.prenom, u.nom, u.email, u.telephone,
        GROUP_CONCAT(m.titre ORDER BY lc.id_ligne SEPARATOR ', ') AS menus_titres
    FROM commande c
    JOIN utilisateur u          ON c.id_utilisateur = u.id_utilisateur
    LEFT JOIN ligne_commande lc ON c.id_commande    = lc.id_commande
    LEFT JOIN menu m            ON lc.id_menu       = m.id_menu
    WHERE 1=1
";
$params = [];

if (!empty($filtreStatut) && in_array($filtreStatut, $statutsValides)) {
    $sqlCmd .= " AND c.statut = :statut";
    $params[':statut'] = $filtreStatut;
}
if (!empty($filtreClient)) {
    $sqlCmd .= " AND (u.prenom LIKE :client OR u.nom LIKE :client2 OR u.email LIKE :client3)";
    $params[':client']  = '%' . $filtreClient . '%';
    $params[':client2'] = '%' . $filtreClient . '%';
    $params[':client3'] = '%' . $filtreClient . '%';
}

$sqlCmd .= " GROUP BY c.id_commande, c.date_evenement, c.nb_personnes,
             c.ville_livraison, c.total, c.statut, c.created_at,
             u.prenom, u.nom, u.email, u.telephone
             ORDER BY c.created_at DESC";

$stmtCmd = $pdo->prepare($sqlCmd);
$stmtCmd->execute($params);
$commandes = $stmtCmd->fetchAll();

// ── Avis en attente ──────────────────────────────────────
$stmtAvis = $pdo->prepare("
    SELECT
        a.id_avis, a.note, a.commentaire, a.created_at, a.statut,
        u.prenom, u.nom, c.date_evenement,
        GROUP_CONCAT(m.titre ORDER BY lc.id_ligne SEPARATOR ', ') AS menu_titre
    FROM avis a
    JOIN utilisateur u          ON a.id_utilisateur = u.id_utilisateur
    JOIN commande c             ON a.id_commande    = c.id_commande
    LEFT JOIN ligne_commande lc ON c.id_commande    = lc.id_commande
    LEFT JOIN menu m            ON lc.id_menu       = m.id_menu
    WHERE a.statut = 'pending'
    GROUP BY a.id_avis, a.note, a.commentaire, a.created_at,
             a.statut, u.prenom, u.nom, c.date_evenement
    ORDER BY a.created_at DESC
");
$stmtAvis->execute();
$avisEnAttente = $stmtAvis->fetchAll();

$statutLabels = [
    'pending'        => ['label' => 'En attente',     'color' => '#F59E0B'],
    'confirmed'      => ['label' => 'Confirmée',       'color' => '#3B82F6'],
    'in_preparation' => ['label' => 'En préparation', 'color' => '#8B5CF6'],
    'in_delivery'    => ['label' => 'En livraison',   'color' => '#06B6D4'],
    'completed'      => ['label' => 'Terminée',        'color' => '#10B981'],
    'cancelled'      => ['label' => 'Annulée',         'color' => '#EF4444'],
];

$prochainStatut = [
    'pending'        => 'confirmed',
    'confirmed'      => 'in_preparation',
    'in_preparation' => 'in_delivery',
    'in_delivery'    => 'completed',
];

// ════════════════════════════════════════════════════════
// POST — Actions
// ════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Créer un employé ─────────────────────────────
    if ($action === 'create_employee') {
        $email    = strtolower(trim(strip_tags($_POST['email']    ?? '')));
        $prenom   = trim(strip_tags($_POST['prenom']  ?? ''));
        $nom      = trim(strip_tags($_POST['nom']     ?? ''));
        $password = $_POST['password'] ?? '';

        $errors = [];
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide.';
        }
        if (empty($prenom)) $errors[] = 'Prénom obligatoire.';
        if (empty($nom))    $errors[] = 'Nom obligatoire.';
        if (mb_strlen($password) < 8) {
            $errors[] = 'Mot de passe : 8 caractères minimum.';
        }

        if (empty($errors)) {
            // Vérifier email unique
            $existing = $userModel->findByEmail($email);
            if ($existing) {
                $_SESSION['flash_error'] = 'Cette adresse email est déjà utilisée.';
            } else {
                try {
                    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $pdo->prepare("
                        INSERT INTO utilisateur
                            (email, mot_de_passe, prenom, nom, role, is_active, created_at)
                        VALUES
                            (:email, :mdp, :prenom, :nom, 'employee', 1, NOW())
                    ")->execute([
                        ':email'  => $email,
                        ':mdp'    => $hash,
                        ':prenom' => $prenom,
                        ':nom'    => $nom,
                    ]);

                    // TODO: Envoyer email notification (sans mot de passe)
                    $_SESSION['flash_success'] = "Compte employé créé pour {$prenom} {$nom}. Communiquez le mot de passe en main propre.";
                } catch (PDOException $e) {
                    error_log($e->getMessage());
                    $_SESSION['flash_error'] = 'Erreur technique.';
                }
            }
        } else {
            $_SESSION['flash_error'] = implode(' ', $errors);
        }
        header('Location: /espace-admin/?tab=employes');
        exit;
    }

    // ── Activer / Désactiver un employé ──────────────
    if ($action === 'toggle_employe') {
        $idUser    = (int) ($_POST['id_utilisateur'] ?? 0);
        $newStatus = (int) ($_POST['new_status']     ?? 0);

        // Protéger le compte admin
        if ($idUser === (int) $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'Vous ne pouvez pas désactiver votre propre compte.';
        } else {
            try {
                $pdo->prepare("
                    UPDATE utilisateur SET is_active = :status
                    WHERE id_utilisateur = :id AND role = 'employee'
                ")->execute([':status' => $newStatus, ':id' => $idUser]);
                $_SESSION['flash_success'] = 'Compte ' . ($newStatus ? 'activé' : 'désactivé') . ' avec succès.';
            } catch (PDOException $e) {
                $_SESSION['flash_error'] = 'Erreur technique.';
            }
        }
        header('Location: /espace-admin/?tab=employes');
        exit;
    }

    // ── Statut commande (même logique qu'employé) ────
    if ($action === 'update_statut') {
        $idCommande    = (int) ($_POST['id_commande']    ?? 0);
        $nouveauStatut = trim($_POST['nouveau_statut']   ?? '');
        $motif         = trim(strip_tags($_POST['motif'] ?? ''));

        if ($idCommande > 0 && in_array($nouveauStatut, $statutsValides)) {
            try {
                $pdo->beginTransaction();
                $stmtCurrent = $pdo->prepare("SELECT statut FROM commande WHERE id_commande = :id");
                $stmtCurrent->execute([':id' => $idCommande]);
                $current = $stmtCurrent->fetch();

                $pdo->prepare("UPDATE commande SET statut = :statut WHERE id_commande = :id")
                    ->execute([':statut' => $nouveauStatut, ':id' => $idCommande]);

                $pdo->prepare("
                    INSERT INTO historique_statut
                        (id_commande, statut_precedent, nouveau_statut, id_utilisateur, motif, created_at)
                    VALUES (:id, :prev, :new, :uid, :motif, NOW())
                ")->execute([
                    ':id'    => $idCommande,
                    ':prev'  => $current['statut'] ?? 'pending',
                    ':new'   => $nouveauStatut,
                    ':uid'   => (int) $_SESSION['user_id'],
                    ':motif' => $motif ?: 'Mis à jour par l\'administrateur',
                ]);

                $pdo->commit();
                $_SESSION['flash_success'] = 'Commande #' . str_pad($idCommande, 4, '0', STR_PAD_LEFT) . ' mise à jour.';
            } catch (PDOException $e) {
                $pdo->rollBack();
                $_SESSION['flash_error'] = 'Erreur technique.';
            }
        }
        header('Location: /espace-admin/?tab=commandes');
        exit;
    }

    // ── Modérer un avis ──────────────────────────────
    if (in_array($action, ['approve_avis', 'reject_avis'])) {
        $idAvis    = (int) ($_POST['id_avis'] ?? 0);
        $newStatut = $action === 'approve_avis' ? 'approved' : 'rejected';
        if ($idAvis > 0) {
            $pdo->prepare("
                UPDATE avis SET statut = :statut, id_moderateur = :uid, moderated_at = NOW()
                WHERE id_avis = :id
            ")->execute([':statut' => $newStatut, ':uid' => (int) $_SESSION['user_id'], ':id' => $idAvis]);
            $_SESSION['flash_success'] = 'Avis ' . ($newStatut === 'approved' ? 'validé' : 'refusé') . '.';
        }
        header('Location: /espace-admin/?tab=avis');
        exit;
    }
}

// ── Affichage ────────────────────────────────────────────
ob_start();
require_once __DIR__ . '/../../views/espace-admin/index.php';
$content = ob_get_clean();
require_once __DIR__ . '/../../views/layouts/base.php';
