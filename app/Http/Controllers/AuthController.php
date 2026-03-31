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
        include PROJECT_ROOT . 'views/auth/login.php';
    }

    /**
     * Traiter la connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return; // Le router affichera la vue
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
            $GLOBALS['login_errors'] = $errors;
            return; // Le router affichera la vue avec les erreurs
        }

        // Authentifier
        $user = $this->userModel->authenticate($email, $password);
        
        if (!$user) {
            $errors[] = 'Email ou mot de passe incorrect';
            $GLOBALS['login_errors'] = $errors;
            return; // Le router affichera la vue avec les erreurs
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
        include PROJECT_ROOT . 'views/auth/register.php';
    }

    /**
     * Traiter l'inscription
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return; // Le router affichera la vue
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirm'] ?? '';
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
        if (strlen($password) < 8) {
            $errors[] = 'Mot de passe doit avoir au minimum 8 caractères';
        }
        // Valider mot de passe fort
        if (!empty($password)) {
            $passwordValidation = validateStrongPassword($password);
            if (!$passwordValidation['valid']) {
                $errors[] = 'Le mot de passe ne respecte pas les critères de sécurité:';
                $errors = array_merge($errors, $passwordValidation['errors']);
            }
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }

        if (!empty($errors)) {
            $GLOBALS['register_errors'] = $errors;
            return; // Le router affichera la vue avec les erreurs
        }

        // Créer l'utilisateur
        $userId = $this->userModel->create($name, $email, $password);
        
        if (!$userId) {
            $errors[] = 'Cet email est déjà utilisé';
            $GLOBALS['register_errors'] = $errors;
            return; // Le router affichera la vue avec les erreurs
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
