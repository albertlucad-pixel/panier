# 📊 Schéma Relationnel - PANIER

## Modèle Entité-Association (MCD)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                                 USERS                                    │
├─────────────────────────────────────────────────────────────────────────┤
│ id (PK)                                                                  │
│ name (VARCHAR 255)                                                       │
│ email (VARCHAR 255) UNIQUE                                              │
│ password (VARCHAR 255)                                                   │
│ role (ENUM: 'user', 'admin')                                            │
│ created_at (TIMESTAMP)                                                   │
│ updated_at (TIMESTAMP)                                                   │
└─────────────────────────────────────────────────────────────────────────┘
          │
          │ (1,N) crée
          │
          ├──────────────────────────┬──────────────────┤
          │                          │                  │
          ▼                          ▼                  ▼
┌──────────────────────┐  ┌──────────────────────┐  ┌──────────────────────┐
│   SHOPPING_LISTS     │  │      ITEMS           │  │   (admin seulement)  │
├──────────────────────┤  ├──────────────────────┤  │  Gestion système     │
│ id (PK)              │  │ id (PK)              │  │  - Users             │
│ user_id (FK)         │◄─┤ user_id (FK)         │  │  - Items             │
│ name (VARCHAR)       │  │ name (VARCHAR)       │  │  - Lists             │
│ description (TEXT)   │  │ description (TEXT)   │  │  - Suppression       │
│ is_public (BOOLEAN)  │  │ price (DECIMAL)      │  │  - Rôles             │
│ is_completed (BOOL)  │  │ category (VARCHAR)   │  └──────────────────────┘
│ created_at           │  │ is_bio (BOOLEAN)     │
│ updated_at           │  │ nutri_score (ENUM)   │
└──────────────────────┘  │ created_at           │
          │               │ updated_at           │
          │               └──────────────────────┘
          │ (1,N)                   ▲
          │                         │ (N,M)
          │                         │
          │         ┌───────────────┴───────────────┐
          │         │                               │
          ▼         │                               │
┌──────────────────────┐                    ┌──────────────────────┐
│     LIST_ITEM        │                    │   (PIVOT TABLE)      │
├──────────────────────┤                    │  Relation N,N        │
│ id (PK)              │                    │  portée de données   │
│ shopping_list_id(FK) │                    └──────────────────────┘
│ item_id (FK)         │
│ quantity (DECIMAL)   │
│ unit (VARCHAR)       │
│ is_checked (BOOLEAN) │
│ notes (TEXT)         │
│ created_at           │
│ updated_at           │
└──────────────────────┘
```

## Relations

### 1. **USERS → SHOPPING_LISTS** (1,N)
- **Cardinalité**: Un utilisateur peut créer plusieurs listes
- **Type**: One-to-Many (1,N)
- **Intégrité**: CASCADE DELETE (suppression de l'user = suppression de ses listes)
- **Clé étrangère**: `shopping_lists.user_id` → `users.id`

### 2. **USERS → ITEMS** (1,N)
- **Cardinalité**: Un utilisateur peut créer plusieurs produits
- **Type**: One-to-Many (1,N)
- **Intégrité**: CASCADE DELETE (suppression de l'user = suppression de ses produits)
- **Clé étrangère**: `items.user_id` → `users.id`

### 3. **SHOPPING_LISTS → LIST_ITEM** (1,N)
- **Cardinalité**: Une liste contient plusieurs articles
- **Type**: One-to-Many (1,N)
- **Intégrité**: CASCADE DELETE (suppression d'une liste = suppression de ses articles)
- **Clé étrangère**: `list_item.shopping_list_id` → `shopping_lists.id`

### 4. **ITEMS ↔ SHOPPING_LISTS** (N,M) via LIST_ITEM
- **Cardinalité**: Un produit peut être dans plusieurs listes, une liste contient plusieurs produits
- **Type**: Many-to-Many (N,M)
- **Table pivot**: `LIST_ITEM`
- **Données portées**: `quantity`, `unit`, `is_checked`, `notes`
- **Unicité**: UNIQUE(shopping_list_id, item_id) - Un produit ne peut être qu'une fois par liste
- **Intégrité**: RESTRICT DELETE sur item_id (empêche la suppression d'un produit s'il est utilisé)

## Dépendances Fonctionnelles

```
Users
├─ id → name, email, password, role
├─ email → id (clé candidate)
└─ role → permissions (admin vs user)

