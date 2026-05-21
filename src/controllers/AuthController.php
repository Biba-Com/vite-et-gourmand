<?php

/**
 * ============================================================
 * Vite & Gourmand — AuthController (v2.0)
 * ============================================================
 * Chemin : src/controllers/AuthController.php
 *
 * Fusion : CSRF + brute-force + remember-me + audit
 * Aligné sur : table `utilisateur`, colonne `mot_de_passe`
 *
 * Sécurité :
 *  - bcrypt cost 12
 *  - CSRF token
 *  - session_regenerate_id (anti-fixation)
 *  - Protection brute force (5 tentatives / 5 min)
 *  - Anti-énumération (message volontairement vague)
 *  - MDP conforme Studi : 10 car, 1 maj, 1 min, 1 chiffre, 1 spécial
 * ============================================================
 */

class AuthController
{
    private UserModel $userModel;
    private PDO $pdo;

    public function __construct(UserModel $userModel, PDO $pdo)
    {
        $this->userModel = $userModel;
        $this->pdo       = $pdo;
    }

    // ══════════════════════════════════════════════════════════
    // CSRF
    // ══════════════════════════════════════════════════════════

    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        return !empty($token)
            && isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function regenerateCsrfToken(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // ══════════════════════════════════════════════════════════
    // CONNEXION
    // ══════════════════════════════════════════════════════════

    /**
     * @return array ['success' => bool, 'message' => string|null, 'user' => array|null]
     */
    public function login(array $post): array
    {
        // ── CSRF ────────────────────────────────────────────
        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ['success' => false, 'message' => 'Session expirée. Veuillez réessayer.'];
        }

        $email    = strtolower(trim(strip_tags($post['email'] ?? '')));
        $password = $post['password'] ?? '';
        $remember = !empty($post['remember']);

        // ── Validation ──────────────────────────────────────
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Adresse email invalide.'];
        }
        if (empty($password)) {
            return ['success' => false, 'message' => 'Le mot de passe est obligatoire.'];
        }

        // ── Protection brute force ──────────────────────────
        $key      = 'login_attempts_' . md5($email);
        $attempts = $_SESSION[$key] ?? 0;
        $lastTime = $_SESSION[$key . '_time'] ?? 0;

        if ($attempts >= 5 && (time() - $lastTime) < 300) {
            $minutes = ceil((300 - (time() - $lastTime)) / 60);
            return ['success' => false, 'message' => "Trop de tentatives. Réessayez dans {$minutes} minute(s)."];
        }

        // ── Vérification ────────────────────────────────────
        $user = $this->userModel->findByEmail($email);

