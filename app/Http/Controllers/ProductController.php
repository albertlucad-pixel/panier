<?php
/**
 * ProductController
 * Gestion des produits (CRUD)
 */

class ProductController {
    private $productModel;

    public function __construct($productModel) {
        $this->productModel = $productModel;
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
     * Lister les produits de l'utilisateur connecté
     */
    public function myProducts() {
        $this->requireAuth();
        $products = $this->productModel->getProductsByUser($_SESSION['user_id']);
        include 'views/products/my_products.php';
    }

    /**
     * Voir les détails d'un produit
     */
    public function detail() {
        $this->requireAuth();

        if (!isset($_GET['id'])) {
            header('Location: index.php?page=my-products');
            exit;
        }

        $product = $this->productModel->getProductById($_GET['id']);
        
        if (!$product) {
            header('Location: index.php?page=my-products');
            exit;
        }

        // L'utilisateur ne peut voir que ses propres produits en détail
        if ($product['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?page=my-products');
            exit;
        }

        include 'views/products/detail.php';
    }

    /**
     * Créer un nouveau produit
     */
    public function create() {
        $this->requireAuth();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateProductForm();

            if (empty($errors)) {
                $productId = $this->productModel->create(
                    $_SESSION['user_id'],
                    $_POST['name'],
                    $_POST['price'],
                    [
                        'description' => $_POST['description'] ?? null,
                        'category' => $_POST['category'] ?? null,
                        'is_bio' => $_POST['is_bio'] ?? 0,
                        'nutri_score' => $_POST['nutri_score'] ?? null
                    ]
                );

                if ($productId) {
                    header('Location: index.php?page=my-products');
                    exit;
                } else {
                    $errors[] = 'Erreur lors de la création du produit';
                }
            }
        }

        include 'views/products/form.php';
    }

    /**
     * Modifier un produit
     */
    public function edit() {
        $this->requireAuth();

        if (!isset($_GET['id'])) {
            header('Location: index.php?page=my-products');
            exit;
        }

        $product = $this->productModel->getProductById($_GET['id']);
        
        if (!$product || $product['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?page=my-products');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateProductForm();

            if (empty($errors)) {
                $success = $this->productModel->update(
                    $_GET['id'],
                    $_SESSION['user_id'],
                    [
                        'name' => $_POST['name'],
                        'price' => $_POST['price'],
                        'description' => $_POST['description'] ?? null,
                        'category' => $_POST['category'] ?? null,
                        'is_bio' => $_POST['is_bio'] ?? 0,
                        'nutri_score' => $_POST['nutri_score'] ?? null
                    ]
                );

                if ($success) {
                    header('Location: index.php?page=product&id=' . $_GET['id']);
                    exit;
                } else {
                    $errors[] = 'Erreur lors de la mise à jour';
                }
            }
        }

        include 'views/products/form.php';
    }

    /**
     * Supprimer un produit
     */
    public function delete() {
        $this->requireAuth();

        if (!isset($_GET['id'])) {
            header('Location: index.php?page=my-products');
            exit;
        }

        $product = $this->productModel->getProductById($_GET['id']);
        
        if (!$product || $product['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?page=my-products');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->productModel->delete($_GET['id'], $_SESSION['user_id'])) {
                header('Location: index.php?page=my-products');
                exit;
            }
        }

        include 'views/products/delete_confirm.php';
    }

    /**
     * Valider les données du formulaire produit
     */
    private function validateProductForm() {
        $errors = [];

        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';

        if (empty($name)) {
            $errors[] = 'Le nom du produit est obligatoire';
        }
        if (empty($price)) {
            $errors[] = 'Le prix est obligatoire';
        }
        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'Le prix doit être un nombre positif';
        }
        if (isset($_POST['nutri_score']) && $_POST['nutri_score'] && !in_array($_POST['nutri_score'], ['A', 'B', 'C', 'D', 'E'])) {
            $errors[] = 'Nutri-score invalide';
        }

        return $errors;
    }
}
?>
