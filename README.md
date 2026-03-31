# 🛒 Panier - Gestionnaire de Listes de Courses

Projet Laravel pour gérer les listes de courses.

## 📋 Fonctionnalités

- ✅ **CRUD Listes de Courses**: Créer, lire, modifier, supprimer des listes
- ✅ **CRUD Articles**: Gérer les articles dans les listes
- ✅ **Utilisateurs**: Système d'authentification
- ✅ **Associations**: 
  - 1 utilisateur → N listes de courses (1,N)
  - N listes ↔ N articles (M,N avec données portées)

## 🚀 Installation

### Prérequis
- PHP 8.1+
- MySQL
- Composer
- Laragon

### Étapes

1. **Cloner le projet**
```bash
git clone https://github.com/[TON_USERNAME]/panier.git
cd panier
```

2. **Configurer l'environnement**
```bash
cp .env.example .env
```

3. **Configurer la base de données** dans `.env`:
```env
DB_DATABASE=panier_db
DB_USERNAME=root
DB_PASSWORD=
```

4. **Créer la base de données**
```bash
mysql -u root -e "CREATE DATABASE panier_db"
```

5. **Lancer les migrations**
```bash
php artisan migrate
```

6. **Démarrer l'application**
```bash
php artisan serve
```

Accède à `http://localhost:8000`

## 📁 Structure du projet

```
panier/
├── app/                    # Code applicatif
│   ├── Http/
│   │   └── Controllers/   # Contrôleurs
│   └── Models/            # Modèles Eloquent
├── database/
│   ├── migrations/        # Fichiers de migration
│   └── seeders/           # Seeders de données
├── resources/
│   ├── views/             # Vues Blade
│   ├── css/               # Styles
│   └── js/                # Scripts
├── routes/                # Définition des routes
├── config/                # Configuration
└── public/                # Fichiers publics
```

## 🗄️ Schéma de Base de Données

### Tables

**users**
- id (PK)
- name
- email (unique)
- password
- timestamps

**shopping_lists**
- id (PK)
- user_id (FK → users)
- name
- description
- timestamps

**items**
- id (PK)
- name
- timestamps

**list_item** (Pivot avec données)
- id (PK)
- shopping_list_id (FK → shopping_lists)
- item_id (FK → items)
- quantity
- unit (kg, pcs, etc.)
- checked (boolean)
- timestamps

### Relations

- **Users (1) ↔ (N) Shopping Lists**
- **Shopping Lists (N) ↔ (N) Items** (via `list_item`)

## ✅ Conformité

- ✨ **Accessibilité Web**: Contraste, alternatives d'images, navigation au clavier
- 🧪 **Tests Unitaires**: PHPUnit pour valider la logique métier
- 📚 **Documentation**: Code documenté et README complet
- 🔄 **Versioning Git**: Structure clonable et reproducible

## 👨‍💻 Développement

### Tests
```bash
php artisan test
```

### Lint/Style
```bash
php artisan lint
```

## 📄 Licence

MIT
