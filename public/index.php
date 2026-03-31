<?php
/**
 * PANIER - Point d'entrée principal (Router)
 * Tous les requêtes passent par ici
 */

// Démarrer la session
session_start();

// Définir les constantes
define('BASE_URL', '');
define('PROJECT_ROOT', __DIR__ . '/../');

// Charger les fichiers de configuration
require_once PROJECT_ROOT . 'config/database.php';
require_once PROJECT_ROOT . 'config/helpers.php';

// Charger les modèles
require_once PROJECT_ROOT . 'app/Models/User.php';
require_once PROJECT_ROOT . 'app/Models/Product.php';
require_once PROJECT_ROOT . 'app/Models/ShoppingList.php';

// Charger les contrôleurs
require_once PROJECT_ROOT . 'app/Http/Controllers/AuthController.php';
require_once PROJECT_ROOT . 'app/Http/Controllers/ProductController.php';
require_once PROJECT_ROOT . 'app/Http/Controllers/ShoppingListController.php';
require_once PROJECT_ROOT . 'app/Http/Controllers/AdminController.php';

try {
    // Instancier les modèles
    $user = new User($pdo);
    $product = new Product($pdo);
    $shoppingList = new ShoppingList($pdo);

    // Instancier les contrôleurs
    $authController = new AuthController($user);
    $productController = new ProductController($product, $user);
    $shoppingListController = new ShoppingListController($shoppingList, $product, $user);
    $adminController = new AdminController($user, $shoppingList, $product);

    // Récupérer la page demandée
    $page = $_GET['page'] ?? 'home';
    $view = null;

    // Router - Dispatcher les requêtes
    switch ($page) {
        // --- PAGES PUBLIQUES ---
        case 'home':
            $view = PROJECT_ROOT . 'views/home.php';
            break;

        case 'public-lists':
            $view = PROJECT_ROOT . 'views/shopping_lists/public_list.php';
            break;

        case 'public-detail':
            $view = PROJECT_ROOT . 'views/shopping_lists/detail.php';
            break;

        // --- AUTHENTIFICATION ---
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->login();
            }
            $view = PROJECT_ROOT . 'views/auth/login.php';
            break;

        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $authController->register();
            }
            $view = PROJECT_ROOT . 'views/auth/register.php';
            break;

        case 'logout':
            $authController->logout();
            break;

        // --- USER PAGES (Nécessite auth) ---
        case 'dashboard':
            if (!isLoggedIn()) {
                $_SESSION['error'] = 'Vous devez être connecté.';
                header('Location: index.php?page=login');
                exit;
            }
            $view = PROJECT_ROOT . 'views/dashboard.php';
            break;

        case 'profile':
            if (!isLoggedIn()) {
                $_SESSION['error'] = 'Vous devez être connecté.';
                header('Location: index.php?page=login');
                exit;
            }
            $view = PROJECT_ROOT . 'views/profile.php';
            break;

        // --- SHOPPING LISTS ---
        case 'my-lists':
            if (!isLoggedIn()) {
                $_SESSION['error'] = 'Vous devez être connecté.';
                header('Location: index.php?page=login');
                exit;
            }
            $view = PROJECT_ROOT . 'views/shopping_lists/my_lists.php';
            break;

        case 'list-create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $shoppingListController->create();
                // Après traitement POST, charger la vue du formulaire pour afficher les erreurs
                $view = PROJECT_ROOT . 'views/shopping_lists/form.php';
            } else {
                if (!isLoggedIn()) {
                    header('Location: index.php?page=login');
                    exit;
                }
                $view = PROJECT_ROOT . 'views/shopping_lists/form.php';
            }
            break;

        case 'list-edit':
            if (!isLoggedIn()) {
                header('Location: index.php?page=login');
                exit;
            }
            $shoppingListController->edit();
            $view = PROJECT_ROOT . 'views/shopping_lists/form_edit.php';
            break;

        case 'list-delete':
            if (!isLoggedIn()) {
                header('Location: index.php?page=login');
                exit;
            }
            $view = PROJECT_ROOT . 'views/shopping_lists/delete_confirm.php';
            $shoppingListController->delete();
            break;

        case 'list-add-item':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $shoppingListController->addProduct();
            } else {
                if (!isLoggedIn()) {
                    header('Location: index.php?page=login');
                    exit;
                }
                $view = PROJECT_ROOT . 'views/shopping_lists/form_add_item.php';
            }
            break;

        case 'list-remove-item':
            $shoppingListController->removeProduct();
            break;

        case 'list-toggle-item':
            $shoppingListController->toggleProduct();
            break;

        // --- PRODUCTS ---
        case 'my-products':
            if (!isLoggedIn()) {
                $_SESSION['error'] = 'Vous devez être connecté.';
                header('Location: index.php?page=login');
                exit;
            }
            $view = PROJECT_ROOT . 'views/products/my_products.php';
            break;

        case 'product-create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productController->create();
                // Après traitement POST, charger la vue du formulaire pour afficher les erreurs
                $view = PROJECT_ROOT . 'views/products/form.php';
            } else {
                if (!isLoggedIn()) {
                    header('Location: index.php?page=login');
                    exit;
                }
                $view = PROJECT_ROOT . 'views/products/form.php';
            }
            break;

        case 'product-edit':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productController->edit();
                // Après traitement POST, charger la vue du formulaire pour afficher les erreurs
                $view = PROJECT_ROOT . 'views/products/form_edit.php';
            } else {
                if (!isLoggedIn()) {
                    header('Location: index.php?page=login');
                    exit;
                }
                $view = PROJECT_ROOT . 'views/products/form_edit.php';
            }
            break;

        case 'product-delete':
            if (!isLoggedIn()) {
                header('Location: index.php?page=login');
                exit;
            }
            $view = PROJECT_ROOT . 'views/products/delete_confirm.php';
            $productController->delete();
            break;

        case 'product-detail':
            $view = PROJECT_ROOT . 'views/products/detail.php';
            break;

        // --- ADMIN ---
        case 'admin':
            $adminController->dashboard();
            $view = PROJECT_ROOT . 'views/admin/dashboard.php';
            break;

        case 'admin-delete-user':
            $adminController->deleteUser();
            break;

        case 'admin-delete-list':
            $adminController->deleteList();
            break;

        case 'admin-delete-product':
            $adminController->deleteProduct();
            break;

        // --- PAGE PAR DÉFAUT ---
        default:
            if (!isLoggedIn()) {
                $page = 'home';
                $view = PROJECT_ROOT . 'views/home.php';
            } else {
                $page = 'dashboard';
                $view = PROJECT_ROOT . 'views/dashboard.php';
            }
            break;
    }

    // Rendre la vue avec le layout
    if ($view && file_exists($view)) {
        include PROJECT_ROOT . 'views/layout.php';
    } else {
        http_response_code(404);
        echo '404 - Page non trouvée';
    }

} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo '<div style="margin: 2rem; background: #fee2e2; padding: 1rem; border-radius: 0.5rem;">';
    echo '<h1>❌ Erreur</h1>';
    echo '<p>' . escape($e->getMessage()) . '</p>';
    echo '</div>';
}
