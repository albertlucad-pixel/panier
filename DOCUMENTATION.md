# 📚 Documentation Complète - PANIER

## 📑 Table des Matières

1. **README.md** - Vue d'ensemble générale
2. **GUIDE_CONNEXION.md** - Guide d'accès et comptes
3. **SCHEMA_RELATIONNEL.md** - Schéma de la base de données
4. **SCHEMA_USECASES.md** - Diagrammes des use cases
5. **DOCUMENTATION.md** (ce fichier) - Index complet

---

## 🎯 Quick Start

```bash
# 1. Créer la BDD
mysql -u root panier_db < script.sql

# 2. Créer les comptes
php create_admin.php
php create_test_account.php

# 3. Lancer le serveur
php -S localhost:8000 -t public

# 4. Ouvrir dans le navigateur
http://localhost:8000
```

### Identifiants

| Rôle | Email | Mdp |
|------|-------|-----|
| Admin | admin@panier.local | admin123 |
| User | test@gmail.com | Test1234@ |

---

## 🏗️ Architecture Globale

```
┌─────────────────────────────────────────────────────────────┐
│                    PANIER WEB APPLICATION                   │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
    ┌────────┐         ┌────────┐         ┌────────┐
    │ PUBLIC │         │ CONFIG │         │  APP   │
    │ INDEX  │         │  DB    │         │ MODELS │
    ├────────┤         ├────────┤         ├────────┤
    │Router  │         │PDO     │         │User    │
    │Views   │         │Helpers │         │Product │
    │Styles  │         │        │         │ShopList│
    │JS      │         │        │         │Admin   │
    └────────┘         └────────┘         └────────┘
        │                   │                   │
        └───────────────────┼───────────────────┘
                            │
                    ┌───────▼────────┐
                    │   MYSQL BDD    │
                    ├────────────────┤
                    │ users          │
                    │ shopping_lists │
                    │ items          │
                    │ list_item      │
                    └────────────────┘
```

---

## 🗄️ Base de Données - Vue Simplifiée

```
┌──────────────┐
│    USERS     │
├──────────────┤
│ id (PK)      │
│ name         │
│ email        │
│ password     │
│ role*        │◄─── ENUM('user','admin')
│ created_at   │
└──────────────┘
      │
      │ (1,N)
      │
      ├─────────────────────┬──────────────────┤
      │                     │                  │
      ▼                     ▼                  ▼
┌─────────────────┐  ┌──────────────┐
│ SHOPPING_LISTS  │  │    ITEMS     │
├─────────────────┤  ├──────────────┤
│ id (PK)         │  │ id (PK)      │
│ user_id (FK)    │  │ user_id (FK) │
│ name            │  │ name         │
│ description     │  │ price        │
│ is_public       │  │ category     │
│ is_completed    │  │ is_bio       │
│ created_at      │  │ nutri_score  │
└─────────────────┘  │ created_at   │
      │              └──────────────┘
      │ (1,N)              ▲
      │                    │ (N,M)
      │         ┌──────────┘
      │         │
      ▼         │
┌──────────────────┐
│   LIST_ITEM      │◄─── Pivot Table (Relation N,M)
├──────────────────┤
│ id (PK)          │
│ shopping_list_id │
│ item_id          │
│ quantity         │
│ unit             │
│ is_checked       │
│ created_at       │
└──────────────────┘
```

---

## 🎭 Acteurs et Rôles

