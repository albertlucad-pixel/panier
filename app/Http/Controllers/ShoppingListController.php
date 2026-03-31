<?php
/**
 * ShoppingListController
 * Gestion des listes de courses (CRUD + calcul coût)
 */

class ShoppingListController {
    private $shoppingListModel;
    private $productModel;
    private $userModel;

    public function __construct($shoppingListModel, $productModel, $userModel) {
        $this->shoppingListModel = $shoppingListModel;
        $this->productModel = $productModel;
        $this->userModel = $userModel;
    }

    /**
     * Vérifier que l'utilisateur est connecté
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Afficher les paniers publics (non connectés ET connectés)
     */
    public function publicLists() {
        $lists = $this->shoppingListModel->getPublicLists();
        include 'views/shopping_lists/public_list.php';
    }

    /**
     * Voir les détails d'un panier public
     */
    public function publicDetail() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?page=public-lists');
            exit;
        }

        $list = $this->shoppingListModel->getListById($_GET['id']);
        
        if (!$list || $list['is_completed']) {
            header('Location: index.php?page=public-lists');
            exit;
        }

        $items = $this->shoppingListModel->getListItems($_GET['id']);
        $totalCost = $this->shoppingListModel->calculateTotalCost($_GET['id']);
        $creator = $this->userModel->getUserById($list['user_id']);
        $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $list['user_id'];

        include 'views/shopping_lists/public_detail.php';
    }

    /**
     * Lister les paniers de l'utilisateur connecté
     */
    public function myLists() {
        $this->requireAuth();
        $lists = $this->shoppingListModel->getListsByUser($_SESSION['user_id']);
        include 'views/shopping_lists/my_lists.php';
    }

    /**
     * Voir les détails d'un panier personnel
     */
    public function detail() {
        $this->requireAuth();

        if (!isset($_GET['id'])) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $list = $this->shoppingListModel->getListById($_GET['id']);
        
        if (!$list || $list['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $items = $this->shoppingListModel->getListItems($_GET['id']);
        $totalCost = $this->shoppingListModel->calculateTotalCost($_GET['id']);
        $myProducts = $this->productModel->getProductsByUser($_SESSION['user_id']);

        include 'views/shopping_lists/detail.php';
    }

    /**
     * Créer une nouvelle liste
     */
    public function create() {
        $this->requireAuth();
        $errors = [];
        $myProducts = $this->productModel->getProductsByUser($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                $errors[] = 'Le nom de la liste est obligatoire';
            }

            if (empty($errors)) {
                $listId = $this->shoppingListModel->create(
                    $_SESSION['user_id'],
                    $name,
                    $description
                );

                if ($listId) {
                    // Ajouter les produits sélectionnés
                    if (isset($_POST['products']) && is_array($_POST['products'])) {
                        foreach ($_POST['products'] as $productId => $quantity) {
                            if ($quantity > 0) {
                                $this->shoppingListModel->addProductToList(
                                    $listId,
                                    $productId,
                                    $quantity,
                                    $_POST['units'][$productId] ?? 'pcs'
                                );
                            }
                        }
                    }

                    header('Location: index.php?page=list&id=' . $listId);
                    exit;
                } else {
                    $errors[] = 'Erreur lors de la création';
                }
            }
        }

        include 'views/shopping_lists/form.php';
    }

    /**
     * Modifier une liste
     */
    public function edit() {
        $this->requireAuth();

        if (!isset($_GET['id'])) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $list = $this->shoppingListModel->getListById($_GET['id']);
        
        if (!$list || $list['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $errors = [];
        $items = $this->shoppingListModel->getListItems($_GET['id']);
        $myProducts = $this->productModel->getProductsByUser($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';

            if (empty($name)) {
                $errors[] = 'Le nom de la liste est obligatoire';
            }

            if (empty($errors)) {
                $this->shoppingListModel->update(
                    $_GET['id'],
                    $_SESSION['user_id'],
                    [
                        'name' => $name,
                        'description' => $_POST['description'] ?? null,
                        'is_completed' => $_POST['is_completed'] ?? 0
                    ]
                );

                // Mettre à jour les produits
                if (isset($_POST['products']) && is_array($_POST['products'])) {
                    foreach ($_POST['products'] as $productId => $quantity) {
                        if ($quantity > 0) {
                            $this->shoppingListModel->updateProductInList(
                                $_GET['id'],
                                $productId,
                                $quantity,
                                $_POST['units'][$productId] ?? 'pcs'
                            );
                        }
                    }
                }

                header('Location: index.php?page=list&id=' . $_GET['id']);
                exit;
            }
        }

        include 'views/shopping_lists/form_edit.php';
    }

    /**
     * Ajouter un produit à une liste existante
     */
    public function addProduct() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['list_id']) || !isset($_POST['product_id'])) {
            http_response_code(400);
            die('Invalid request');
        }

        $listId = $_POST['list_id'];
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'] ?? 1;
        $unit = $_POST['unit'] ?? 'pcs';

        $list = $this->shoppingListModel->getListById($listId);
        
        if (!$list || $list['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            die('Forbidden');
        }

        $this->shoppingListModel->addProductToList($listId, $productId, $quantity, $unit);
        
        header('Location: index.php?page=list&id=' . $listId);
        exit;
    }

    /**
     * Supprimer un produit d'une liste
     */
    public function removeProduct() {
        $this->requireAuth();

        if (!isset($_GET['list_id']) || !isset($_GET['item_id'])) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $list = $this->shoppingListModel->getListById($_GET['list_id']);
        
        if (!$list || $list['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $this->shoppingListModel->removeProductFromList($_GET['list_id'], $_GET['item_id']);
        header('Location: index.php?page=list&id=' . $_GET['list_id']);
        exit;
    }

    /**
     * Marquer un produit comme coché dans une liste
     */
    public function toggleProduct() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            die('Invalid request');
        }

        $listItemId = $_POST['item_id'] ?? null;
        $checked = isset($_POST['checked']);

        if (!$listItemId) {
            http_response_code(400);
            die('Missing item_id');
        }

        $this->shoppingListModel->toggleProductChecked($listItemId, $checked);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    /**
     * Supprimer une liste complète
     */
    public function delete() {
        $this->requireAuth();

        if (!isset($_GET['id'])) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $list = $this->shoppingListModel->getListById($_GET['id']);
        
        if (!$list) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        
        if ($list['user_id'] != $_SESSION['user_id'] && !$isAdmin) {
            header('Location: index.php?page=my-lists');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->shoppingListModel->delete($_GET['id'], $_SESSION['user_id'], $isAdmin)) {
                header('Location: index.php?page=my-lists');
                exit;
            }
        }

        include 'views/shopping_lists/delete_confirm.php';
    }
}
?>
