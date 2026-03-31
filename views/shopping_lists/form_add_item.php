<?php
$pageTitle = 'Ajouter un article';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

// Récupérer l'ID de la liste
$listId = $_GET['id'] ?? null;
if (!$listId) {
    $_SESSION['error'] = 'Liste non trouvée.';
    header('Location: index.php?page=my-lists');
    exit;
}

try {
    // Charger la liste
    $shoppingList = new ShoppingList($pdo);
    $list = $shoppingList->getListById($listId);
    if (!$list) {
        $_SESSION['error'] = 'Cette liste n\'existe pas.';
        header('Location: index.php?page=my-lists');
        exit;
    }

    // Vérifier que c'est le propriétaire
    if ($list['user_id'] != $_SESSION['user_id']) {
        $_SESSION['error'] = 'Vous n\'avez pas la permission de modifier cette liste.';
        header('Location: index.php?page=my-lists');
        exit;
    }

    // Charger les produits de l'utilisateur
    $product = new Product($pdo);
    $myProducts = $product->getProductsByUser($_SESSION['user_id']);

    // Récupérer les produits déjà dans la liste pour les exclure
    $existingItems = $shoppingList->getListItems($listId);
    $existingProductIds = array_map(function($item) { return $item['product_id']; }, $existingItems);

    // Filtrer les produits pour exclure ceux déjà présents
    $availableProducts = array_filter($myProducts, function($product) use ($existingProductIds) {
        return !in_array($product['id'], $existingProductIds);
    });

} catch (Exception $e) {
    $_SESSION['error'] = 'Erreur: ' . $e->getMessage();
    header('Location: index.php?page=my-lists');
    exit;
}
?>

<div class="form-container">
    <div class="form-card">
        <h1>➕ Ajouter un article à "<?php echo escape($list['name']); ?>"</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($availableProducts)): ?>
            <div class="alert alert-info">
                ℹ️ Tous vos produits sont déjà dans cette liste. 
                <a href="index.php?page=product-create">Créer un nouveau produit</a>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="index.php?page=list&id=<?php echo $list['id']; ?>" class="btn btn-secondary">Retour à la liste</a>
            </div>
        <?php else: ?>
            <form method="POST" action="index.php?page=list-add-item" class="form-create">
                <input type="hidden" name="list_id" value="<?php echo $list['id']; ?>">

                <div class="form-group">
                    <label for="product_id">Sélectionnez un produit *</label>
                    <select id="product_id" name="product_id" required class="product-select">
                        <option value="">-- Choisir un produit --</option>
                        <?php foreach ($availableProducts as $prod): ?>
                            <option value="<?php echo $prod['id']; ?>">
                                <?php echo escape($prod['name']); ?> - <?php echo formatCurrency($prod['price']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantité *</label>
                    <input 
                        type="number" 
                        id="quantity" 
                        name="quantity" 
                        min="1" 
                        value="1"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="unit">Unité</label>
                    <input 
                        type="text" 
                        id="unit" 
                        name="unit" 
                        placeholder="Ex: pcs, kg, L"
                        value="pcs"
                        maxlength="50"
                    >
                </div>

                <div class="form-actions">
                    <a href="index.php?page=list&id=<?php echo $list['id']; ?>" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-success">Ajouter l'article</button>
                </div>
            </form>
        <?php endif; ?>
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
.form-group select {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
}

.form-group input[type="text"]:focus,
.form-group input[type="number"]:focus,
.form-group select:focus {
    outline: none;
    border-color: #6b7280;
    box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.1);
}

.form-group small {
    font-size: 12px;
    color: #6b7280;
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

.alert-success {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-info {
    background-color: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.btn {
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background-color: #4b5563;
}

.btn-success {
    background-color: #16a34a;
    color: white;
}

.btn-success:hover {
    background-color: #15803d;
}

.product-select {
    cursor: pointer;
}
</style>
