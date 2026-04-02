
# 🛒 Panier - Gestionnaire de Listes de Courses

Système web de gestion de listes de courses avec authentification utilisateur et panneau d'administration.

## 📋 Fonctionnalités

### ✅ Utilisateur Non Connecté
- Consulter les paniers publics
- Consulter les détails des paniers publics (articles, prix, total)
- S'inscrire avec validation de mot de passe fort
- Se connecter à son compte

### ✅ Utilisateur Connecté
- **Tableau de bord personnel**: Vue d'ensemble des paniers
- **Gestion des paniers**:
  - Créer une liste de courses
  - Éditer une liste (ajouter/retirer/modifier articles)
  - Consulter les détails
  - Supprimer une liste
- **Gestion des produits**:
  - Créer des produits réutilisables
  - Éditer les produits
  - Consulter le catalogue personnel
  - Supprimer des produits
- **Profil**: Voir ses informations, se déconnecter

### ✅ Administrateur
- **Accès à tous les droits utilisateur** (paniers, produits personnels, profil)
- **Tableau de bord administrateur** avec 3 onglets:
  - 👥 **Utilisateurs**: Voir tous les comptes, supprimer des utilisateurs
  - 📋 **Paniers**: Voir tous les paniers du système, supprimer
  - 🛒 **Produits**: Voir tous les produits, supprimer
- **Statistiques en temps réel**: Nombre total d'utilisateurs, paniers, produits

## 🏗️ Architecture

```
Panier/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php      (Authentification)
│   │   ├── ShoppingListController.php (Gestion des paniers)
│   │   ├── ProductController.php   (Gestion des produits)
│   │   └── AdminController.php     (Gestion admin)
│   └── Models/
│       ├── User.php               (Modèle utilisateur)
│       ├── ShoppingList.php        (Modèle panier)
│       └── Product.php            (Modèle produit)
├── config/
│   ├── database.php              (Configuration PDO)
│   └── helpers.php               (Fonctions utilitaires)
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── shopping_lists/
│   │   ├── my_lists.php
│   │   ├── form.php
│   │   ├── form_edit.php
│   │   ├── detail.php
│   │   └── public_list.php
│   ├── products/
│   │   ├── my_products.php
│   │   ├── form.php
│   │   └── form_edit.php
│   ├── admin/
│   │   └── dashboard.php         (Tableau de bord admin)
│   ├── dashboard.php
│   ├── profile.php
│   └── layout.php               (Template principal)
├── public/
│   ├── index.php               (Router principal)
│   ├── style.css               (Styles)
│   └── js/main.js              (Scripts)
├── SCHEMA_RELATIONNEL.md        (Schéma BDD)
├── SCHEMA_USECASES.md           (Diagramme use cases)
├── script.sql                   (Création BDD)
├── create_admin.php             (Script création admin)
└── create_test_account.php      (Script création compte test)
```

## 🗄️ Base de Données

### Tables

1. **users** - Utilisateurs du système
   - id (PK)
   - name, email (UNIQUE), password (hashé)
   - role (ENUM: 'user', 'admin')
   - created_at, updated_at

2. **shopping_lists** - Listes de courses
   - id (PK)
   - user_id (FK → users)
   - name, description
   - is_public, is_completed
   - created_at, updated_at

3. **items** - Produits
   - id (PK)
   - user_id (FK → users)
   - name, price, description, category
   - is_bio, nutri_score
   - created_at, updated_at

4. **list_item** - Relation N,M (shopping_lists ↔ items)
   - id (PK)
   - shopping_list_id (FK)
   - item_id (FK)
   - quantity, unit, is_checked, notes
   - Contrainte UNIQUE: (shopping_list_id, item_id)

### Relations
- User → ShoppingLists (1,N) - CASCADE DELETE
- User → Items (1,N) - CASCADE DELETE
- ShoppingList ↔ Items (N,M) via list_item

📄 Voir `SCHEMA_RELATIONNEL.md` pour le détail complet.

## 🎭 Use Cases

Le système supporte 3 rôles principaux:

1. **Visiteur (Non connecté)**
   - Consulter paniers publics
   - S'inscrire / Se connecter

2. **Utilisateur (Connecté)**
   - Gérer ses paniers et produits
   - Consulter son profil

3. **Administrateur**
   - Tous les droits utilisateur +
   - Gérer tous les comptes, paniers, produits du système

📊 Voir `SCHEMA_USECASES.md` pour le diagramme complet avec tous les use cases détaillés.

