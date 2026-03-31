<?php
// Dashboard - Tableau de bord utilisateur
$pageTitle = 'Tableau de bord';

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page.';
    header('Location: index.php?page=login');
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
?>

<div class="dashboard">
    <h1>Bienvenue, <?php echo escape($userName); ?>!</h1>
    
    <div class="dashboard-grid">
        <!-- Mes Listes -->
        <div class="dashboard-card">
            <h2>Mes listes de courses</h2>
            <p>Gérez vos listes de courses, ajoutez des articles et suivez vos dépenses.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=my-lists" class="btn btn-primary">Voir mes listes</a>
        </div>

        <!-- Mes Produits -->
        <div class="dashboard-card">
            <h2>Mes produits</h2>
            <p>Créez vos propres produits avec prix et détails (bio, nutri-score, etc).</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=my-products" class="btn btn-primary">Voir mes produits</a>
        </div>

        <!-- Listes Publiques -->
        <div class="dashboard-card">
            <h2>Listes publiques</h2>
            <p>Consultez les listes publiques créées par d'autres utilisateurs.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=public-lists" class="btn btn-primary">Voir les listes</a>
        </div>

        <!-- Créer Nouvelle Liste -->
        <div class="dashboard-card">
            <h2>Nouvelle liste</h2>
            <p>Créez rapidement une nouvelle liste de courses.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=list-create" class="btn btn-success">Créer une liste</a>
        </div>

        <!-- Créer Nouveau Produit -->
        <div class="dashboard-card">
            <h2>Nouveau produit</h2>
            <p>Ajoutez un nouveau produit à votre collection personnelle.</p>
            <a href="<?php echo BASE_URL; ?>/index.php?page=product-create" class="btn btn-success">Créer un produit</a>
        </div>

        <?php if (isAdmin()): ?>
            <!-- Admin Panel -->
            <div class="dashboard-card admin-card">
                <h2>Admin</h2>
                <p>Accès administrateur - vous pouvez supprimer les listes d'autres utilisateurs.</p>
                <a href="<?php echo BASE_URL; ?>/index.php?page=admin" class="btn btn-warning">Aller à l'admin</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Stats -->
    <div class="stats-section">
        <h2>Statistiques rapides</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="stat-lists">0</div>
                <div class="stat-label">Listes créées</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-products">0</div>
                <div class="stat-label">Produits créés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="stat-items">0</div>
                <div class="stat-label">Articles en total</div>
            </div>
        </div>
    </div>
</div>

<script>
    // Charger les stats (optionnel - peut être fait via AJAX plus tard)
    // Pour maintenant, c'est juste du design
</script>
