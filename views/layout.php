<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? escape($pageTitle) . ' - Panier' : 'Panier'; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/style.css">
</head>
<body>
    <!-- HEADER / NAV -->
    <header class="header">
        <div class="navbar">
            <div class="navbar-brand">
                <a href="<?php echo BASE_URL; ?>/index.php?page=home">
                    Panier
                </a>
            </div>
            
            <nav class="navbar-menu">
                <a href="<?php echo BASE_URL; ?>/index.php?page=home" class="nav-link">Accueil</a>

                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=my-lists" class="nav-link">Mes paniers</a>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=my-products" class="nav-link">Mes produits</a>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=profile" class="nav-link">Profil</a>
                    <?php if (isAdmin()): ?>
                        <a href="<?php echo BASE_URL; ?>/index.php?page=admin" class="nav-link" style="background-color: #fbbf24; color: #1f2937; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600;">👨‍💼 Admin</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=logout" class="nav-link nav-logout">Déconnexion</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=login" class="nav-link">Connexion</a>
                    <a href="<?php echo BASE_URL; ?>/index.php?page=register" class="nav-link nav-register">S'inscrire</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="container">
            <!-- Messages d'alerte -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    ✅ <?php echo escape($_SESSION['success']); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    ❌ <?php echo escape($_SESSION['error']); ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['info'])): ?>
                <div class="alert alert-info">
                    ℹ️ <?php echo escape($_SESSION['info']); ?>
                </div>
                <?php unset($_SESSION['info']); ?>
            <?php endif; ?>

            <!-- PAGE CONTENT -->
            <?php include $view; ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <p>&copy; 2026 Panier - Application de gestion des listes de courses</p>
    </footer>

    <script src="<?php echo BASE_URL; ?>/js/main.js"></script>
</body>
</html>
