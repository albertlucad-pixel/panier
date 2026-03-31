<?php
// Mes listes de courses
$pageTitle = 'Mes listes';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

// PROJECT_ROOT est défini dans public/index.php
$shoppingList = new ShoppingList($pdo);

$userId = $_SESSION['user_id'];

try {
    $lists = $shoppingList->getListsByUser($userId);
} catch (Exception $e) {
    $lists = [];
    $_SESSION['error'] = 'Erreur lors du chargement des listes.';
}
?>

<div class="my-lists">
    <div class="page-header">
        <div>
            <h1>📋 Mes listes de courses</h1>
            <p>Gérez vos listes personnelles</p>
        </div>
        <a href="index.php?page=list-create" class="btn btn-success">
            ➕ Nouvelle liste
        </a>
    </div>

    <?php if (empty($lists)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h2>Aucune liste créée</h2>
            <p>Commencez par créer votre première liste de courses!</p>
            <a href="index.php?page=list-create" class="btn btn-success">
                ➕ Créer une liste
            </a>
        </div>
    <?php else: ?>
        <div class="lists-table">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Créée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lists as $list): ?>
                        <tr class="list-row">
                            <td class="name">
                                <strong><?php echo escape($list['name']); ?></strong>
                            </td>
                            <td class="description">
                                <?php echo escape($list['description'] ?? '-'); ?>
                            </td>
                            <td class="date">
                                <?php echo formatDateFR($list['created_at']); ?>
                            </td>
                            <td class="actions">
                                <a href="index.php?page=public-detail&id=<?php echo $list['id']; ?>" 
                                   class="btn-small" title="Voir">👁️</a>
                                <a href="index.php?page=list-edit&id=<?php echo $list['id']; ?>" 
                                   class="btn-small" title="Éditer">✏️</a>
                                <a href="index.php?page=list-delete&id=<?php echo $list['id']; ?>" 
                                   class="btn-small btn-danger-small"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette liste?');"
                                   title="Supprimer">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.my-lists {
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

.lists-table {
    background-color: white;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background-color: var(--light);
    border-bottom: 2px solid var(--border);
}

th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--dark);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background-color 0.2s;
}

tbody tr:hover {
    background-color: var(--light);
}

tbody tr:last-child {
    border-bottom: none;
}

td {
    padding: 1rem;
    color: var(--text);
    font-size: 0.9rem;
}

.name {
    font-weight: 600;
    color: var(--primary);
}

.description {
    color: var(--text-light);
    font-style: italic;
}

.count {
    text-align: center;
}

.count .badge {
    background-color: var(--primary);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status {
    text-align: center;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-completed {
    background-color: #dcfce7;
    color: #166534;
}

.status-pending {
    background-color: #fef3c7;
    color: #92400e;
}

.date {
    color: var(--text-light);
}

.actions {
    display: flex;
    gap: 0.5rem;
    text-align: center;
}

.btn-small {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 0.25rem;
    font-size: 1rem;
    border: 1px solid var(--border);
    background-color: white;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-small:hover {
    background-color: var(--light);
    border-color: var(--primary);
    transform: scale(1.1);
}

.btn-danger-small:hover {
    background-color: #fee2e2;
    border-color: var(--danger);
    color: var(--danger);
}

.stats-footer {
    text-align: right;
    padding: 1rem;
    background-color: var(--light);
    border-radius: var(--radius);
    border-top: 2px solid var(--border);
    color: var(--text-light);
}

.stats-footer strong {
    color: var(--dark);
}

@media (max-width: 768px) {
    table {
        font-size: 0.8rem;
    }

    th, td {
        padding: 0.75rem 0.5rem;
    }

    .description {
        display: none;
    }

    .date {
        display: none;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
