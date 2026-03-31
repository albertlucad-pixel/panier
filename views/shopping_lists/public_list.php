<?php
// Listes publiques - Visible par tous (connectés ou pas)
$pageTitle = 'Listes publiques';

// Charger le modèle ShoppingList
// PROJECT_ROOT est défini dans public/index.php
$shoppingList = new ShoppingList($pdo);

// Récupérer les listes publiques (non complétées)
try {
    $lists = $shoppingList->getPublicLists();
} catch (Exception $e) {
    $lists = [];
    $_SESSION['error'] = 'Erreur lors du chargement des listes.';
}
?>

<div class="public-lists">
    <div class="page-header">
        <h1>🌍 Listes publiques</h1>
        <p>Consultez les listes de courses partagées par la communauté</p>
    </div>

    <?php if (empty($lists)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h2>Aucune liste publique</h2>
            <p>Il n'y a pas encore de listes publiques. Créez la première en vous connectant!</p>
            <?php if (!isLoggedIn()): ?>
                <a href="index.php?page=login" class="btn btn-primary">Se connecter</a>
                <a href="index.php?page=register" class="btn btn-secondary">S'inscrire</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="lists-grid">
            <?php foreach ($lists as $list): ?>
                <div class="list-card">
                    <div class="list-header">
                        <h3><?php echo escape($list['name']); ?></h3>
                        <span class="list-status">
                            <?php echo $list['is_completed'] ? '✅ Complétée' : '🔄 En cours'; ?>
                        </span>
                    </div>

                    <?php if (!empty($list['description'])): ?>
                        <p class="list-description">
                            <?php echo escape($list['description']); ?>
                        </p>
                    <?php endif; ?>

                    <div class="list-meta">
                        <span class="meta-item">
                            👤 <?php echo escape($list['user_name'] ?? 'Anonyme'); ?>
                        </span>
                        <span class="meta-item">
                            📅 <?php echo formatDateFR($list['created_at']); ?>
                        </span>
                    </div>

                    <div class="list-footer">
                        <a href="index.php?page=public-detail&id=<?php echo $list['id']; ?>" class="btn btn-outline">
                            Voir la liste
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (isLoggedIn()): ?>
            <div class="action-section">
                <a href="index.php?page=list-create" class="btn btn-success btn-large">
                    ➕ Créer votre propre liste
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
