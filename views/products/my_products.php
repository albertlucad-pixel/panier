<?php
// Mes produits
$pageTitle = 'Mes produits';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

// PROJECT_ROOT est défini dans public/index.php
$product = new Product($pdo);

$userId = $_SESSION['user_id'];

try {
    $products = $product->getProductsByUser($userId);
} catch (Exception $e) {
    $products = [];
    $_SESSION['error'] = 'Erreur lors du chargement des produits.';
}
?>

<div class="my-products">
    <div class="page-header">
        <div>
            <h1>📦 Mes produits</h1>
            <p>Vos articles personnalisés pour vos listes</p>
        </div>
        <a href="index.php?page=product-create" class="btn btn-success">
            ➕ Nouveau produit
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h2>Aucun produit créé</h2>
            <p>Commencez par ajouter vos premiers produits à votre collection!</p>
            <a href="index.php?page=product-create" class="btn btn-success">
                ➕ Créer un produit
            </a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $item): ?>
                <div class="product-card">
                    <div class="product-header">
                        <h3><?php echo escape($item['name']); ?></h3>
                        <div class="product-price">
                            <?php echo formatCurrency($item['price']); ?>
                        </div>
                    </div>

                    <?php if (!empty($item['description'])): ?>
                        <p class="product-description">
                            <?php echo escape($item['description']); ?>
                        </p>
                    <?php endif; ?>

                    <div class="product-badges">
                        <?php if (!empty($item['category'])): ?>
                            <span class="badge-category">
                                📂 <?php echo escape($item['category']); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($item['is_bio']): ?>
                            <span class="badge-bio">🌱 Bio</span>
                        <?php endif; ?>

                        <?php if (!empty($item['nutri_score'])): ?>
                            <?php echo getNutriScoreBadge($item['nutri_score']); ?>
                        <?php endif; ?>
                    </div>

                    <div class="product-meta">
                        <span class="meta-date">
                            📅 <?php echo formatDateFR($item['created_at']); ?>
                        </span>
                    </div>

                    <div class="product-actions">
                        <a href="index.php?page=product-detail&id=<?php echo $item['id']; ?>" 
                           class="btn btn-small">👁️ Voir</a>
                        <a href="index.php?page=product-edit&id=<?php echo $item['id']; ?>" 
                           class="btn btn-small btn-warning">✏️ Éditer</a>
                        <a href="index.php?page=product-delete&id=<?php echo $item['id']; ?>" 
                           class="btn btn-small btn-danger"
                           onclick="return confirm('Êtes-vous sûr?');">🗑️ Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="products-stats">
            <p><strong><?php echo count($products); ?></strong> produit(s) dans votre collection</p>
        </div>
    <?php endif; ?>
</div>

<style>
.my-products {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
}

.page-header h1 {
    font-size: 2rem;
    color: var(--dark);
    margin-bottom: 0.5rem;
}

.page-header p {
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

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--spacing-lg);
}

.product-card {
    background-color: white;
    border-radius: var(--radius);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
    transition: all 0.2s;
}

.product-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.product-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: var(--spacing-md);
}

.product-header h3 {
    color: var(--dark);
    font-size: 1.1rem;
    flex: 1;
}

.product-price {
    font-size: 1.3rem;
    font-weight: bold;
    color: var(--success);
    white-space: nowrap;
}

.product-description {
    color: var(--text-light);
    font-size: 0.9rem;
    font-style: italic;
}

.product-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.badge-category,
.badge-bio {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    background-color: var(--light);
    color: var(--text);
    border: 1px solid var(--border);
}

.badge-bio {
    background-color: #f0fdf4;
    border-color: var(--success);
    color: var(--success);
}

.product-meta {
    padding: 0.75rem 0;
    border-top: 1px solid var(--border);
    font-size: 0.8rem;
    color: var(--text-light);
}

.meta-date {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: auto;
}

.btn-small {
    flex: 1;
    padding: 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.8rem;
    text-align: center;
    text-decoration: none;
    background-color: var(--light);
    color: var(--text);
    border: 1px solid var(--border);
    cursor: pointer;
    transition: all 0.2s;
}

.btn-small:hover {
    background-color: var(--primary);
    color: white;
    border-color: var(--primary);
}

.btn-warning:hover {
    background-color: var(--warning);
}

.btn-danger:hover {
    background-color: var(--danger);
}

.products-stats {
    text-align: right;
    padding: 1rem;
    background-color: var(--light);
    border-radius: var(--radius);
    color: var(--text-light);
}

.products-stats strong {
    color: var(--dark);
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}
</style>
