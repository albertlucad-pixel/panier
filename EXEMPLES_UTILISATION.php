<?php
/**
 * EXEMPLES D'UTILISATION - Panier
 * Ce fichier montre comment utiliser les modèles et contrôleurs
 * (Ne pas exécuter directement, juste pour référence)
 */

// ============================================
// 1. EXEMPLE: CRÉER UN UTILISATEUR
// ============================================

$userModel = new User($pdo);

// Enregistrement
$userId = $userModel->create(
    'Jean Dupont',           // name
    'jean@example.com',      // email
    'password123'            // password (sera hashé automatiquement)
);
// Retourne: 4 (ID du nouvel utilisateur)

// Vérifier login
$user = $userModel->authenticate('jean@example.com', 'password123');
// Retourne: ['id' => 4, 'name' => 'Jean Dupont', 'email' => '...', 'role' => 'user', ...]

// Vérifier si admin
$isAdmin = $userModel->isAdmin(4);
// Retourne: false


// ============================================
// 2. EXEMPLE: CRÉER UN PRODUIT
// ============================================

$productModel = new Product($pdo);

// Créer un produit
$productId = $productModel->create(
    1,                                        // user_id (utilisateur créateur)
    'Pommes Gala',                           // name (obligatoire)
    2.50,                                    // price (obligatoire)
    [                                        // données optionnelles
        'description' => 'Pommes rouges 1kg',
        'category' => 'Fruits',
        'is_bio' => 1,
        'nutri_score' => 'A'
    ]
);
// Retourne: 16 (ID du produit)

// Récupérer mes produits
$myProducts = $productModel->getProductsByUser(1);
// Retourne:
// [
//     ['id' => 15, 'name' => 'Lait', 'price' => 1.50, ...],
//     ['id' => 16, 'name' => 'Pommes Gala', 'price' => 2.50, ...]
// ]

// Modifier un produit
$success = $productModel->update(
    16,                      // product_id
    1,                       // user_id (vérification propriétaire)
    [
        'price' => 2.80,     // Peut modifier certains champs
        'description' => 'Pommes Gala Bio 1kg'
    ]
);
// Retourne: true ou false


// ============================================
// 3. EXEMPLE: CRÉER UNE LISTE DE COURSES
// ============================================

$shoppingListModel = new ShoppingList($pdo);

// Créer une liste
$listId = $shoppingListModel->create(
    1,                          // user_id (propriétaire)
    'Marché du dimanche',      // name (obligatoire)
    'Courses pour la semaine'  // description (optionnel)
);
// Retourne: 5 (ID de la nouvelle liste)


// ============================================
// 4. EXEMPLE: AJOUTER DES PRODUITS À UNE LISTE
// ============================================

// Ajouter Pommes (ID=16) x 3 à la liste
$result = $shoppingListModel->addProductToList(
    5,          // shopping_list_id
    16,         // item_id (produit)
    3,          // quantity (3 pommes)
    'kg',       // unit (kilogrammes)
    'Bio si possible'  // notes (optionnel)
);
// Retourne: ID de l'association (ou false si erreur)

// Ajouter du Lait (ID=15) x 2
$shoppingListModel->addProductToList(
    5,
    15,
    2,
    'L'  // Litres
);

// Ajouter du Pain (ID=14) x 1
$shoppingListModel->addProductToList(
    5,
    14,
    1,
    'pcs'  // Pièces
);


// ============================================
// 5. EXEMPLE: CALCULER LE COÛT TOTAL
// ============================================

// Coût = (Pommes €2.50 x 3kg) + (Lait €1.50 x 2L) + (Pain €0.95 x 1pcs)
//      = €7.50 + €3.00 + €0.95
//      = €11.45

$totalCost = $shoppingListModel->calculateTotalCost(5);
echo "Coût total: €" . number_format($totalCost, 2);
// Affiche: Coût total: €11.45


// ============================================
// 6. EXEMPLE: VOIR LES ARTICLES D'UNE LISTE
// ============================================

$items = $shoppingListModel->getListItems(5);
// Retourne:
// [
//     [
//         'product_id' => 16,
//         'name' => 'Pommes Gala',
//         'price' => 2.50,
//         'quantity' => 3,
//         'unit' => 'kg',
//         'subtotal' => 7.50,  // (calculé: price × quantity)
//         'is_bio' => 1,
//         'nutri_score' => 'A',
//         'notes' => 'Bio si possible'
//     ],
//     [
//         'product_id' => 15,
//         'name' => 'Lait',
//         'price' => 1.50,
//         'quantity' => 2,
//         'unit' => 'L',
//         'subtotal' => 3.00,
//         'notes' => null
//     ],
//     [
//         'product_id' => 14,
//         'name' => 'Pain',
//         'price' => 0.95,
//         'quantity' => 1,
//         'unit' => 'pcs',
//         'subtotal' => 0.95
//     ]
// ]


// ============================================
// 7. EXEMPLE: COCHER UN PRODUIT COMME ACHETÉ
// ============================================

// Marquer le Lait comme coché
$shoppingListModel->toggleProductChecked(
    2,      // list_item_id (ID de l'association)
    true    // checked (true = coché)
);
// Retourne: true