ShoppingLists
├─ id → name, description, user_id, is_public, is_completed
├─ user_id → user_name (via JOIN)
└─ id → list_items (1,N)

Items
├─ id → name, price, category, is_bio, nutri_score, user_id
├─ user_id → creator_name (via JOIN)
└─ id → list_items (N,M)

ListItem
├─ (shopping_list_id, item_id) → quantity, unit, is_checked, notes
├─ shopping_list_id → shopping_list_data
└─ item_id → item_data
```

## Normalisation

### Forme Normale de Boyce-Codd (BCNF)

✅ **1NF** - Tous les attributs contiennent des valeurs atomiques
- Pas de listes/tableaux dans les colonnes
- Chaque colonne contient une seule valeur

✅ **2NF** - Tous les attributs non-clés dépendent de toute la clé primaire
- Pas de dépendances partielles
- Clés primaires simples ou complètes

✅ **3NF** - Pas de dépendances transitives entre attributs non-clés
- Les attributs non-clés ne dépendent que de la clé primaire
- Pas d'attribut calculé

✅ **BCNF** - Chaque déterminant est une clé candidate
- Relations bien structurées
- Redondance minimale

## Indices (INDEX)

```sql
-- USERS
INDEX idx_email (email)              -- Connexion rapide
INDEX idx_role (role)                -- Filtrage des admins

-- SHOPPING_LISTS
INDEX idx_user_id (user_id)          -- Récupérer les listes d'un user
INDEX idx_is_public (is_public)      -- Listes publiques
INDEX idx_is_completed (is_completed)-- Listes complétées

-- ITEMS
INDEX idx_name (name)                -- Recherche de produits
INDEX idx_user_id (user_id)          -- Produits d'un utilisateur
INDEX idx_price (price)              -- Tri/filtrage par prix

-- LIST_ITEM
INDEX idx_shopping_list_id           -- Récupérer les articles d'une liste
INDEX idx_item_id                    -- Vérifier les listes contenant un item
INDEX idx_is_checked                 -- Filtrer les articles cochés
```

## Intégrité Référentielle

| Relation | Clé Étrangère | Référence | Action DELETE | Action UPDATE |
|----------|---------------|-----------|---------------|---------------|
| Users → ShoppingLists | user_id | users.id | CASCADE | CASCADE |
| Users → Items | user_id | users.id | CASCADE | CASCADE |
| ShoppingLists → ListItem | shopping_list_id | shopping_lists.id | CASCADE | CASCADE |
| Items → ListItem | item_id | items.id | RESTRICT | RESTRICT |

## Statistiques Potentielles

```
-- Nombre de listes par utilisateur
SELECT user_id, COUNT(*) as list_count 
FROM shopping_lists 
GROUP BY user_id;

-- Total produits utilisés par liste
SELECT sl.id, COUNT(li.id) as item_count
FROM shopping_lists sl
LEFT JOIN list_item li ON sl.id = li.shopping_list_id
GROUP BY sl.id;

-- Coût total par liste
SELECT sl.id, SUM(i.price * li.quantity) as total_cost
FROM shopping_lists sl
JOIN list_item li ON sl.id = li.shopping_list_id
JOIN items i ON li.item_id = i.id
GROUP BY sl.id;

-- Produits les plus utilisés
SELECT i.id, i.name, COUNT(li.id) as usage_count
FROM items i
JOIN list_item li ON i.id = li.item_id
GROUP BY i.id
ORDER BY usage_count DESC;
```
