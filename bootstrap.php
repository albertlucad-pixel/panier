<?php
/**
 * Bootstrap - Initialisation globale de l'application
 */

// Définir la racine du projet si pas déjà défini
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', __DIR__ . '/');
}

// Définir la base URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '');
}

// Inclure les fichiers de configuration
require_once PROJECT_ROOT . 'config/database.php';
require_once PROJECT_ROOT . 'config/helpers.php';

// Créer les instances globales des modèles
$user = new User($pdo);
$product = new Product($pdo);
$shoppingList = new ShoppingList($pdo);