        if ($user === false || !$this->userModel->verifyPassword($password, $user['mot_de_passe'])) {
            $_SESSION[$key]           = $attempts + 1;
            $_SESSION[$key . '_time'] = time();
            $this->logAudit(null, 'LOGIN_FAILED', "Email: {$email}");
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }

        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Votre compte est désactivé. Contactez-nous.'];
        }

        // ── Succès ──────────────────────────────────────────
        unset($_SESSION[$key], $_SESSION[$key . '_time']);
        session_regenerate_id(true);

        $_SESSION['user_id']     = $user['id_utilisateur'];
        $_SESSION['user_name']   = $user['prenom'];
        $_SESSION['user_nom']    = $user['nom'];
        $_SESSION['user_email']  = $user['email'];
        $_SESSION['user_role']   = $user['role'];
        $_SESSION['logged_in']   = true;

        self::regenerateCsrfToken();
        $this->logAudit($user['id_utilisateur'], 'LOGIN_SUCCESS', "Email: {$user['email']}");

        return ['success' => true, 'message' => null, 'user' => $user];
    }

    // ══════════════════════════════════════════════════════════
    // INSCRIPTION
    // ══════════════════════════════════════════════════════════

    /**
     * Retourne erreurs par champ pour la vue inscription.
     * @return array ['success' => bool, 'errors' => ['champ' => 'message'], 'user_id' => int|null]
     */
    public function register(array $post): array
    {
        $errors = [];

        // ── CSRF ────────────────────────────────────────────
        if (!self::verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ['success' => false, 'errors' => ['global' => 'Session expirée. Veuillez réessayer.'], 'user_id' => null];
        }

        // ── Extraction ──────────────────────────────────────
        $prenom       = trim(strip_tags($post['prenom'] ?? ''));
        $nom          = trim(strip_tags($post['nom'] ?? ''));
        $email        = strtolower(trim(strip_tags($post['email'] ?? '')));
        $telephone    = trim(strip_tags($post['telephone'] ?? ''));
        $password     = $post['password'] ?? '';
        $passwordConf = $post['password_confirm'] ?? '';
        $cgv          = $post['cgv'] ?? '';

        // ── Validations par champ ───────────────────────────

        // Prénom
        if (empty($prenom)) {
            $errors['prenom'] = 'Le prénom est obligatoire.';
        } elseif (mb_strlen($prenom) < 2 || mb_strlen($prenom) > 50) {
            $errors['prenom'] = 'Le prénom doit contenir entre 2 et 50 caractères.';
        }

        // Nom
        if (empty($nom)) {
            $errors['nom'] = 'Le nom est obligatoire.';
        } elseif (mb_strlen($nom) < 2 || mb_strlen($nom) > 50) {
            $errors['nom'] = 'Le nom doit contenir entre 2 et 50 caractères.';
        }

        // Email
        if (empty($email)) {
            $errors['email'] = 'L\'adresse email est obligatoire.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'adresse email n\'est pas valide.';
        }

        // Téléphone (optionnel, validé si fourni)
        if (!empty($telephone)) {
            $telClean = preg_replace('/[\s\.\-]/', '', $telephone);
            if (!preg_match('/^(\+33|0)[1-9]\d{8}$/', $telClean)) {
                $errors['telephone'] = 'Numéro invalide (format français attendu).';
            }
        }

        // Mot de passe — Conformité énoncé Studi page 5
        if (empty($password)) {
            $errors['password'] = 'Le mot de passe est obligatoire.';
        } else {
            $pwdErrors = [];
            if (mb_strlen($password) < 10)          $pwdErrors[] = '10 caractères minimum';
            if (!preg_match('/[A-Z]/', $password))   $pwdErrors[] = '1 majuscule';
            if (!preg_match('/[a-z]/', $password))   $pwdErrors[] = '1 minuscule';
            if (!preg_match('/\d/', $password))       $pwdErrors[] = '1 chiffre';
            if (!preg_match('/[^A-Za-z0-9]/', $password)) $pwdErrors[] = '1 caractère spécial';

            if (!empty($pwdErrors)) {
                $errors['password'] = 'Le mot de passe doit contenir : ' . implode(', ', $pwdErrors) . '.';
            }
        }

        // Confirmation
        if ($password !== $passwordConf) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas.';
        }

        // CGV
        if (empty($cgv)) {
            $errors['cgv'] = 'Vous devez accepter les conditions générales.';
        }

        // ── Email unique ────────────────────────────────────
        if (empty($errors['email'])) {
            $existing = $this->userModel->findByEmail($email);
            if ($existing !== false) {
                $errors['email'] = 'Cette adresse email est déjà utilisée.';
            }
        }

        // ── Retour si erreurs ───────────────────────────────
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'user_id' => null];
        }

        // ── Hashage + Insertion ─────────────────────────────
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        try {
            $userId = $this->userModel->create([
                'email'        => $email,
                'mot_de_passe' => $hashedPassword,
                'prenom'       => $prenom,
                'nom'          => $nom,
                'telephone'    => $telephone ?: null,
                'adresse'      => null,
                'code_postal'  => null,
                'ville'        => null,
            ]);

            self::regenerateCsrfToken();
            $this->logAudit($userId, 'REGISTER_SUCCESS', "Email: {$email}");

            // TODO: mail de bienvenue (énoncé Studi page 5)

            return ['success' => true, 'errors' => [], 'user_id' => $userId];

        } catch (PDOException $e) {
            error_log('AuthController::register — ' . $e->getMessage());
            return ['success' => false, 'errors' => ['global' => 'Erreur technique. Veuillez réessayer.'], 'user_id' => null];
        }
    }

    // ══════════════════════════════════════════════════════════
    // DÉCONNEXION
    // ══════════════════════════════════════════════════════════

    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $this->logAudit($userId, 'LOGOUT', '');

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS STATIQUES
    // ══════════════════════════════════════════════════════════

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
    }

    public static function hasRole(string $role): bool
    {
        return self::isLoggedIn() && ($_SESSION['user_role'] ?? '') === $role;
    }

    public static function requireAuth(string $redirectTo = '/connexion/'): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    public static function requireRole(string $role, string $redirectTo = '/'): void
    {
        self::requireAuth();
        if (!self::hasRole($role) && ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    // ══════════════════════════════════════════════════════════
    // LOG AUDIT
    // ══════════════════════════════════════════════════════════

    private function logAudit(?int $userId, string $action, string $detail): void
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO log_audit (id_utilisateur, action, type_entite, id_entite, nouvelles_valeurs, ip_address, user_agent, created_at)
                VALUES (:uid, :action, 'auth', :uid2, :detail, :ip, :ua, NOW())
            ");
            $stmt->execute([
                ':uid'    => $userId,
                ':action' => strtolower($action),
                ':uid2'   => $userId,
                ':detail' => json_encode(['detail' => $detail]),
                ':ip'     => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                ':ua'     => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
        } catch (PDOException $e) {
            error_log('logAudit: ' . $e->getMessage());
        }
    }
}
