# 📚 Guide Développeur - Architecture Panier

## 🏗️ Structure du Projet

```
panier/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php        # Login, Register, Logout
│   │       ├── ProductController.php     # CRUD Produits
│   │       └── ShoppingListController.php # CRUD Listes
│   └── Models/
│       ├── User.php                      # Modèle utilisateur + rôles
│       ├── Product.php                   # Modèle produits
│       └── ShoppingList.php              # Modèle listes + calculs
├── config/
│   └── database.php                      # Connexion PDO
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── products/
│   │   ├── my_products.php
│   │   ├── form.php
│   │   ├── detail.php
│   │   └── delete_confirm.php
│   ├── shopping_lists/
│   │   ├── public_list.php
│   │   ├── public_detail.php
│   │   ├── my_lists.php
│   │   ├── detail.php
│   │   ├── form.php
│   │   ├── form_edit.php
│   │   └── delete_confirm.php
│   ├── dashboard.php
│   └── home.php
├── routes/ (optionnel pour Laravel)
├── public/
│   ├── index.php                         # Routeur principal
│   ├── style.css
│   └── js/
├── storage/
│   ├── logs/
│   └── app/
├── database/
│   ├── migrations/ (optionnel)
│   └── seeders/ (optionnel)
├── script.sql                            # Script de création BD
├── .env                                  # Variables d'environnement
├── .env.example
├── .gitignore
└── README.md
```

## 🔐 Modèle de Sécurité - Moindre Privilège

### Rôles Utilisateurs

| Rôle | Login | Lire paniers | Créer produits | Créer listes | Modifier ses données | Modifi autre | Admin |
|------|:-----:|:----------:|:----------:|:----------:|:----------:|:----------:|:-----:|
| **Non connecté** | ❌ | ✅ (publics) | ❌ | ❌ | ❌ | ❌ | ❌ |
| **User** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### Vérifications dans le Code

```php
// Vérification connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Vérification propriétaire (dans modèles/contrôleurs)
if ($resource['user_id'] != $_SESSION['user_id'] && !$isAdmin) {
    return false; // ou redirect 403
}

// Vérification admin
$isAdmin = $userModel->isAdmin($_SESSION['user_id']);
```

## 📊 Modèle de Données

### Relations

```
users (1) ──────→ (N) shopping_lists
                      │
                      └──→ (N) list_item ←─ (N) items ←─ (1) users
```

### Tables

**users**
- id (PK)
- name, email, password (hashé BCRYPT)
- **role** (user | admin) ✅ Moindre privilège
- timestamps

**items** (Produits créés par utilisateurs)
- id (PK)
- **user_id** (FK) ✅ Créateur du produit
- name (obligatoire), price (obligatoire)
- description, category
- is_bio (boolean), nutri_score (A-E)
- timestamps

**shopping_lists** (Listes de courses)
- id (PK)
- user_id (FK) ✅ Propriétaire
- name (obligatoire), description (optionnel)
- is_completed (boolean)
- timestamps

