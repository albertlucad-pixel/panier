<?php
$pageTitle = 'Supprimer un produit';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

// Récupérer le produit s'il n'est pas déjà défini
if (!isset($product) || !$product) {
    // PROJECT_ROOT est défini dans public/index.php
    $productModel = new Product($pdo);
    if (!isset($_GET['id'])) {
        header('Location: index.php?page=my-products');
        exit;
    }
    $product = $productModel->getProductById($_GET['id']);
    if (!$product || $product['user_id'] != $_SESSION['user_id']) {
        header('Location: index.php?page=my-products');
        exit;
    }
}
?>

<div class="delete-container">
    <div class="delete-card">
        <div class="delete-icon">⚠️</div>
        <h1>Supprimer ce produit ?</h1>
        
        <p class="product-name"><?php echo htmlspecialchars($product['name']); ?></p>
        
        <p class="warning-text">
            Cette action est <strong>irréversible</strong>. Le produit sera supprimé définitivement.
        </p>

        <form method="POST" action="index.php?page=product-delete&id=<?php echo $product['id']; ?>" class="delete-form">
            <div class="form-actions">
                <a href="index.php?page=product-detail&id=<?php echo $product['id']; ?>" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
            </div>
        </form>
    </div>
</div>

<style>
.delete-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 200px);
    padding: 20px;
}

.delete-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 400px;
    width: 100%;
    text-align: center;
}

.delete-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.delete-card h1 {
    margin: 0 0 20px 0;
    color: #1f2937;
    font-size: 24px;
}

.product-name {
    background: #fef2f2;
    border-left: 4px solid #dc2626;
    padding: 12px 16px;
    border-radius: 4px;
    margin: 20px 0;
    color: #dc2626;
    font-weight: 500;
}

.warning-text {
    color: #6b7280;
    margin: 20px 0;
    line-height: 1.6;
}

.delete-form {
    margin-top: 30px;
}

.form-actions {
    display: flex;
    gap: 10px;
}

.form-actions .btn {
    flex: 1;
    text-align: center;
}

.btn {
    padding: 10px 16px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    border: none;
    cursor: pointer;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-secondary {
    background-color: #9ca3af;
    color: white;
}

.btn-secondary:hover {
    background-color: #6b7280;
}

.btn-danger {
    background-color: #dc2626;
    color: white;
}

.btn-danger:hover {
    background-color: #991b1b;
}
</style>
