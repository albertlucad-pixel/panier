<?php
$pageTitle = 'Tableau de bord Admin';

if (!isAdmin()) {
    $_SESSION['error'] = 'Accès refusé. Seul un administrateur peut accéder à cette page.';
    header('Location: index.php?page=dashboard');
    exit;
}
?>

<div class="admin-dashboard">
    <div class="page-header">
        <h1>🔧 Tableau de bord Administrateur</h1>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-label">Utilisateurs</div>
                <div class="stat-value"><?php echo count($users ?? []); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <div class="stat-label">Paniers</div>
                <div class="stat-value"><?php echo count($lists ?? []); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div class="stat-content">
                <div class="stat-label">Produits</div>
                <div class="stat-value"><?php echo count($products ?? []); ?></div>
            </div>
        </div>
    </div>

    <!-- Onglets -->
    <div class="admin-tabs">
        <div class="tab-buttons">
            <button class="tab-button active" data-tab="users">👥 Utilisateurs</button>
            <button class="tab-button" data-tab="lists">📋 Paniers</button>
            <button class="tab-button" data-tab="products">🛒 Produits</button>
        </div>

        <!-- Onglet Utilisateurs -->
        <div id="users" class="tab-content active">
            <h2>Liste des utilisateurs</h2>
            <?php if (!empty($users)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Inscrit le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo escape($user['name']); ?></td>
                                <td><?php echo escape($user['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo $user['role'] === 'admin' ? '🔑 Admin' : '👤 Utilisateur'; ?>
                                    </span>
                                </td>
                                <td><?php echo formatDateFR($user['created_at']); ?></td>
                                <td class="actions">
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="<?php echo BASE_URL; ?>/index.php?page=admin-delete-user&id=<?php echo $user['id']; ?>" 
                                           class="btn-small btn-danger-small"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?');"
                                           title="Supprimer">🗑️</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun utilisateur.</p>
            <?php endif; ?>
        </div>

        <!-- Onglet Paniers -->
        <div id="lists" class="tab-content">
            <h2>Liste de tous les paniers</h2>
            <?php if (!empty($lists)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Propriétaire</th>
                            <th>Articles</th>
                            <th>Créé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lists as $list): ?>
                            <tr>
                                <td><?php echo escape($list['name']); ?></td>
                                <td><?php echo escape($list['creator_name'] ?? 'N/A'); ?></td>
                                <td><?php echo isset($list['item_count']) ? $list['item_count'] : 0; ?></td>
                                <td><?php echo formatDateFR($list['created_at']); ?></td>
                                <td class="actions">
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=public-detail&id=<?php echo $list['id']; ?>" 
                                       class="btn-small" title="Voir">👁️</a>
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=admin-delete-list&id=<?php echo $list['id']; ?>" 
                                       class="btn-small btn-danger-small"
                                       onclick="return confirm('Êtes-vous sûr?');"
                                       title="Supprimer">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun panier.</p>
            <?php endif; ?>
        </div>

        <!-- Onglet Produits -->
        <div id="products" class="tab-content">
            <h2>Liste de tous les produits</h2>
            <?php if (!empty($products)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Créateur</th>
                            <th>Prix</th>
                            <th>Catégorie</th>
                            <th>Créé le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo escape($product['name']); ?></td>
                                <td><?php echo escape($product['creator_name'] ?? 'N/A'); ?></td>
                                <td><?php echo formatCurrency($product['price']); ?></td>
                                <td><?php echo escape($product['category'] ?? '-'); ?></td>
                                <td><?php echo formatDateFR($product['created_at']); ?></td>
                                <td class="actions">
                                    <a href="<?php echo BASE_URL; ?>/index.php?page=admin-delete-product&id=<?php echo $product['id']; ?>" 
                                       class="btn-small btn-danger-small"
                                       onclick="return confirm('Êtes-vous sûr?');"
                                       title="Supprimer">🗑️</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun produit.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    color: #1f2937;
    font-size: 2rem;
    margin: 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    font-size: 2.5rem;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 5px;
}

.stat-value {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1f2937;
}

.admin-tabs {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.tab-buttons {
    display: flex;
    border-bottom: 2px solid #e5e7eb;
}

.tab-button {
    flex: 1;
    padding: 15px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    color: #6b7280;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
}

.tab-button:hover {
    color: #1f2937;
    background-color: #f9fafb;
}

.tab-button.active {
    color: #10b981;
    border-bottom-color: #10b981;
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}

.tab-content h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #1f2937;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.admin-table thead {
    background-color: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
}

.admin-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #374151;
}

.admin-table tbody tr {
    border-bottom: 1px solid #e5e7eb;
}

.admin-table tbody tr:hover {
    background-color: #f9fafb;
}

.admin-table td {
    padding: 12px;
    vertical-align: middle;
}

.role-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.role-admin {
    background-color: #dbeafe;
    color: #1e40af;
}

.role-user {
    background-color: #dcfce7;
    color: #166534;
}

.actions {
    display: flex;
    gap: 5px;
}

.btn-small {
    padding: 4px 8px;
    background-color: #f3f4f6;
    color: #1f2937;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-small:hover {
    background-color: #e5e7eb;
}

.btn-danger-small {
    background-color: #fee2e2;
    color: #991b1b;
}

.btn-danger-small:hover {
    background-color: #fca5a5;
}

.alert {
    padding: 12px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}
</style>

<script>
document.querySelectorAll('.tab-button').forEach(button => {
    button.addEventListener('click', function() {
        // Masquer tous les onglets
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Enlever active de tous les boutons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Afficher l'onglet sélectionné
        const tabName = this.getAttribute('data-tab');
        document.getElementById(tabName).classList.add('active');
        this.classList.add('active');
    });
});
</script>
