<?php
/**
 * Modèle User
 * Gestion des utilisateurs avec rôles (user, admin)
 */

class User {
    private $pdo;

    // Constantes pour les rôles
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouvel utilisateur
     * @param string $name Nom de l'utilisateur
     * @param string $email Email unique
     * @param string $password Mot de passe (sera hashé)
     * @return int|false ID de l'utilisateur créé ou false
     */
    public function create($name, $email, $password) {
        // Vérifier que l'email n'existe pas déjà
        if ($this->getUserByEmail($email)) {
            return false; // Email déjà utilisé
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare('
            INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, ?)
        ');
        
        if ($stmt->execute([$name, $email, $hashedPassword, self::ROLE_USER])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Récupérer un utilisateur par son email
     * @param string $email
     * @return array|false Données de l'utilisateur ou false
     */
    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un utilisateur par son ID
     * @param int $userId
     * @return array|false Données de l'utilisateur ou false
     */
    public function getUserById($userId) {
        $stmt = $this->pdo->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifier les identifiants de connexion
     * @param string $email
     * @param string $password
     * @return array|false Données de l'utilisateur si valide, false sinon
     */
    public function authenticate($email, $password) {
        $user = $this->getUserByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Vérifier si un utilisateur est admin
     * @param int $userId
     * @return bool
     */
    public function isAdmin($userId) {
        $user = $this->getUserById($userId);
        return $user && $user['role'] === self::ROLE_ADMIN;
    }

    /**
     * Assigner un rôle à un utilisateur (admin seulement)
     * @param int $userId
     * @param string $role 'user' ou 'admin'
     * @return bool
     */
    public function setRole($userId, $role) {
        if (!in_array($role, [self::ROLE_USER, self::ROLE_ADMIN])) {
            return false;
        }

        $stmt = $this->pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
        return $stmt->execute([$role, $userId]);
    }

    /**
     * Récupérer tous les utilisateurs (admin seulement)
     * @return array
     */
    public function getAllUsers() {
        $stmt = $this->pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprimer un utilisateur
     * @param int $userId
     * @return bool
     */
    public function delete($userId) {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$userId]);
    }
}
?>
