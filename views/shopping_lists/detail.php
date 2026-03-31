<?php
// Détail d'une liste de courses - CORE FEATURE: Affiche le coût total ⭐
$pageTitle = 'Détail de la liste';

// Récupérer l'ID de la liste
$listId = $_GET['id'] ?? null;
if (!$listId) {
    $_SESSION['error'] = 'Liste non trouvée.';
    header('Location: index.php?page=public-lists');
    exit;
}

try {
    // Charger la liste
    $list = $shoppingList->getListById($listId);
    if (!$list) {
        $_SESSION['error'] = 'Cette liste n\'existe pas.';
        header('Location: index.php?page=public-lists');
        exit;
    }

    // Vérifier les permissions
    $userId = $_SESSION['user_id'] ?? null;
    $isOwner = $list['user_id'] == $userId;
    $isAdmin = isAdmin();
    $canEdit = $isOwner || $isAdmin;

    // Si ce n'est pas public et pas owner/admin, refuser l'accès
    // (À implémenter selon votre logique)

    // Charger les items de la liste
    $items = $shoppingList->getListItems($listId);

    // ⭐ CALCULER LE COÛT TOTAL
    $totalCost = $shoppingList->calculateTotalCost($listId);

} catch (Exception $e) {
    $_SESSION['error'] = 'Erreur lors du chargement de la liste: ' . $e->getMessage();
    header('Location: index.php?page=public-lists');
    exit;
}
?>

<div class="list-detail">
    <!-- En-tête de la liste -->
    <div class="list-detail-header">
        <div>
            <h1><?php echo escape($list['name']); ?></h1>
            <p class="list-owner">
                👤 Créée par: <?php echo escape($list['creator_name'] ?? 'Anonyme'); ?>
            </p>
            <?php if (!empty($list['description'])): ?>
                <p class="list-description">
                    📝 <?php echo escape($list['description']); ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if ($canEdit): ?>
            <div class="list-actions">
                <a href="index.php?page=list-edit&id=<?php echo $list['id']; ?>" class="btn btn-warning">
                    ✏️ Éditer
                </a>
                <a href="index.php?page=list-delete&id=<?php echo $list['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr?')">
                    🗑️ Supprimer
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Contenu principal -->
    <div class="list-content">
        <!-- Colonne gauche: Items -->
        <div class="list-items-section">
            <h2>Articles (<?php echo count($items); ?>)</h2>

            <?php if (empty($items)): ?>
                <div class="empty-items">
                    <p>Aucun article dans cette liste</p>
                </div>
            <?php else: ?>
                <div class="items-list">
                    <?php foreach ($items as $item): ?>
                        <div class="item-row <?php echo $item['is_checked'] ? 'item-checked' : ''; ?>">
                            <div class="item-info">
                                <div class="item-name">
                                    <?php echo escape($item['name']); ?>
                                </div>
                            </div>

                            <div class="item-price">
                                <div class="price"><?php echo formatCurrency($item['price']); ?></div>
                                <div class="subtotal">
                                    ×<?php echo $item['quantity']; ?> = <?php echo formatCurrency($item['subtotal']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Colonne droite: Résumé + Coût TOTAL ⭐ -->
        <div class="list-summary">
            <div class="summary-card">
                <h3>Résumé</h3>

                <div class="summary-line">
                    <span>Articles:</span>
                    <strong><?php echo count($items); ?></strong>
                </div>

                <div class="summary-divider"></div>

                <!-- ⭐ COÛT TOTAL - LE POINT FORT DE L'APP ⭐ -->
                <div class="total-cost-section">
                    <div class="total-label">💰 Coût Total:</div>
                    <div class="total-amount">
                        <?php echo formatCurrency($totalCost); ?>
                    </div>
                    <div class="total-hint">
                        Somme de tous les articles
                    </div>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-meta">
                    <div class="meta-item">
                        <span>Créée:</span>
                        <span><?php echo formatDateFR($list['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle checkbox pour marquer article comme acheté
    document.querySelectorAll('.toggle-item').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // À implémenter via AJAX plus tard
            console.log('Item ' + this.dataset.itemId + ' toggled');
        });
    });
</script>