## 🚀 Installation et Setup

### Prérequis
- PHP 8.1+
- MySQL 5.7+
- Laragon (ou serveur web classique)

### Étapes

#### 1. Cloner/Télécharger le projet
```bash
git clone https://github.com/albertlucad-pixel/composition
```
Placer le dossier `composition` dans le répertoire `www` de Laragon :
```
C:\laragon\www\panier\
```

#### 2. Créer la base de données
```bash
# Via Laragon en terminal:
mysql -u root -e
source script.sql
```

#### 5. Lancer le serveur
```bash
# Via Laragon: double-clic sur Panier
# Ou en terminal:
php -S localhost:8000 -t public
```

#### 6. Accéder à l'application
- Ouvrir: `http://localhost:8000` (ou `http://localhost/panier/public`)

## 👤 Comptes de Connexion

### 🔑 Administrateur
```
Email:    admin@panier.local
Mot de passe: admin123

⚠️ À CHANGER OBLIGATOIREMENT EN PRODUCTION
```

### 👤 Utilisateur Test
```
Email:    test@gmail.com
Mot de passe: Test1234@
```

### 📝 Créer un nouveau compte
- Cliquer sur "S'inscrire"
- Remplir le formulaire avec:
  - Nom d'utilisateur
  - Email (unique)
  - Mot de passe (8+ caractères, 1 MAJUSCULE, 1 minuscule, 1 chiffre, 1 caractère spécial)
  - Confirmation mot de passe

## 🔐 Sécurité

- ✅ **Authentification**: Mots de passe hachés avec PASSWORD_BCRYPT
- ✅ **Validation**: Côté serveur (données + permissions)
- ✅ **Autorisation**: Basée sur les rôles (moindre privilège)
- ✅ **Intégrité**: Contraintes DB (PK, FK, UNIQUE)
- ✅ **Protection**: Vérification de propriété/rôle sur toutes les actions
- ✅ **Échappement**: htmlspecialchars() sur tous les outputs

### Politique de mot de passe
Minimum requis:
- 8 caractères
- 1 lettre majuscule
- 1 lettre minuscule
- 1 chiffre
- 1 caractère spécial (@$!%*?&)

Exemple valide: `MyPass123!` ✅

## 📱 Navigation

### Visiteur
- Accueil → Stats et informations
- Paniers publics → Consulter les listes publiques
- Connexion / Inscription

### Utilisateur Connecté
- **Mes paniers** → CRUD des listes personnelles
- **Mes produits** → CRUD des produits personnels
- **Profil** → Infos compte
- **👨‍💼 Admin** (si admin) → Tableau de bord administrateur
- **Déconnexion**

### Admin
- Accès à **Tableau de bord admin** (👨‍💼 Admin)
  - Onglet Utilisateurs (👥)
  - Onglet Paniers (📋)
  - Onglet Produits (🛒)
- Tous les droits utilisateur

## 🛠️ Technologies

- **Backend**: PHP 8.1 (POO, MVC)
- **Base de données**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Sécurité**: PASSWORD_BCRYPT, moindre privilèges

## 📊 Diagrammes

### Schéma Relationnel
Voir `SCHEMA_RELATIONNEL.md`
- Diagramme ER
- Relations et cardinalités
- Intégrité référentielle

### Use Cases
Voir `SCHEMA_USECASES.md`
- Diagrammes détaillés pour chaque acteur
- 13+ Use Cases avec flux complets
- Matrice de permissions
- Scénarios alternatifs
- Modèle de sécurité

## 🧪 Tests

### Fonctionnalités testées
- ✅ Inscription avec validation mot de passe fort
- ✅ Connexion/Déconnexion
- ✅ CRUD Paniers (créer, consulter, éditer, supprimer)
- ✅ CRUD Produits
- ✅ Modification quantités dans paniers
- ✅ Affichage paniers publics
- ✅ Calcul des totaux dynamiques
- ✅ Gestion admin (voir, supprimer utilisateurs/paniers/produits)
- ✅ Protections d'accès (non-propriétaire ne peut pas éditer)
- ✅ Suppression d'utilisateur ne peut pas être l'admin connecté
- ✅ Édition de panier avec détection produits ajoutés/modifiés/supprimés

## � Support

Pour les problèmes:
1. Vérifier que la BDD est créée et le serveur lancé
2. Consulter les messages d'erreur
3. Vérifier les permissions des fichiers
4. Vérifier les identifiants de base de données dans `config/database.php`

