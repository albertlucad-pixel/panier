<?php
// Home - Page d'accueil = Affiche les listes publiques
$pageTitle = 'Accueil';

require_once __DIR__ . '/../config/database.php';
$shoppingList = new ShoppingList($pdo);

// Récupérer les listes publiques (non complétées)
try {
    $lists = $shoppingList->getPublicLists();
} catch (Exception $e) {
    $lists = [];
}
?>

<div class="home">
    <div class="page-header">
        <h1>Paniers Disponibles</h1>
        <p>Consultez les paniers de courses publics</p>
    </div>

    <?php if (empty($lists)): ?>
        <div class="empty-state">
            <h2>Aucun panier enregistré</h2>
            <p>Il n'y a pas encore de paniers publics. Créez le premier en vous connectant!</p>
            <?php if (!isLoggedIn()): ?>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="index.php?page=login" class="btn btn-primary">Se connecter</a>
                    <a href="index.php?page=register" class="btn btn-success">S'inscrire</a>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="lists-grid">
            <?php foreach ($lists as $list): ?>
                <div class="list-card">
                    <div class="list-header">
                        <h3><?php echo escape($list['name']); ?></h3>
                        <span class="list-status">
                            <?php echo $list['is_completed'] ? 'Complétée' : 'En cours'; ?>
                        </span>
                    </div>

                    <?php if (!empty($list['description'])): ?>
                        <p class="list-description">
                            <?php echo escape($list['description']); ?>
                        </p>
                    <?php endif; ?>

                    <div class="list-meta">
                        <span class="meta-item">
                            Par <?php echo escape($list['user_name'] ?? 'Anonyme'); ?>
                        </span>
                        <span class="meta-item">
                            <?php echo formatDateFR($list['created_at']); ?>
                        </span>
                    </div>

                    <div class="list-footer">
                        <a href="index.php?page=public-detail&id=<?php echo $list['id']; ?>" class="btn btn-outline">
                            Voir le panier
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (isLoggedIn()): ?>
            <div class="action-section">
                <a href="index.php?page=list-create" class="btn btn-primary btn-large">
                    Créer votre propre panier
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.home {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xl);
}

.page-header {
    text-align: center;
    margin-bottom: var(--spacing-lg);
}

.page-header h1 {
    font-size: 2.5rem;
    color: var(--dark);
    margin-bottom: var(--spacing-sm);
}

.page-header p {
    font-size: 1.1rem;
    color: var(--text-light);
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    background-color: white;
    border-radius: var(--radius);
    border: 2px dashed var(--border);
    box-shadow: var(--shadow);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-state h2 {
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-light);
    margin-bottom: 1.5rem;
}

.lists-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.list-card {
    background-color: white;
    border-radius: var(--radius);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
    transition: all 0.2s;
}

.list-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.list-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--spacing-md);
}

.list-header h3 {
    color: var(--dark);
    font-size: 1.1rem;
}

.list-status {
    font-size: 0.8rem;
    white-space: nowrap;
}

.list-description {
    color: var(--text-light);
    font-size: 0.9rem;
    font-style: italic;
}

.list-meta {
    display: flex;
    gap: var(--spacing-lg);
    font-size: 0.85rem;
    color: var(--text-light);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.list-footer {
    display: flex;
    gap: var(--spacing-sm);
}

.action-section {
    text-align: center;
    padding: var(--spacing-xl);
}
</style>
