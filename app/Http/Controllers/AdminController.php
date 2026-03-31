<?php
/**
 * AdminController
 * Gestion du tableau de bord administrateur
 */

class AdminController {
    private $userModel;
    private $shoppingListModel;
    private $productModel;

    public function __construct($userModel, $shoppingListModel, $productModel) {
        $this->userModel = $userModel;
        $this->shoppingListModel = $shoppingListModel;
        $this->productModel = $productModel;
    }

    private function requireAdmin() {
        if (!isAdmin()) {
            $_SESSION['error'] = 'Accès refusé';
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    /**
     * Afficher le tableau de bord admin
     */
    public function dashboard() {
        $this->requireAdmin();

        $users = $this->userModel->getAllUsers();
        $lists = $this->getAllListsWithStats();
        $products = $this->productModel->getAllProducts();

        $GLOBALS['users'] = $users;
        $GLOBALS['lists'] = $lists;
        $GLOBALS['products'] = $products;

        return;
    }

    /**
     * Récupérer toutes les listes avec stats
     */
    private function getAllListsWithStats() {
        $lists = $this->shoppingListModel->getAllLists();
        
        foreach ($lists as &$list) {
            $items = $this->shoppingListModel->getListItems($list['id']);
            $list['item_count'] = count($items);
        }
        
        return $lists;
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser() {
        $this->requireAdmin();

        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            header('Location: index.php?page=admin');
            exit;
        }

        $userId = $_GET['id'];
        
        // Ne pas pouvoir se supprimer soi-même
        if ($userId == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas vous supprimer vous-même';
            header('Location: index.php?page=admin');
            exit;
        }

        try {
            $this->userModel->delete($userId);
            $_SESSION['success'] = 'Utilisateur supprimé';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la suppression: ' . $e->getMessage();
        }

        header('Location: index.php?page=admin');
        exit;
    }

    /**
     * Supprimer une liste
     */
    public function deleteList() {
        $this->requireAdmin();

        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'Liste non trouvée';
            header('Location: index.php?page=admin');
            exit;
        }

        try {
            $this->shoppingListModel->delete($_GET['id'], null, true);
            $_SESSION['success'] = 'Liste supprimée';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la suppression: ' . $e->getMessage();
        }

        header('Location: index.php?page=admin');
        exit;
    }

    /**
     * Supprimer un produit
     */
    public function deleteProduct() {
        $this->requireAdmin();

        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'Produit non trouvé';
            header('Location: index.php?page=admin');
            exit;
        }

        try {
            $this->productModel->delete($_GET['id'], null, true);
            $_SESSION['success'] = 'Produit supprimé';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la suppression: ' . $e->getMessage();
        }

        header('Location: index.php?page=admin');
        exit;
    }
}
