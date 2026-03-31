<?php
$pageTitle = 'Détails du produit';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

// PROJECT_ROOT est défini dans public/index.php
$product = new Product($pdo);

if (!isset($_GET['id'])) {
    header('Location: index.php?page=my-products');
    exit;
}

$productData = $product->getProductById($_GET['id']);

if (!$productData) {
    $_SESSION['error'] = 'Produit non trouvé';
    header('Location: index.php?page=my-products');
    exit;
}

// Vérifier que c'est le produit de l'utilisateur
if ($productData['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'Accès refusé';
    header('Location: index.php?page=my-products');
    exit;
}
?>

<div class="product-detail">
    <div class="detail-container">
        <div class="detail-header">
            <h1><?php echo htmlspecialchars($productData['name']); ?></h1>
            <div class="detail-actions">
                <a href="index.php?page=product-edit&id=<?php echo $productData['id']; ?>" class="btn btn-primary">Éditer</a>
                <a href="index.php?page=product-delete&id=<?php echo $productData['id']; ?>" class="btn btn-danger">Supprimer</a>
            </div>
        </div>

        <div class="detail-content">
            <div class="detail-section">
                <h3>Informations</h3>
                <table class="detail-table">
                    <tr>
                        <td class="label">Prix :</td>
                        <td class="value"><strong><?php echo number_format($productData['price'], 2, ',', ' '); ?>€</strong></td>
                    </tr>
                    <?php if (!empty($productData['description'])): ?>
                    <tr>
                        <td class="label">Description :</td>
                        <td class="value"><?php echo htmlspecialchars($productData['description']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($productData['category'])): ?>
                    <tr>
                        <td class="label">Catégorie :</td>
                        <td class="value"><?php echo htmlspecialchars($productData['category']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label">Biologique :</td>
                        <td class="value"><?php echo $productData['is_bio'] ? 'Oui' : 'Non'; ?></td>
                    </tr>
                    <?php if (!empty($productData['nutri_score'])): ?>
                    <tr>
                        <td class="label">Nutri-score :</td>
                        <td class="value"><strong><?php echo htmlspecialchars($productData['nutri_score']); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label">Créé le :</td>
                        <td class="value"><?php echo date('d/m/Y H:i', strtotime($productData['created_at'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="detail-footer">
            <a href="index.php?page=my-products" class="btn btn-secondary">Retour à mes produits</a>
        </div>
    </div>
</div>

<style>
.product-detail {
    padding: 40px 20px;
    max-width: 800px;
    margin: 0 auto;
}

.detail-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: 40px;
    border-bottom: 1px solid #e5e7eb;
    gap: 20px;
}

.detail-header h1 {
    margin: 0;
    color: #1f2937;
    font-size: 28px;
}

.detail-actions {
    display: flex;
    gap: 10px;
}

.detail-content {
    padding: 40px;
}

.detail-section h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #374151;
    font-size: 16px;
}

.detail-table {
    width: 100%;
    border-collapse: collapse;
}

.detail-table tr {
    border-bottom: 1px solid #e5e7eb;
}

.detail-table tr:last-child {
    border-bottom: none;
}

.detail-table td {
    padding: 12px 0;
}

.detail-table .label {
    width: 30%;
    color: #6b7280;
    font-weight: 500;
}

.detail-table .value {
    color: #1f2937;
}

.detail-footer {
    padding: 20px 40px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
}

.detail-footer .btn {
    flex: 1;
    text-align: center;
    margin-right: 10px;
}

.detail-footer .btn:last-child {
    margin-right: 0;
}

@media (max-width: 600px) {
    .detail-header {
        flex-direction: column;
    }
    
    .detail-actions {
        width: 100%;
    }
    
    .detail-actions .btn {
        flex: 1;
    }
}
</style>
