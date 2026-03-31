<?php
// Register - Formulaire d'inscription
$pageTitle = 'Inscription';

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    header('Location: index.php?page=dashboard');
    exit;
}

// Récupérer les erreurs du contrôleur ou de la session
$errors = $GLOBALS['register_errors'] ?? [];
if (isset($_SESSION['error'])) {
    $errors[] = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Inscription</h1>
            <p>Créez votre compte Panier</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div>❌ <?php echo escape($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register" class="auth-form">
            <div class="form-group">
                <label for="name">Nom complet</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       placeholder="Votre nom"
                       required
                       value="<?php echo escape($_POST['name'] ?? ''); ?>">
                <span class="form-hint">Votre nom d'utilisateur</span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       placeholder="votre@email.com"
                       required
                       value="<?php echo escape($_POST['email'] ?? ''); ?>">
                <span class="form-hint">Votre adresse email unique</span>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe fort</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="MyPass123@"
                       required
                       minlength="8">
                <span class="form-hint">
                    Doit contenir: 8+ caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 spécial (@$!%*?&)
                </span>
                <div style="margin-top: 0.5rem; padding: 0.75rem; background-color: #f0f9ff; border-radius: 0.375rem; border-left: 3px solid #0891b2; font-size: 0.85rem;">
                    <div style="color: #374151; margin-bottom: 0.25rem;"><strong>Exemple valide:</strong> MyPass123@ ou Secure@2024</div>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input type="password" 
                       id="password_confirm" 
                       name="password_confirm" 
                       placeholder="Confirmez votre mot de passe"
                       required
                       minlength="8">
                <span class="form-hint">Doit correspondre au mot de passe ci-dessus</span>
            </div>

            <button type="submit" class="btn btn-success btn-block">
                Créer mon compte
            </button>
        </form>

        <div class="auth-footer">
            <p>Déjà un compte? 
               <a href="index.php?page=login">Se connecter ici</a>
            </p>
        </div>
    </div>
</div>
