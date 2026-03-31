<?php
/**
 * Modèle ShoppingList
 * Gestion des listes de courses avec calcul automatique du coût total
 */

class ShoppingList {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer une nouvelle liste de courses
     * @param int $userId ID de l'utilisateur propriétaire
     * @param string $name Nom de la liste (obligatoire)
     * @param string $description Description (optionnel)
     * @return int|false ID de la liste créée ou false
     */
    public function create($userId, $name, $description = null) {
        $stmt = $this->pdo->prepare('
            INSERT INTO shopping_lists (user_id, name, description)
            VALUES (?, ?, ?)
        ');

        if ($stmt->execute([$userId, $name, $description])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Récupérer une liste par son ID
     * @param int $listId
     * @return array|false
     */
    public function getListById($listId) {
        $stmt = $this->pdo->prepare('
            SELECT * FROM shopping_lists WHERE id = ?
        ');
        $stmt->execute([$listId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer toutes les listes d'un utilisateur
     * @param int $userId
     * @return array
     */
    public function getListsByUser($userId) {
        $stmt = $this->pdo->prepare('
            SELECT * FROM shopping_lists 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer toutes les listes publiques (non complétées)
     * @return array
     */
    public function getPublicLists() {
        $stmt = $this->pdo->query('
            SELECT sl.*, u.name as creator_name, 
                   COUNT(li.id) as item_count,
                   COALESCE(SUM(i.price * li.quantity), 0) as total_cost
            FROM shopping_lists sl
            JOIN users u ON sl.user_id = u.id
            LEFT JOIN list_item li ON sl.id = li.shopping_list_id
            LEFT JOIN items i ON li.item_id = i.id
            WHERE sl.is_completed = FALSE
            GROUP BY sl.id, sl.user_id, sl.name, sl.description, sl.is_completed, sl.created_at, sl.updated_at, u.name
            ORDER BY sl.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour une liste
     * @param int $listId
     * @param int $userId Vérifier que l'utilisateur est propriétaire
     * @param array $data Données à mettre à jour
     * @return bool
     */
    public function update($listId, $userId, $data) {
        // Vérifier que l'utilisateur est propriétaire
        $list = $this->getListById($listId);
        if (!$list || $list['user_id'] != $userId) {
            return false;
        }

        $fields = [];
        $values = [];

        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $values[] = $data['name'];
        }
        if (isset($data['description'])) {
            $fields[] = 'description = ?';
            $values[] = $data['description'];
        }
        if (isset($data['is_completed'])) {
            $fields[] = 'is_completed = ?';
            $values[] = (int)$data['is_completed'];
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $values[] = $listId;

        $sql = 'UPDATE shopping_lists SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Supprimer une liste
     * @param int $listId
     * @param int $userId Vérifier que l'utilisateur est propriétaire OU admin
     * @param bool $isAdmin
     * @return bool
     */
    public function delete($listId, $userId, $isAdmin = false) {
        $list = $this->getListById($listId);
        if (!$list) {
            return false;
        }

        // Vérifier les permissions
        if (!$isAdmin && $list['user_id'] != $userId) {
            return false;
        }

        $stmt = $this->pdo->prepare('DELETE FROM shopping_lists WHERE id = ?');
        return $stmt->execute([$listId]);
    }

    /**
     * Ajouter un produit à une liste
     * @param int $listId
     * @param int $itemId
     * @param int $quantity Quantité souhaitée
     * @param string $unit Unité (pcs, kg, L, etc.)
     * @param string $notes Notes optionnelles
     * @return int|false ID de l'association ou false
     */
    public function addProductToList($listId, $itemId, $quantity = 1, $unit = 'pcs', $notes = null) {
        // Vérifier que le produit et la liste existent
        $list = $this->getListById($listId);
        if (!$list) {
            return false;
        }

        // Vérifier que le produit existe et appartient au même utilisateur
        $stmt = $this->pdo->prepare('SELECT id FROM items WHERE id = ? AND user_id = ?');
        $stmt->execute([$itemId, $list['user_id']]);
        if (!$stmt->fetch()) {
            return false;
        }

        // Vérifier si le produit est déjà dans la liste
        $stmt = $this->pdo->prepare('
            SELECT id FROM list_item 
            WHERE shopping_list_id = ? AND item_id = ?
        ');
        $stmt->execute([$listId, $itemId]);
        
        if ($stmt->fetch()) {
            // Produit déjà dans la liste, mettre à jour la quantité
            return $this->updateProductInList($listId, $itemId, $quantity, $unit, $notes);
        }

        // Ajouter le produit à la liste
        $stmt = $this->pdo->prepare('
            INSERT INTO list_item (shopping_list_id, item_id, quantity, unit, notes)
            VALUES (?, ?, ?, ?, ?)
        ');

        if ($stmt->execute([$listId, $itemId, $quantity, $unit, $notes])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Mettre à jour un produit dans une liste
     * @param int $listId
     * @param int $itemId
     * @param int $quantity
     * @param string $unit
     * @param string $notes
     * @return bool
     */
    public function updateProductInList($listId, $itemId, $quantity, $unit = 'pcs', $notes = null) {
        $stmt = $this->pdo->prepare('
            UPDATE list_item 
            SET quantity = ?, unit = ?, notes = ?, updated_at = NOW()
            WHERE shopping_list_id = ? AND item_id = ?
        ');

        return $stmt->execute([$quantity, $unit, $notes, $listId, $itemId]);
    }

    /**
     * Supprimer un produit d'une liste
     * @param int $listId
     * @param int $itemId
     * @return bool
     */
    public function removeProductFromList($listId, $itemId) {
        $stmt = $this->pdo->prepare('
            DELETE FROM list_item 
            WHERE shopping_list_id = ? AND item_id = ?
        ');

        return $stmt->execute([$listId, $itemId]);
    }

    /**
     * Récupérer les produits d'une liste avec coût unitaire
     * @param int $listId
     * @return array
     */
    public function getListItems($listId) {
        $stmt = $this->pdo->prepare('
            SELECT 
                li.id as item_link_id,
                li.quantity,
                li.unit,
                li.notes,
                li.is_checked,
                li.created_at,
                i.id as product_id,
                i.name,
                i.price,
                i.category,
                i.is_bio,
                i.nutri_score,
                (i.price * li.quantity) as subtotal
            FROM list_item li
            JOIN items i ON li.item_id = i.id
            WHERE li.shopping_list_id = ?
            ORDER BY i.name
        ');
        $stmt->execute([$listId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calculer le coût total d'une liste
     * @param int $listId
     * @return float
     */
    public function calculateTotalCost($listId) {
        $stmt = $this->pdo->prepare('
            SELECT COALESCE(SUM(i.price * li.quantity), 0) as total
            FROM list_item li
            JOIN items i ON li.item_id = i.id
            WHERE li.shopping_list_id = ?
        ');
        $stmt->execute([$listId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)$result['total'];
    }

    /**
     * Marquer un produit comme coché dans une liste
     * @param int $listItemId ID de l'association list_item
     * @param bool $checked
     * @return bool
     */
    public function toggleProductChecked($listItemId, $checked = true) {
        $stmt = $this->pdo->prepare('
            UPDATE list_item 
            SET is_checked = ?, updated_at = NOW()
            WHERE id = ?
        ');

        return $stmt->execute([(int)$checked, $listItemId]);
    }
}
?>
