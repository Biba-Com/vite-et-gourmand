<?php

/**
 * ============================================================
 * Vite & Gourmand — UserModel (v2.0)
 * ============================================================
 * Chemin : src/models/UserModel.php
 *
 * Accès données utilisateurs — pattern Repository
 * Sécurité : bcrypt, prepared statements
 * ============================================================
 */

class UserModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Recherche un utilisateur actif par email.
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT id_utilisateur, email, mot_de_passe, prenom, nom,
                   telephone, role, adresse, code_postal, ville, is_active
            FROM utilisateur
            WHERE email = :email AND is_active = 1
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Recherche un utilisateur par ID.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT id_utilisateur, email, prenom, nom, telephone,
                   role, adresse, code_postal, ville, is_active
            FROM utilisateur
            WHERE id_utilisateur = :id AND is_active = 1
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Crée un nouvel utilisateur (rôle client par défaut).
     *
     * @return int L'ID du nouvel utilisateur
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO utilisateur
                (email, mot_de_passe, prenom, nom, telephone, role,
                 adresse, code_postal, ville, is_active, created_at)
            VALUES
                (:email, :mot_de_passe, :prenom, :nom, :telephone, 'client',
                 :adresse, :code_postal, :ville, 1, NOW())
        ");

        $stmt->execute([
            ':email'        => $data['email'],
            ':mot_de_passe' => $data['mot_de_passe'],
            ':prenom'       => $data['prenom'],
            ':nom'          => $data['nom'],
            ':telephone'    => $data['telephone'] ?? null,
            ':adresse'      => $data['adresse'] ?? null,
            ':code_postal'  => $data['code_postal'] ?? null,
            ':ville'        => $data['ville'] ?? null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met à jour les informations personnelles.
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE utilisateur SET
                prenom      = :prenom,
                nom         = :nom,
                telephone   = :telephone,
                adresse     = :adresse,
                code_postal = :code_postal,
                ville       = :ville,
                updated_at  = NOW()
            WHERE id_utilisateur = :id
        ");

        return $stmt->execute([
            ':prenom'       => $data['prenom'],
            ':nom'          => $data['nom'],
            ':telephone'    => $data['telephone'] ?? null,
            ':adresse'      => $data['adresse'] ?? null,
            ':code_postal'  => $data['code_postal'] ?? null,
            ':ville'        => $data['ville'] ?? null,
            ':id'           => $id,
        ]);
    }

    /**
     * Vérifie un mot de passe en clair contre le hash.
     */
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }
}
