<?php
/**
 * Modèle Product
 * Gestion des produits créés par les utilisateurs
 */

class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Créer un nouveau produit
     * @param int $userId ID de l'utilisateur créateur
     * @param string $name Nom du produit (obligatoire)
     * @param float $price Prix du produit (obligatoire)
     * @param array $optionalData Données optionnelles (description, category, bio, nutriScore, etc.)
     * @return int|false ID du produit créé ou false
     */
    public function create($userId, $name, $price, $optionalData = []) {
        $description = $optionalData['description'] ?? null;
        $category = $optionalData['category'] ?? null;
        $isBio = isset($optionalData['is_bio']) ? (int)$optionalData['is_bio'] : 0;
        $nutriScore = $optionalData['nutri_score'] ?? null; // A, B, C, D, E

        $stmt = $this->pdo->prepare('
            INSERT INTO items (user_id, name, description, price, category, is_bio, nutri_score)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');

        if ($stmt->execute([$userId, $name, $description, $price, $category, $isBio, $nutriScore])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Récupérer un produit par son ID
     * @param int $productId
     * @return array|false
     */
    public function getProductById($productId) {
        $stmt = $this->pdo->prepare('
            SELECT * FROM items WHERE id = ?
        ');
        $stmt->execute([$productId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les produits d'un utilisateur
     * @param int $userId
     * @return array
     */
    public function getProductsByUser($userId) {
        $stmt = $this->pdo->prepare('
            SELECT * FROM items 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour un produit
     * @param int $productId
     * @param int $userId Vérifier que l'utilisateur est le propriétaire
     * @param array $data Données à mettre à jour
     * @return bool
     */
    public function update($productId, $userId, $data) {
        // Vérifier que l'utilisateur est propriétaire
        $product = $this->getProductById($productId);
        if (!$product || $product['user_id'] != $userId) {
            return false;
        }

        $fields = [];
        $values = [];

        if (isset($data['name'])) {
            $fields[] = 'name = ?';
            $values[] = $data['name'];
        }
        if (isset($data['price'])) {
            $fields[] = 'price = ?';
            $values[] = $data['price'];
        }
        if (isset($data['description'])) {
            $fields[] = 'description = ?';
            $values[] = $data['description'];
        }
        if (isset($data['category'])) {
            $fields[] = 'category = ?';
            $values[] = $data['category'];
        }
        if (isset($data['is_bio'])) {
            $fields[] = 'is_bio = ?';
            $values[] = (int)$data['is_bio'];
        }
        if (isset($data['nutri_score'])) {
            $fields[] = 'nutri_score = ?';
            // Convertir la chaîne vide en NULL
            $nutriScore = empty($data['nutri_score']) ? null : $data['nutri_score'];
            $values[] = $nutriScore;
        }

        if (empty($fields)) {
            return false;
        }

        $fields[] = 'updated_at = NOW()';
        $values[] = $productId;

        $sql = 'UPDATE items SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    /**
     * Supprimer un produit
     * @param int $productId
     * @param int $userId Vérifier que l'utilisateur est propriétaire
     * @param bool $isAdmin Si true, ne pas vérifier la propriété
     * @return bool
     */
    public function delete($productId, $userId, $isAdmin = false) {
        // Vérifier que l'utilisateur est propriétaire (sauf si admin)
        $product = $this->getProductById($productId);
        if (!$product) {
            return false;
        }
        
        if (!$isAdmin && $product['user_id'] != $userId) {
            return false;
        }

        try {
            // D'abord, supprimer tous les liens dans list_item
            $stmt = $this->pdo->prepare('DELETE FROM list_item WHERE item_id = ?');
            $stmt->execute([$productId]);

            // Ensuite, supprimer le produit
            $stmt = $this->pdo->prepare('DELETE FROM items WHERE id = ?');
            return $stmt->execute([$productId]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Récupérer tous les produits (pour affichage public)
     * @return array
     */
    public function getAllProducts() {
        $stmt = $this->pdo->query('
            SELECT i.*, u.name as creator_name
            FROM items i
            JOIN users u ON i.user_id = u.id
            ORDER BY i.created_at DESC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
