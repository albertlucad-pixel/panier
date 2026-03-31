# 📋 TODO List - Panier

## ✅ Completed

- [x] Structure de dossiers
- [x] Configuration base de données (PDO)
- [x] Modèles (User, Product, ShoppingList)
- [x] Contrôleurs (Auth, Product, ShoppingList)
- [x] Router principal (index.php)
- [x] Script SQL (création BD + données test)
- [x] Documentation (README, README_DEVELOPPEUR)

---

## ⏳ À FAIRE

### 1️⃣ VUES HTML/PHP (PRIORITÉ HAUTE)

#### Auth
- [ ] `views/auth/login.php` - Formulaire login
- [ ] `views/auth/register.php` - Formulaire registration
- [ ] Validation côté client (HTML5)

#### Produits
- [ ] `views/products/my_products.php` - Liste mes produits
- [ ] `views/products/form.php` - Formulaire création/édition
- [ ] `views/products/detail.php` - Détail produit
- [ ] `views/products/delete_confirm.php` - Confirmation suppression

#### Listes de Courses (CORE)
- [ ] `views/shopping_lists/public_list.php` - Listes publiques (pour tous)
- [ ] `views/shopping_lists/public_detail.php` - Détail panier public (lecture seule)
- [ ] `views/shopping_lists/my_lists.php` - Mes listes
- [ ] `views/shopping_lists/detail.php` - Détail liste + produits + calcul coût
  - [ ] Tableau avec colonnes: Produit | Prix unitaire | Quantité | Coût ligne
  - [ ] Afficher **total coût** en bas
  - [ ] Bouton "Ajouter produit" (modal/formulaire)
  - [ ] Bouton "Supprimer produit" par ligne
  - [ ] Checkbox pour cocher produits
- [ ] `views/shopping_lists/form.php` - Créer liste + sélectionner produits
- [ ] `views/shopping_lists/form_edit.php` - Modifier liste
- [ ] `views/shopping_lists/delete_confirm.php` - Confirmation suppression

#### Pages Générales
- [ ] `views/home.php` - Accueil (non-connectés)
- [ ] `views/dashboard.php` - Tableau de bord (connectés)
- [ ] `views/layout.php` - Layout/Header/Footer (réutilisable)
- [ ] `views/errors/404.php` - Erreur 404
- [ ] `views/errors/403.php` - Erreur 403 (permission refusée)

### 2️⃣ STYLE CSS (PRIORITÉ MOYENNE)

- [ ] `public/style.css` - Style global
  - [ ] Layout responsive (mobile-first)
  - [ ] Navigation/Menu
  - [ ] Formulaires
  - [ ] Tableaux
  - [ ] Alertes/Messages d'erreur
  - [ ] **Conformité Accessibilité**:
    - [ ] Contraste texte/fond (WCAG AA)
    - [ ] Polices lisibles
    - [ ] Taille minimale 14px
    - [ ] Espacements suffisants

### 3️⃣ JAVASCRIPT (PRIORITÉ BASSE)

- [ ] `public/js/main.js` - Interactions
  - [ ] Confirmation suppression avec modal
  - [ ] Calcul dynamique du coût (lors changement quantité)
  - [ ] Toggle produit coché (AJAX)
  - [ ] Validation formulaire côté client

### 4️⃣ SÉCURITÉ (PRIORITÉ HAUTE)

- [ ] Ajouter `htmlspecialchars()` dans les vues pour XSS
- [ ] CSRF tokens sur formulaires
- [ ] Rate limiting connexion
- [ ] Input sanitization (trim, filter_var)
- [ ] Vérifier SQL injection (requêtes préparées ✅)
- [ ] Teste edge cases (produits supprimés, etc.)

### 5️⃣ TESTS (PRIORITÉ MOYENNE)

- [ ] `tests/Unit/UserTest.php`
  - [ ] `testCreateUser()`
  - [ ] `testUserCannotAccessOthersList()`
  - [ ] `testAdminCanDeleteOthersList()`
- [ ] `tests/Unit/ProductTest.php`
  - [ ] `testCreateProduct()`
  - [ ] `testUserCannotDeleteOthersProduct()`
- [ ] `tests/Unit/ShoppingListTest.php`
  - [ ] `testCalculateTotalCost()` - **Important**
  - [ ] `testAddProductToList()`
  - [ ] `testCalculateCorrectTotal()`
- [ ] `tests/Feature/` - Tests d'intégration

### 6️⃣ ACCESSIBILITÉ WEB (PRIORITÉ MOYENNE)

- [ ] `aria-label` sur boutons
- [ ] `alt` sur images
- [ ] Navigation au clavier (Tab)
- [ ] Focus visible sur éléments
- [ ] Couleurs: contraste minimum 4.5:1 (texte/fond)
- [ ] Test avec outils: WAVE, Lighthouse, axe

### 7️⃣ ADMIN PANEL (OPTIONNEL)

- [ ] `views/admin/users.php` - Gérer utilisateurs
  - [ ] Lister users
  - [ ] Changer rôle (user ↔ admin)
  - [ ] Supprimer user
- [ ] `views/admin/dashboard.php` - Statistiques
  - [ ] Nombre users
  - [ ] Nombre listes/produits
  - [ ] Graphiques (optionnel)

### 8️⃣ DOCUMENTATION & GITHUB

- [ ] Créer repo GitHub
- [ ] Ajouter `.gitignore` ✅
- [ ] Commit initial
- [ ] `CONTRIBUTING.md`
- [ ] Badges README (tests, coverage, etc.)

---

## 🔍 Checklist d'Accessibilité

Pour chaque page créer:
- [ ] Contrastes vérifiés (WCAG AA)
- [ ] Images ont `alt` (ou `aria-label` si icône)
- [ ] Formulaires ont `<label>` + `for`
- [ ] Pas de couleur seule pour informer
- [ ] Test clavier entièrement possible
- [ ] Headings hiérarchiques (h1 > h2 > h3)
- [ ] Focus visible

---

## 📝 Notes pour BAC+2

Ce projet utilise:
- **MVC classique** (pas Laravel, juste approche)
- **PDO** pour BD (sécurisé)
- **Sessions PHP** pour auth
- **HTML5 sémantique**
- **CSS3 responsive**
- **JavaScript vanilla** (pas jQuery)

Concept clé: **Moindre privilège** = User normal ne peut modifier que SES données, Admin peut modifier tout.

---

## 🚀 Ordre de Priorité

1. **Vues de base** (public_list, my_lists, detail) 🔴
2. **CSS + Accessibilité** 🟠
3. **Tests unitaires** 🟠
4. **JavaScript interactions** 🟡
5. **Sécurité avancée** 🟡
6. **Admin panel** 🟢
7. **Optimisations** 🟢

---

Dernière mise à jour: 31/03/2026
