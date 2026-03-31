<?php
$pageTitle = 'Éditer un produit';

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

<div class="form-container">
    <div class="form-card">
        <h1>📝 Éditer un produit</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=product-edit&id=<?php echo $productData['id']; ?>" class="form-create">
            <div class="form-group">
                <label for="name">Nom du produit *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo htmlspecialchars($productData['name']); ?>"
                    required
                    maxlength="255"
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3"
                ><?php echo htmlspecialchars($productData['description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Prix (€) *</label>
                <input 
                    type="number" 
                    id="price" 
                    name="price" 
                    value="<?php echo number_format($productData['price'], 2, '.', ''); ?>"
                    step="0.01"
                    min="0"
                    required
                >
            </div>

            <div class="form-group">
                <label for="category">Catégorie</label>
                <input 
                    type="text" 
                    id="category" 
                    name="category" 
                    value="<?php echo htmlspecialchars($productData['category'] ?? ''); ?>"
                    maxlength="100"
                >
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_bio" value="1" <?php echo $productData['is_bio'] ? 'checked' : ''; ?>>
                    Produit biologique
                </label>
            </div>

            <div class="form-group">
                <label for="nutri_score">Score nutritionnel</label>
                <select id="nutri_score" name="nutri_score">
                    <option value="">-- Non spécifié --</option>
                    <option value="A" <?php echo $productData['nutri_score'] === 'A' ? 'selected' : ''; ?>>A (Excellent)</option>
                    <option value="B" <?php echo $productData['nutri_score'] === 'B' ? 'selected' : ''; ?>>B (Bon)</option>
                    <option value="C" <?php echo $productData['nutri_score'] === 'C' ? 'selected' : ''; ?>>C (Moyen)</option>
                    <option value="D" <?php echo $productData['nutri_score'] === 'D' ? 'selected' : ''; ?>>D (Mauvais)</option>
                    <option value="E" <?php echo $productData['nutri_score'] === 'E' ? 'selected' : ''; ?>>E (Très mauvais)</option>
                </select>
            </div>

            <div class="form-actions">
                <a href="index.php?page=product-detail&id=<?php echo $productData['id']; ?>" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-success">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<style>
.form-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 200px);
    padding: 20px;
}

.form-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 500px;
    width: 100%;
}

.form-card h1 {
    margin-bottom: 30px;
    color: #1f2937;
    font-size: 24px;
}

.form-create {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 500;
    color: #374151;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group textarea,
.form-group select {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
}

.form-group input[type="text"]:focus,
.form-group input[type="number"]:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #6b7280;
    box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.1);
}

.form-group label input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.form-actions .btn {
    flex: 1;
    text-align: center;
}

.alert {
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}
</style>
