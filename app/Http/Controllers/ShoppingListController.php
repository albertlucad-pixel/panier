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
        include PROJECT_ROOT . 'views/shopping_lists/public_list.php';
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

        include PROJECT_ROOT . 'views/shopping_lists/public_detail.php';
    }

    /**
     * Lister les paniers de l'utilisateur connecté
     */
    public function myLists() {
        $this->requireAuth();
        $lists = $this->shoppingListModel->getListsByUser($_SESSION['user_id']);
        include PROJECT_ROOT . 'views/shopping_lists/my_lists.php';
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

        include PROJECT_ROOT . 'views/shopping_lists/detail.php';
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

            // Vérifier qu'il y a au moins un produit sélectionné
            $hasProducts = false;
            if (isset($_POST['products']) && is_array($_POST['products'])) {
                foreach ($_POST['products'] as $productId => $quantity) {
                    // Les inputs disabled ne sont pas envoyés, donc si on les reçoit c'est qu'ils sont actifs
                    $quantity = (int)$quantity;
                    if ($quantity > 0) {
                        $hasProducts = true;
                        break;
                    }
                }
            }
            if (!$hasProducts) {
                $errors[] = 'Vous devez sélectionner au moins un produit';
            }

            if (empty($errors)) {
                $listId = $this->shoppingListModel->create(
                    $_SESSION['user_id'],
                    $name,
                    $description
                );

                if ($listId) {
                    // Ajouter les produits sélectionnés (seulement ceux non-disabled)
                    if (isset($_POST['products']) && is_array($_POST['products'])) {
                        foreach ($_POST['products'] as $productId => $quantity) {
                            // Les inputs disabled ne sont pas envoyés
                            $quantity = (int)$quantity;
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

                    $_SESSION['success'] = 'Liste créée avec succès!';
                    header('Location: index.php?page=my-lists');
                    exit;
                } else {
                    $errors[] = 'Erreur lors de la création';
                }
            }
            // Si erreurs, les variables $errors, $myProducts sont disponibles pour la vue
        }

        // Assigner les variables au scope global pour qu'elles soient accessibles à la vue
        $GLOBALS['errors'] = $errors;
        $GLOBALS['myProducts'] = $myProducts;
        
        // Retourner pour que le router charge la vue avec le layout
        return;
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
                        'is_public' => $_POST['is_public'] ?? 0,
                        'is_completed' => $_POST['is_completed'] ?? 0
                    ]
                );

                // Obtenir les produits actuellement dans la liste
                $currentItems = $this->shoppingListModel->getListItems($_GET['id']);
                $currentProductIds = array_map(function($item) { return $item['product_id']; }, $currentItems);
                
                // Obtenir les produits envoyés (cochés)
                $sentProductIds = isset($_POST['products']) ? array_keys($_POST['products']) : [];
                
                // Supprimer les produits qui ont été décochés
                $productsToRemove = array_diff($currentProductIds, $sentProductIds);
                foreach ($productsToRemove as $productId) {
                    // Trouver le lien dans la liste
                    foreach ($currentItems as $item) {
                        if ($item['product_id'] == $productId) {
                            $this->shoppingListModel->removeProductFromList($_GET['id'], $item['item_link_id']);
                            break;
                        }
                    }
                }

                // Mettre à jour les produits cochés
                if (isset($_POST['products']) && is_array($_POST['products'])) {
                    foreach ($_POST['products'] as $productId => $quantity) {
                        $quantity = (int)$quantity;
                        if ($quantity > 0) {
                            // Vérifier si le produit existe déjà dans la liste
                            $existingItem = null;
                            foreach ($currentItems as $item) {
                                if ($item['product_id'] == $productId) {
                                    $existingItem = $item;
                                    break;
                                }
                            }
                            
                            if ($existingItem) {
                                // Produit existe: mettre à jour
                                $this->shoppingListModel->updateProductInList(
                                    $_GET['id'],
                                    $productId,
                                    $quantity,
                                    $_POST['units'][$productId] ?? 'pcs'
                                );
                            } else {
                                // Produit n'existe pas: ajouter
                                $this->shoppingListModel->addProductToList(
                                    $_GET['id'],
                                    $productId,
                                    $quantity,
                                    $_POST['units'][$productId] ?? 'pcs'
                                );
                            }
                        }
                    }
                }

                $_SESSION['success'] = 'Liste mise à jour avec succès!';
                header('Location: index.php?page=my-lists');
                exit;
            }
        }

        // Assigner les variables au scope global pour qu'elles soient accessibles à la vue
        $GLOBALS['errors'] = $errors;
        $GLOBALS['listData'] = $list;
        $GLOBALS['myProducts'] = $myProducts;
        $GLOBALS['listItems'] = $items;  // Ajouter les items actuels de la liste
        
        // Retourner pour que le router charge la vue avec le layout
        return;
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

        // Rendre $shoppingList disponible à la vue
        $GLOBALS['shoppingList'] = $list;
        
        // Retourner pour laisser le router inclure avec le layout
        return;
    }
}
?>
