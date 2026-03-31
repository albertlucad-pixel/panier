<?php
$pageTitle = 'Créer une liste de courses';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

// Charger les données du contrôleur si disponibles
$errors = $GLOBALS['errors'] ?? [];
$myProducts = $GLOBALS['myProducts'] ?? [];
if (empty($myProducts)) {
    $myProducts = (new Product($pdo))->getProductsByUser($_SESSION['user_id']);
}
?>

<div class="form-container">
    <div class="form-card">
        <h1>📋 Créer une liste de courses</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo escape($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=list-create" class="form-create">
            <div class="form-group">
                <label for="name">Nom de la liste *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="Ex: Courses du lundi"
                    required
                    maxlength="255"
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    placeholder="Ex: Produits pour la semaine"
                    rows="4"
                ></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_public" value="1">
                    Rendre cette liste publique
                </label>
                <small>Les autres utilisateurs pourront voir votre liste</small>
            </div>

            <!-- Section produits -->
            <div class="form-group">
                <label>Produits à ajouter</label>
                <small>Sélectionnez vos produits et indiquez les quantités</small>
                
                <?php if (!empty($myProducts)): ?>
                    <div class="products-table-container">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">✓</th>
                                    <th style="width: 50%;">Produit</th>
                                    <th style="width: 25%;">Prix</th>
                                    <th style="width: 20%;">Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myProducts as $product): ?>
                                    <tr class="product-row">
                                        <td class="checkbox-cell">
                                            <input type="checkbox" 
                                                   class="product-toggle"
                                                   data-product-id="<?php echo $product['id']; ?>"
                                                   id="product-<?php echo $product['id']; ?>">
                                        </td>
                                        <td class="product-name">
                                            <label for="product-<?php echo $product['id']; ?>">
                                                <?php echo escape($product['name']); ?>
                                            </label>
                                        </td>
                                        <td class="product-price">
                                            <?php echo formatCurrency($product['price']); ?>
                                        </td>
                                        <td class="quantity-cell" id="quantity-<?php echo $product['id']; ?>" style="display: none;">
                                            <input type="number" 
                                                   name="products[<?php echo $product['id']; ?>]"
                                                   class="quantity-input"
                                                   min="1" 
                                                   value="1"
                                                   placeholder="Qtté"
                                                   disabled>
                                            <input type="hidden" name="units[<?php echo $product['id']; ?>]" value="pcs">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        ℹ️ Vous n'avez pas encore créé de produits. <a href="index.php?page=product-create">Créer un produit</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="index.php?page=my-lists" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-success">Créer la liste</button>
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
.form-group textarea {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
}

.form-group input[type="text"]:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #6b7280;
    box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.1);
}

.form-group small {
    font-size: 12px;
    color: #6b7280;
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

.alert-info {
    background-color: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
    padding: 12px 16px;
    border-radius: 4px;
    margin: 10px 0;
}

.products-selection {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 12px;
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #d1d5db;
    padding: 12px;
    border-radius: 4px;
    background-color: #f9fafb;
}

.products-table-container {
    margin-top: 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    overflow: hidden;
    background-color: white;
}

.products-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.products-table thead {
    background-color: #f3f4f6;
    border-bottom: 2px solid #d1d5db;
}

.products-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #1f2937;
}

.products-table tbody tr {
    border-bottom: 1px solid #e5e7eb;
}

.products-table tbody tr:hover {
    background-color: #f9fafb;
}

.products-table td {
    padding: 12px;
    vertical-align: middle;
}

.checkbox-cell {
    text-align: center;
}

.checkbox-cell input[type="checkbox"] {
    cursor: pointer;
    width: 18px;
    height: 18px;
}

.product-name {
    color: #1f2937;
    font-weight: 500;
    cursor: pointer;
}

.product-name label {
    cursor: pointer;
    margin: 0;
}

.product-price {
    color: #10b981;
    font-weight: 600;
}

.quantity-cell input[type="number"],
.unit-cell select {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
    font-family: inherit;
}

.quantity-cell input[type="number"]:focus,
.unit-cell select:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.product-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 12px;
    background: white;
    border-radius: 4px;
    border: 1px solid #e5e7eb;
}

.product-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-weight: 500;
}

.product-checkbox input[type="checkbox"] {
    cursor: pointer;
    width: 18px;
    height: 18px;
}

.quantity-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
    min-width: 80px;
}

.unit-select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
    background-color: white;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer l'affichage/masquage des quantités quand on clique sur les checkboxes du tableau
    const productToggles = document.querySelectorAll('.product-toggle');
    
    productToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantityCell = document.getElementById('quantity-' + productId);
            const quantityInput = quantityCell ? quantityCell.querySelector('input[type="number"]') : null;
            
            if (this.checked) {
                // Afficher et activer la cellule de quantité
                if (quantityCell) {
                    quantityCell.style.display = 'table-cell';
                    if (quantityInput) {
                        quantityInput.disabled = false;
                        quantityInput.value = quantityInput.value || 1;
                    }
                }
            } else {
                // Masquer et désactiver la cellule de quantité
                if (quantityCell) {
                    quantityCell.style.display = 'none';
                    if (quantityInput) {
                        quantityInput.disabled = true;
                        quantityInput.value = 1;
                    }
                }
            }
        });
    });
});
</script>