// ============================================
// 8. EXEMPLE: MODIFIER LA QUANTITÉ D'UN PRODUIT
// ============================================

$shoppingListModel->updateProductInList(
    5,      // shopping_list_id
    16,     // item_id
    5,      // nouvelle quantité (au lieu de 3)
    'kg',   // unit
    'Bio impérativement'  // notes
);
// Retourne: true


// ============================================
// 9. EXEMPLE: SUPPRIMER UN PRODUIT D'UNE LISTE
// ============================================

$shoppingListModel->removeProductFromList(
    5,      // shopping_list_id
    14      // item_id (le pain)
);
// Retourne: true
// Résultat: Pain retiré de la liste


// ============================================
// 10. EXEMPLE: DANS UNE VUE (afficher liste)
// ============================================

/*
<h1>Ma liste: Marché du dimanche</h1>

<table border="1">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Prix unitaire</th>
            <th>Quantité</th>
            <th>Coût</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <input type="checkbox" <?= $item['is_checked'] ? 'checked' : '' ?>>
                <?= htmlspecialchars($item['name']) ?>
            </td>
            <td>€<?= number_format($item['price'], 2) ?></td>
            <td><?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?></td>
            <td>€<?= number_format($item['subtotal'], 2) ?></td>
            <td>
                <a href="?page=remove-product&list_id=5&item_id=<?= $item['item_link_id'] ?>">
                    Supprimer
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Coût total: €<?= number_format($totalCost, 2) ?></h2>
*/


// ============================================
// 11. EXEMPLE: MOINDRE PRIVILÈGE EN ACTION
// ============================================

// Scénario: User 2 essaie de supprimer produit de User 1
$product = $productModel->getProductById(10);
// $product = ['id' => 10, 'user_id' => 1, 'name' => 'Lait', ...]

// Tenter de supprimer le produit de User 1 avec User 2
$success = $productModel->delete(10, 2);  // 2 ≠ 1
// Retourne: false ✅ SÉCURITÉ APPLIQUÉE
// Le produit n'est PAS supprimé car User 2 n'en est pas propriétaire


// ============================================
// 12. EXEMPLE: ADMIN PEUT SUPPRIMER LISTE D'AUTRE
// ============================================

// Admin (ID=3) essaie de supprimer liste de User 1
$userModel->isAdmin(3);  // true

$success = $shoppingListModel->delete(
    5,           // list_id
    3,           // user_id (admin)
    true         // isAdmin = true
);
// Retourne: true ✅ Admin peut supprimer liste d'autres


// ============================================
// 13. EXEMPLE: AUTHENTIFICATION EN SESSION
// ============================================

// Dans AuthController::login()
$user = $userModel->authenticate('alice@example.com', 'password123');

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    
    header('Location: index.php?page=dashboard');
} else {
    echo "Email ou mot de passe incorrect";
}


// ============================================
// 14. EXEMPLE: PROTECTION D'UNE ACTION
// ============================================

// Au début d'une action qui nécessite connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Au début d'une action qui nécessite admin
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    header('Location: index.php?page=dashboard');
    exit;
}


// ============================================
// 15. EXEMPLE: FLOW COMPLET UTILISATEUR
// ============================================

/*
1. Alice s'inscrit
   → AuthController::register()
   → User::create('Alice', 'alice@ex.com', 'pass')

2. Alice se connecte
   → AuthController::login()
   → User::authenticate('alice@ex.com', 'pass')
   → $_SESSION['user_id'] = 1

3. Alice crée produit "Pommes"
   → ProductController::create()
   → Product::create(1, 'Pommes', 2.50, {...})
   → ID = 16

4. Alice crée liste "Marché"
   → ShoppingListController::create()
   → ShoppingList::create(1, 'Marché', '...')
   → ID = 5

5. Alice ajoute Pommes à liste
   → ShoppingListController::addProduct()
   → ShoppingList::addProductToList(5, 16, 3, 'kg')

6. Alice voir coût total
   → Vue appelle ShoppingList::calculateTotalCost(5)
   → Affiche €7.50

7. Alice coche Pommes comme achetées
   → ShoppingListController::toggleProduct()
   → ShoppingList::toggleProductChecked(item_id, true)

8. Marché complet!
*/

?>

/**
 * ============================================
 * POINTS CLÉ À RETENIR
 * ============================================
 * 
 * 1. Les modèles font la logique métier
 *    - calculateTotalCost() = logique de calcul
 *    - addProductToList() = validation + insertion
 * 
 * 2. Les contrôleurs orchestrent
 *    - Appel modèles
 *    - Gestion session
 *    - Redirection vues
 * 
 * 3. Sécurité par défaut
 *    - Requêtes préparées (pas SQL injection)
 *    - Vérification session
 *    - Vérification ownership
 *    - Vérification role (admin)
 * 
 * 4. Coût total = SUM(prix × quantité)
 *    - Calculé par BD (performant)
 *    - Toujours à jour
 *    - Affiché en vue
 * 
 * 5. Moindre Privilège
 *    - User normal: modifier SES données
 *    - Admin: modifier TOUTES données
 */