**list_item** (Pivot N,N - avec données portées)
- id (PK)
- shopping_list_id (FK), item_id (FK)
- **quantity** (nombre d'articles)
- **unit** (pcs, kg, L, etc.)
- is_checked (coché ou non)
- notes (optionnel)
- timestamps

## 🎯 Flux des Données

### Créer un Panier

```
User connecté 
  → Formulaire (nom liste + sélection produits + quantités)
  → ShoppingListController::create()
    → ShoppingList::create() (créer liste)
    → ShoppingList::addProductToList() (ajouter produits)
      → Vérif: produits appartiennent à l'utilisateur ✅
    → Redirect détail liste
```

### Calculer Coût Total

```
ShoppingList::calculateTotalCost($listId)
  → SUM(items.price * list_item.quantity)
  → Retourne float (€)
```

### Affichage Détail Panier

```
Vue: views/shopping_lists/detail.php
  → $items = ShoppingList::getListItems($listId)
    → JOIN items, list_item
    → Calcul subtotal (price * quantity) par article
  → $totalCost = ShoppingList::calculateTotalCost($listId)
  → Affichage tableau avec:
    - Nom article
    - Prix unitaire
    - Quantité
    - Coût ligne (subtotal)
  → Total bas page
```

## 🔧 Contrôleurs - Responsabilités

### AuthController
- `showLogin()` - Affiche formulaire login
- `login()` - Vérifie identifiants, crée session
- `showRegister()` - Affiche formulaire registration
- `register()` - Valide données, crée user
- `logout()` - Détruit session

### ProductController
- `myProducts()` - Liste mes produits
- `create()` - Formulaire + création produit
- `edit()` - Formulaire + modification
- `delete()` - Confirmation + suppression
- **Vérif**: User ne peut éditer/supprimer que ses produits

### ShoppingListController
- `publicLists()` - Listes publiques (pour tous)
- `publicDetail()` - Détail panier public
- `myLists()` - Mes listes (user connecté)
- `detail()` - Détail ma liste
- `create()` - Créer liste + ajouter produits
- `edit()` - Modifier liste
- `addProduct()` - Ajouter produit à liste
- `removeProduct()` - Supprimer produit de liste
- `toggleProduct()` - Marquer comme coché
- `delete()` - Supprimer liste entière
  - **Admin peut supprimer listes d'autres** ✅

## 📝 Modèles - Logique Métier

### User
- `authenticate($email, $password)` - Vérifie login
- `create($name, $email, $password)` - Crée user
- `isAdmin($userId)` - Vérifie rôle
- `setRole($userId, $role)` - Change rôle (admin)

### Product
- `create($userId, $name, $price, $optionalData)`
- `getProductsByUser($userId)` - Mes produits
- `update($productId, $userId, $data)` - Vérifie propriétaire ✅
- `delete($productId, $userId)` - Vérifie propriétaire ✅

### ShoppingList
- `create($userId, $name, $description)`
- `getListsByUser($userId)` - Mes listes
- `getPublicLists()` - Listes publiques
- `addProductToList($listId, $itemId, $quantity, $unit, $notes)`
- `calculateTotalCost($listId)` - **Calcul automatique**
- `getListItems($listId)` - Articles avec subtotals
- `delete($listId, $userId, $isAdmin)` - Vérifie permissions ✅

## ✅ Conformité Sécurité

### Protection OWASP
- ✅ **SQL Injection**: Requêtes préparées (PDO)
- ✅ **Authentification**: Passwords hashés BCRYPT
- ✅ **Autorisation**: Moindre privilège + vérification session
- ✅ **Session**: `session_start()` + vérif `$_SESSION['user_id']`
- ✅ **XSS**: `htmlspecialchars()` en vues (à implémenter)

### Points à Compléter en Vues

```php
<!-- AVANT (XSS) -->
<h1><?= $user['name'] ?></h1>

<!-- APRÈS (Sécurisé) -->
<h1><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></h1>
```

## 🚀 Utilisation

### Démarrer
```bash
php artisan serve
# OU
php -S localhost:8000 -t public/
```

### Routes Principales
- `?page=home` - Accueil
- `?page=public-lists` - Listes publiques (non-auth)
- `?page=login` - Connexion
- `?page=register` - Inscription
- `?page=dashboard` - Tableau de bord (auth)
- `?page=my-lists` - Mes listes (auth)
- `?page=my-products` - Mes produits (auth)
- `?page=create-list` - Créer liste (auth)
- `?page=create-product` - Créer produit (auth)

## 🧪 Tests à Implémenter

```php
// tests/Unit/ShoppingListTest.php
public function testCalculateTotalCost() {
    $list = $shoppingList->create(1, 'Test', 'Description');
    $shoppingList->addProductToList($list, 1, 2); // 2x€5
    $total = $shoppingList->calculateTotalCost($list);
    $this->assertEquals(10.00, $total);
}

// tests/Unit/UserTest.php
public function testUserCannotAccessOthersList() {
    $result = $shoppingList->update(1, 2, []); // User 2 try to edit List of User 1
    $this->assertFalse($result);
}
```

---

**Dernière mise à jour**: 31/03/2026