```
                    ┌─────────────────┐
                    │  UTILISATEURS   │
                    └─────────────────┘
                            │
            ┌───────────────┼───────────────┐
            │               │               │
            ▼               ▼               ▼
        ┌────────┐     ┌────────┐     ┌────────┐
        │Visiteur│     │ User   │     │ Admin  │
        ├────────┤     ├────────┤     ├────────┤
        │Non auth│     │Auth    │     │Auth +  │
        │        │     │        │     │Spécial │
        └────────┘     └────────┘     └────────┘
             │              │              │
             │              │              │
        Permissions    Permissions    Permissions
        ├─Voir         ├─Voir         ├─Voir
        │ accueil      │ profil       │ tout
        ├─Voir         ├─CRUD         ├─CRUD
        │ paniers      │ paniers      │ tout
        │ publics      ├─CRUD         ├─Sup
        ├─S'inscrire   │ produits     │ tout
        ├─Se           ├─Se           ├─Se
        │ connecter    │ déconnecter  │ déconnec.
        └─            └─            └─
```

---

## 📊 Flux d'Authentification

```
                    ┌─────────────┐
                    │   ACCUEIL   │
                    └─────────────┘
                          │
                ┌─────────┴─────────┐
                │                   │
        [Se connecter]      [S'inscrire]
                │                   │
                ▼                   ▼
            ┌──────────┐       ┌──────────┐
            │  LOGIN   │       │ REGISTER │
            └──────────┘       └──────────┘
                │                   │
         Vérif identif         Vérif données
                │                   │
                ├─ OK ─┐      ┌─ OK ─┤
                └──────┼─────┬┘      │
                       │     │       │
                       ▼     ▼       ▼
                    ┌─────────────────┐
                    │ SESSION ACTIVE  │
                    │ (user_id)       │
                    │ (user_role)     │
                    └─────────────────┘
                          │
            ┌─────────────┼─────────────┐
            │             │             │
        role='user'   role='admin'      │
            │             │             │
            ▼             ▼             ▼
        ┌────────┐   ┌────────┐    ┌──────────┐
        │DASHBOARD    │DASHBOARD    │ ERREUR   │
        │USER         │ADMIN + STATS│ ACCÈS    │
        │(Paniers)    │(Gestion)    │ REFUSÉ   │
        └────────┘    └────────┘    └──────────┘
```

---

## 🔄 Flux de Gestion des Paniers

```
                    UTILISATEUR CONNECTÉ
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
    [Créer]            [Consulter]          [Éditer]
        │                   │                   │
        ▼                   ▼                   ▼
    ┌────────┐         ┌────────┐         ┌────────┐
    │ FORM   │         │ DETAIL │         │ FORM   │
    │ CRÉER  │         │ LECTURE│         │ ÉDITER │
    └────────┘         └────────┘         └────────┘
        │                   │                   │
    Nom + DESC         Voir articles      ┌─────┴─────┐
        │              + quantités        │           │
        │              + prix total       ▼           ▼
        │                  │         [Cocher]    [Quantités]
        │                  │              │           │
        ▼                  │              └─────┬─────┘
    ┌────────┐            │                    │
    │ PANIER │            │         ┌──────────▼──────────┐
    │ CRÉÉ   │            │         │ Traitement         │
    └────────┘            │         ├────────────────────┤
        │                 │         │ - Ajouter produits │
        │                 │         │ - Modif quantités  │
        │                 │         │ - Suppr produits   │
        │                 │         └────────┬───────────┘
        │                 │                  │
        └────────┬────────┴──────────────────┘
                 │
                 ▼
        [Supprimer Panier]
                 │
        Confirmation Y/N
                 │
            ┌────┴────┐
            │          │
            ▼          ▼
         OUI          NON
            │          │
            ▼          │
        SUPPRIMÉ ◄─────┘
```

---

## 🛡️ Flux de Sécurité

