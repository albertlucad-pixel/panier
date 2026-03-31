<?php
$pageTitle = 'Éditer une liste';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

// PROJECT_ROOT est défini dans public/index.php
$shoppingList = new ShoppingList($pdo);

if (!isset($_GET['id'])) {
    header('Location: index.php?page=my-lists');
    exit;
}

// Utiliser les données du contrôleur si disponibles, sinon les charger
$listData = $GLOBALS['listData'] ?? $shoppingList->getListById($_GET['id']);

if (!$listData) {
    $_SESSION['error'] = 'Liste non trouvée';
    header('Location: index.php?page=my-lists');
    exit;
}

// Vérifier que c'est la liste de l'utilisateur
if ($listData['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'Accès refusé';
    header('Location: index.php?page=my-lists');
    exit;
}

// Charger les données du contrôleur si disponibles
$errors = $GLOBALS['errors'] ?? [];
$myProducts = $GLOBALS['myProducts'] ?? [];
$listItems = $GLOBALS['listItems'] ?? [];

// Créer un map des items pour accès rapide par product_id
$itemsMap = [];
foreach ($listItems as $item) {
    $itemsMap[$item['product_id']] = $item;
}
?>

<div class="form-container">
    <div class="form-card">
        <h1>📝 Éditer une liste</h1>
        
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

        <form method="POST" action="index.php?page=list-edit&id=<?php echo $listData['id']; ?>" class="form-create">
            <div class="form-group">
                <label for="name">Nom de la liste *</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo htmlspecialchars($listData['name']); ?>"
                    required
                    maxlength="255"
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                ><?php echo htmlspecialchars($listData['description'] ?? ''); ?></textarea>
            </div>

            <!-- Section produits -->
            <div class="form-group">
                <label>Produits de cette liste</label>
                <small>Cochez les produits à inclure ou décochez pour les retirer</small>
                
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
                                    <?php 
                                    // Vérifier si le produit est dans la liste
                                    $isInList = isset($itemsMap[$product['id']]);
                                    $item = $isInList ? $itemsMap[$product['id']] : null;
                                    ?>
                                    <tr class="product-row">
                                        <td class="checkbox-cell">
                                            <input type="checkbox" 
                                                   class="product-toggle"
                                                   data-product-id="<?php echo $product['id']; ?>"
                                                   id="product-<?php echo $product['id']; ?>"
                                                   <?php echo $isInList ? 'checked' : ''; ?>>
                                        </td>
                                        <td class="product-name">
                                            <label for="product-<?php echo $product['id']; ?>">
                                                <?php echo escape($product['name']); ?>
                                            </label>
                                        </td>
                                        <td class="product-price">
                                            <?php echo formatCurrency($product['price']); ?>
                                        </td>
                                        <td class="quantity-cell" id="quantity-<?php echo $product['id']; ?>" style="display: <?php echo $isInList ? 'table-cell' : 'none'; ?>;">
                                            <input type="number" 
                                                   name="products[<?php echo $product['id']; ?>]"
                                                   class="quantity-input"
                                                   min="1" 
                                                   value="<?php echo $isInList ? $item['quantity'] : '1'; ?>"
                                                   placeholder="Qtté"
                                                   <?php echo $isInList ? '' : 'disabled'; ?>>
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

.product-name {
    flex: 1;
    color: #1f2937;
}

.product-price {
    color: #10b981;
    font-weight: 600;
    font-size: 0.95rem;
}

.product-quantity {
    display: flex;
    gap: 10px;
    padding-left: 28px;
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
    // Initialiser: s'assurer que les inputs disabled/enabled sont corrects
    const productToggles = document.querySelectorAll('.product-toggle');
    
    productToggles.forEach(toggle => {
        const productId = toggle.dataset.productId;
        const quantityCell = document.getElementById('quantity-' + productId);
        const quantityInput = quantityCell ? quantityCell.querySelector('input[type="number"]') : null;
        
        // Initialiser l'état
        if (toggle.checked && quantityInput) {
            quantityInput.disabled = false;
        } else if (!toggle.checked && quantityInput) {
            quantityInput.disabled = true;
        }
        
        // Ajouter le listener de changement
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
                    }
                }
            } else {
                // Masquer et désactiver la cellule de quantité
                if (quantityCell) {
                    quantityCell.style.display = 'none';
                    if (quantityInput) {
                        quantityInput.disabled = true;
                    }
                }
            }
        });
    });
});
</script>
</style>
