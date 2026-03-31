<?php
/**
 * PANIER - Gestionnaire de Listes de Courses
 * Fichier principal - Router
 */

// Démarrer la session
session_start();

// Charger les fichiers de configuration
require_once 'config/database.php';
require_once 'config/helpers.php';

// Charger les modèles
require_once 'app/Models/User.php';
require_once 'app/Models/Product.php';
require_once 'app/Models/ShoppingList.php';

// Charger les contrôleurs
require_once 'app/Http/Controllers/AuthController.php';
require_once 'app/Http/Controllers/ProductController.php';
require_once 'app/Http/Controllers/ShoppingListController.php';

// Initialiser les modèles
$userModel = new User($pdo);
$productModel = new Product($pdo);
$shoppingListModel = new ShoppingList($pdo);

// Initialiser les contrôleurs
$authController = new AuthController($userModel);
$productController = new ProductController($productModel);
$shoppingListController = new ShoppingListController($shoppingListModel, $productModel, $userModel);

// Récupérer la page demandée
$page = $_GET['page'] ?? 'home';

// Router
try {
    switch ($page) {
        // ===== AUTH =====
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->login();
            } else {
                $authController->showLogin();
            }
            break;

        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->register();
            } else {
                $authController->showRegister();
            }
            break;

        case 'logout':
            $authController->logout();
            break;

        // ===== DASHBOARD =====
        case 'dashboard':
            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?page=login');
                exit;
            }
            include 'views/dashboard.php';
            break;

        // ===== SHOPPING LISTS =====
        case 'public-lists':
            $shoppingListController->publicLists();
            break;

        case 'public-list':
            $shoppingListController->publicDetail();
            break;

        case 'my-lists':
            $shoppingListController->myLists();
            break;

        case 'list':
            $shoppingListController->detail();
            break;

        case 'create-list':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $shoppingListController->create();
            } else {
                $shoppingListController->create();
            }
            break;

        case 'edit-list':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $shoppingListController->edit();
            } else {
                $shoppingListController->edit();
            }
            break;

        case 'add-product':
            $shoppingListController->addProduct();
            break;

        case 'remove-product':
            $shoppingListController->removeProduct();
            break;

        case 'toggle-product':
            $shoppingListController->toggleProduct();
            break;

        case 'delete-list':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $shoppingListController->delete();
            } else {
                $shoppingListController->delete();
            }
            break;

        // ===== PRODUCTS =====
        case 'my-products':
            $productController->myProducts();
            break;

        case 'product':
            $productController->detail();
            break;

        case 'create-product':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productController->create();
            } else {
                $productController->create();
            }
            break;

        case 'edit-product':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productController->edit();
            } else {
                $productController->edit();
            }
            break;

        case 'delete-product':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productController->delete();
            } else {
                $productController->delete();
            }
            break;

        // ===== HOME =====
        case 'home':
        default:
            include 'views/home.php';
            break;
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . htmlspecialchars($e->getMessage());
}
?>