```
REQUÊTE UTILISATEUR
        │
        ▼
┌──────────────────┐
│ Vérifier Session │
└──────────────────┘
        │
    ┌───┴───┐
    │       │
  OUI      NON ──────────────┐
    │                        │
    ▼                        ▼
┌──────────────┐    ┌─────────────────┐
│ Session OK   │    │ Redirection     │
└──────────────┘    │ Login           │
    │               └─────────────────┘
    ▼
┌──────────────────┐
│ Vérifier Rôle    │
│ (si requis)      │
└──────────────────┘
    │
┌───┴────┐
│        │
OK    REFUSÉ ───────────┐
│                       │
▼                       ▼
┌──────────┐    ┌─────────────┐
│Traiter   │    │ Erreur 403  │
│Requête   │    │ Accès déni  │
└──────────┘    └─────────────┘
    │
    ▼
┌──────────────────────┐
│ Valider Données      │
│ (Serveur)            │
└──────────────────────┘
    │
┌───┴───┐
│       │
OK    ERREUR ───┐
│               │
▼               ▼
┌──────┐    ┌────────────┐
│OK    │    │ Erreurs    │
│Exéc. │    │ Formulaire │
└──────┘    └────────────┘
    │
    ▼
┌──────────────────┐
│ Exécuter Action  │
│ (DB Query)       │
└──────────────────┘
    │
    ▼
┌──────────────────┐
│ Redirection ou   │
│ Affichage Vue    │
└──────────────────┘
```

---

## 📋 Checklist des Fonctionnalités

### Authentification
- ✅ Inscription avec validation mdp fort
- ✅ Connexion/Déconnexion
- ✅ Session gestion
- ✅ Rôles (user/admin)

### Utilisateur
- ✅ Tableau de bord
- ✅ Profil
- ✅ CRUD Paniers
- ✅ CRUD Produits
- ✅ Édition avancée (cocher/décocher produits)

### Admin
- ✅ Tableau de bord spécial
- ✅ Voir tous les users
- ✅ Voir tous les paniers
- ✅ Voir tous les produits
- ✅ Supprimer users/paniers/produits
- ✅ Statistiques

### Sécurité
- ✅ PASSWORD_BCRYPT hashing
- ✅ Vérification propriété
- ✅ Vérification rôle
- ✅ Escape HTML
- ✅ PDO Prepared Statements
- ⏳ CSRF tokens (non implémenté)

---

## 📂 Fichiers Importants

```
panier/
├── README.md                 ← Lire en premier
├── GUIDE_CONNEXION.md        ← Identifiants
├── SCHEMA_RELATIONNEL.md     ← Structure BDD
├── SCHEMA_USECASES.md        ← Use cases
├── DOCUMENTATION.md          ← Ce fichier
│
├── public/
│   ├── index.php            ← Router principal
│   ├── style.css            ← Styles
│   └── js/main.js           ← Scripts
│
├── app/
│   ├── Http/Controllers/    ← Logique applicative
│   │   ├── AuthController.php
│   │   ├── AdminController.php
│   │   ├── ProductController.php
│   │   └── ShoppingListController.php
│   │
│   └── Models/              ← Modèles BDD
│       ├── User.php
│       ├── Product.php
│       └── ShoppingList.php
│
├── config/
│   ├── database.php         ← Config PDO
│   └── helpers.php          ← Fonctions utilitaires
│
├── views/                   ← Templates HTML
│   ├── auth/
│   ├── shopping_lists/
│   ├── products/
│   ├── admin/
│   ├── layout.php
│   └── ...
│
├── script.sql              ← Création BDD
├── create_admin.php        ← Script admin
└── create_test_account.php ← Script test
```

---

## 🚀 Déploiement

### Dev (Laragon)
```bash
php -S localhost:8000 -t public
# http://localhost:8000
```

### Production
1. Configurer HTTPS
2. Changer mot de passe admin
3. Configurer domaine
4. Backups réguliers
5. Monitoring

---

## 🔗 Fichiers Liés

- 📖 **README.md**: Vue d'ensemble
- 🔐 **GUIDE_CONNEXION.md**: Identifiants & accès
- 🏗️ **SCHEMA_RELATIONNEL.md**: Structure BD
- 🎭 **SCHEMA_USECASES.md**: Use cases détaillés
- 📚 **DOCUMENTATION.md**: Ce fichier (index)

---

Bienvenue dans Panier ! 🎉

