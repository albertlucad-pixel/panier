<?php
// Login - Formulaire de connexion
$pageTitle = 'Connexion';

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    header('Location: index.php?page=dashboard');
    exit;
}

// Récupérer les erreurs du contrôleur ou de la session
$errors = $GLOBALS['login_errors'] ?? [];
if (isset($_SESSION['error'])) {
    $errors[] = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Connexion</h1>
            <p>Connectez-vous à votre compte</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div>❌ <?php echo escape($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       placeholder="votre@email.com"
                       required
                       value="<?php echo escape($_POST['email'] ?? ''); ?>">
                <span class="form-hint">Utilisez votre adresse email</span>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Votre mot de passe"
                       required>
                <span class="form-hint">Votre mot de passe personnel</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Se connecter
            </button>
        </form>

        <div class="auth-footer">
            <p>Pas encore de compte? 
               <a href="index.php?page=register">S'inscrire ici</a>
            </p>
        </div>
    </div>
</div>
