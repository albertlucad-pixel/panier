<?php
/**
 * AuthController
 * Gestion de l'authentification (login, register, logout)
 */

class AuthController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    /**
     * Afficher la page de connexion
     */
    public function showLogin() {
        include 'views/auth/login.php';
    }

    /**
     * Traiter la connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLogin();
            return;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $errors = [];

        // Validation
        if (empty($email)) {
            $errors[] = 'Email requis';
        }
        if (empty($password)) {
            $errors[] = 'Mot de passe requis';
        }

        if (!empty($errors)) {
            include 'views/auth/login.php';
            return;
        }

        // Authentifier
        $user = $this->userModel->authenticate($email, $password);
        
        if (!$user) {
            $errors[] = 'Email ou mot de passe incorrect';
            include 'views/auth/login.php';
            return;
        }

        // Créer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        header('Location: index.php?page=dashboard');
        exit;
    }

    /**
     * Afficher la page d'inscription
     */
    public function showRegister() {
        include 'views/auth/register.php';
    }

    /**
     * Traiter l'inscription
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegister();
            return;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $errors = [];

        // Validation
        if (empty($name)) {
            $errors[] = 'Nom requis';
        }
        if (empty($email)) {
            $errors[] = 'Email requis';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email invalide';
        }
        if (empty($password)) {
            $errors[] = 'Mot de passe requis';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Mot de passe doit avoir au minimum 6 caractères';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        if (!empty($errors)) {
            include 'views/auth/register.php';
            return;
        }

        // Créer l'utilisateur
        $userId = $this->userModel->create($name, $email, $password);
        
        if (!$userId) {
            $errors[] = 'Cet email est déjà utilisé';
            include 'views/auth/register.php';
            return;
        }

        // Auto-connexion
        $user = $this->userModel->getUserById($userId);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        header('Location: index.php?page=dashboard');
        exit;
    }

    /**
     * Déconnecter l'utilisateur
     */
    public function logout() {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
?>
