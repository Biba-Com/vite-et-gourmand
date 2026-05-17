<?php

/**
 * Controller: Auth
 * Path: src/controllers/AuthController.php
 * Sécurité : PDO préparées · bcrypt cost 12 · session regenerate · log_audit
 */

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        require_once BASE_PATH . '/config/database.php';
        $this->db = getDbConnection();
    }

    public function checkRememberMe(): void
    {
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
            $hashedToken = hash('sha256', $_COOKIE['remember_token']);
            $stmt = $this->db->prepare(
                'SELECT user_id FROM remember_tokens
                 WHERE token_hash = :token_hash AND expires_at > NOW() LIMIT 1'
            );
            $stmt->execute([':token_hash' => $hashedToken]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $stmtUser = $this->db->prepare(
                    'SELECT id, prenom, nom, email, role
                     FROM utilisateurs WHERE id = :id AND is_active = 1 LIMIT 1'
                );
                $stmtUser->execute([':id' => $row['user_id']]);
                $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['user_name']  = $user['prenom'];
                    $_SESSION['user_role']  = $user['role'];
                    $_SESSION['user_email'] = $user['email'];
                }
            }
        }
    }

    /* ================================================================
       CONNEXION
       ================================================================ */
    public function login(string $email, string $password, bool $remember = false): array
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT id, prenom, nom, email, password, role, is_active
                 FROM utilisateurs WHERE email = :email LIMIT 1'
            );
            $stmt->execute([':email' => strtolower(trim($email))]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                $this->logAudit(null, 'LOGIN_FAILED', "Email: $email");
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
            }

            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Votre compte est désactivé. Contactez-nous.'];
            }

            session_regenerate_id(true);
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['prenom'];
            $_SESSION['user_role']  = $user['role'];
            $_SESSION['user_email'] = $user['email'];

            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, [
                    'expires'  => time() + (30 * 24 * 3600),
                    'path'     => '/',
                    'secure'   => true,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
                $hashedToken = hash('sha256', $token);
                $stmtToken = $this->db->prepare(
                    'INSERT INTO remember_tokens (user_id, token_hash, expires_at, created_at)
                     VALUES (:user_id, :token_hash, :expires_at, NOW())'
                );
                $stmtToken->execute([
                    ':user_id'    => $user['id'],
                    ':token_hash' => $hashedToken,
                    ':expires_at' => date('Y-m-d H:i:s', time() + (30 * 24 * 3600)),
                ]);
            }

            $this->logAudit($user['id'], 'LOGIN_SUCCESS', "Email: {$user['email']}");
            return ['success' => true, 'user' => $user];

        } catch (PDOException $e) {
            error_log('AuthController::login — ' . $e->getMessage());
            return ['success' => false, 'message' => 'Une erreur technique est survenue. Veuillez réessayer.'];
        }
    }

    /* ================================================================
       INSCRIPTION
       ================================================================ */
    public function register(array $data): array
    {
        $email    = strtolower(trim($data['email'] ?? ''));
        $prenom   = trim($data['prenom'] ?? '');
        $nom      = trim($data['nom'] ?? '');
        $password = $data['password'] ?? '';
        $tel      = trim($data['telephone'] ?? '');

        $stmt = $this->db->prepare('SELECT id FROM utilisateurs WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);

        if ($stmt->fetch()) {
            return ['success' => false, 'field' => 'email', 'message' => 'Cette adresse email est déjà utilisée.'];
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO utilisateurs (prenom, nom, email, password, telephone, role, is_active, created_at)
                 VALUES (:prenom, :nom, :email, :password, :telephone, "client", 1, NOW())'
            );
            $stmt->execute([
                ':prenom'    => htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'),
                ':nom'       => htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'),
                ':email'     => $email,
                ':password'  => $hashedPassword,
                ':telephone' => $tel ?: null,
            ]);

            $newId = (int) $this->db->lastInsertId();
            $this->logAudit($newId, 'REGISTER_SUCCESS', "Email: $email");

            session_regenerate_id(true);
            $_SESSION['user_id']    = $newId;
            $_SESSION['user_name']  = $prenom;
            $_SESSION['user_role']  = 'client';
            $_SESSION['user_email'] = $email;

            return ['success' => true, 'user_id' => $newId];

        } catch (PDOException $e) {
            error_log('AuthController::register — ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création du compte. Veuillez réessayer.'];
        }
    }

    /* ================================================================
       DÉCONNEXION
       ================================================================ */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $this->logAudit($userId, 'LOGOUT', '');

        if (isset($_COOKIE['remember_token'])) {
            $hashedToken = hash('sha256', $_COOKIE['remember_token']);
            $stmt = $this->db->prepare('DELETE FROM remember_tokens WHERE token_hash = :token_hash');
            $stmt->execute([':token_hash' => $hashedToken]);
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }

    /* ================================================================
       LOG AUDIT
       ================================================================ */
    private function logAudit(?int $userId, string $action, string $detail): void
    {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO log_audit (user_id, action, detail, ip_address, created_at)
                 VALUES (:user_id, :action, :detail, :ip, NOW())'
            );
            $stmt->execute([
                ':user_id' => $userId,
                ':action'  => $action,
                ':detail'  => $detail,
                ':ip'      => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);
        } catch (PDOException $e) {
            error_log('logAudit error: ' . $e->getMessage());
        }
    }
}
