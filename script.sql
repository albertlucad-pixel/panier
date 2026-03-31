-- =====================================================
-- PANIER - Gestionnaire de Listes de Courses
-- Script de création de base de données
-- =====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS panier_db;
USE panier_db;

-- =====================================================
-- TABLE: users
-- Relation: (1) à (N) shopping_lists
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: shopping_lists
-- Relation: (1) de users, (N) vers list_item
-- =====================================================
CREATE TABLE IF NOT EXISTS shopping_lists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_completed (is_completed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: items
-- Relation: Créé par users, (N) vers list_item (via pivot)
-- =====================================================
CREATE TABLE IF NOT EXISTS items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100) NULL,
    is_bio BOOLEAN DEFAULT FALSE,
    nutri_score ENUM('A', 'B', 'C', 'D', 'E') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_name (name),
    INDEX idx_user_id (user_id),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: list_item (Pivot table - Relation N,N porteuse de données)
-- Relations: (N) shopping_lists ↔ (N) items
-- Données portées: quantity, unit, is_checked
-- =====================================================
CREATE TABLE IF NOT EXISTS list_item (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shopping_list_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity DECIMAL(10, 2) DEFAULT 1.00,
    unit VARCHAR(50) NOT NULL DEFAULT 'pcs',
    is_checked BOOLEAN DEFAULT FALSE,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shopping_list_id) REFERENCES shopping_lists(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_list_item (shopping_list_id, item_id),
    INDEX idx_shopping_list_id (shopping_list_id),
    INDEX idx_item_id (item_id),
    INDEX idx_is_checked (is_checked)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES DE TEST / SEED DATA
-- =====================================================

-- Insérer des utilisateurs de test
INSERT INTO users (name, email, password, role, created_at) VALUES
('Alice Dupont', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW()),
('Bob Martin', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', NOW()),
('Charlie Lefevre', 'charlie@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());

-- Insérer des articles créés par les utilisateurs
INSERT INTO items (user_id, name, description, price, category, is_bio, nutri_score, created_at) VALUES
(1, 'Lait', 'Lait demi-écrémé 1L', 1.50, 'Produits laitiers', 1, 'A', NOW()),
(1, 'Pain', 'Baguette française', 0.95, 'Boulangerie', 0, 'B', NOW()),
(1, 'Beurre', 'Beurre doux 250g', 3.20, 'Produits laitiers', 1, 'D', NOW()),
(1, 'Œufs', 'Boîte de 6 œufs', 2.50, 'Produits laitiers', 0, 'A', NOW()),
(2, 'Fromage', 'Fromage blanc 500g', 2.80, 'Produits laitiers', 0, 'B', NOW()),
(2, 'Tomates', 'Tomates fraîches 1kg', 2.30, 'Fruits et légumes', 1, 'A', NOW()),
(2, 'Oignon', 'Oignons blancs', 0.80, 'Fruits et légumes', 1, 'A', NOW()),
(3, 'Ail', 'Ail frais', 1.20, 'Fruits et légumes', 1, 'A', NOW()),
(3, 'Huile d\'olive', 'Huile d\'olive vierge extra 1L', 8.50, 'Condiments', 1, 'B', NOW()),
(1, 'Pommes', 'Pommes rouges 1kg', 2.80, 'Fruits et légumes', 1, 'A', NOW()),
(1, 'Bananes', 'Bananes jaunes', 1.50, 'Fruits et légumes', 0, 'A', NOW()),
(2, 'Poulet', 'Filet de poulet 500g', 7.50, 'Viandes', 0, 'B', NOW()),
(2, 'Riz', 'Riz blanc 1kg', 1.80, 'Féculents', 0, 'B', NOW()),
(3, 'Pâtes', 'Pâtes spaghetti 500g', 1.20, 'Féculents', 0, 'B', NOW()),
(3, 'Sel', 'Sel fin 1kg', 0.50, 'Condiments', 0, 'A', NOW());

-- Insérer des listes de courses
INSERT INTO shopping_lists (user_id, name, description, is_completed, created_at) VALUES
(1, 'Courses du lundi', 'Courses de la semaine pour la maison', FALSE, NOW()),
(1, 'Courses du week-end', 'Repas du dimanche', FALSE, NOW()),
(2, 'Marché hebdomadaire', 'Produits frais pour 2 semaines', FALSE, NOW()),
(3, 'Courses urgentes', 'Articles oubliés', TRUE, NOW());

-- Insérer les associations (list_item) - Données portées
INSERT INTO list_item (shopping_list_id, item_id, quantity, unit, is_checked, notes, created_at) VALUES
-- Liste 1 - Alice (lundi)
(1, 1, 2, 'L', FALSE, 'Lait demi-écrémé préféré', NOW()),
(1, 2, 1, 'pcs', FALSE, 'Baguette fraîche', NOW()),
(1, 3, 1, 'pcs', FALSE, 'Beurre doux', NOW()),
(1, 4, 1, 'boîte', FALSE, '6 œufs frais', NOW()),
(1, 5, 1, 'pcs', TRUE, 'Fromage blanc nature', NOW()),
(1, 6, 1.5, 'kg', FALSE, 'Tomates bien rouges', NOW()),
(1, 7, 2, 'pcs', FALSE, 'Oignons moyens', NOW()),

-- Liste 2 - Alice (week-end)
(2, 12, 0.5, 'kg', FALSE, 'Filet de poulet rôti', NOW()),
(2, 13, 1, 'kg', FALSE, 'Riz basmati', NOW()),
(2, 14, 1, 'pcs', FALSE, 'Pâtes fraiches', NOW()),
(2, 9, 1, 'L', FALSE, 'Huile d\'olive qualité supérieure', NOW()),
(2, 10, 2, 'kg', FALSE, 'Pommes pour compote', NOW()),

-- Liste 3 - Bob (marché)
(3, 1, 1.5, 'L', FALSE, 'Lait écrémé', NOW()),
(3, 2, 2, 'pcs', FALSE, 'Pain complet', NOW()),
(3, 8, 3, 'gousses', FALSE, 'Ail frais pour l\'assaisonnement', NOW()),
(3, 11, 1, 'kg', FALSE, 'Bananes bien jaunes', NOW()),
(3, 15, 1, 'kg', FALSE, 'Sel fin de mer', NOW()),

-- Liste 4 - Charlie (urgentes - complétée)
(4, 1, 1, 'L', TRUE, 'Lait pour demain', NOW()),
(4, 4, 1, 'boîte', TRUE, 'Œufs frais', NOW()),
(4, 3, 1, 'pcs', TRUE, 'Beurre pour la cuisine', NOW());

-- =====================================================
-- AFFICHAGE DES STATISTIQUES
-- =====================================================
SELECT '✅ Base de données créée avec succès!' AS status;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_items FROM items;
SELECT COUNT(*) AS total_shopping_lists FROM shopping_lists;
SELECT COUNT(*) AS total_list_items FROM list_item;

-- =====================================================
-- REQUÊTES D'EXEMPLE POUR TESTER
-- =====================================================

-- Voir toutes les listes de courses d'Alice avec ses articles
/*
SELECT 
    sl.name AS liste,
    sl.description,
    sl.is_completed,
    i.name AS article,
    li.quantity,
    li.unit,
    li.is_checked,
    li.notes
FROM shopping_lists sl
JOIN list_item li ON sl.id = li.shopping_list_id
JOIN items i ON li.item_id = i.id
WHERE sl.user_id = 1
ORDER BY sl.name, i.name;
*/

-- Voir les articles non cochés d'une liste spécifique
/*
SELECT 
    i.name,
    li.quantity,
    li.unit,
    li.notes
FROM list_item li
JOIN items i ON li.item_id = i.id
WHERE li.shopping_list_id = 1 AND li.is_checked = FALSE
ORDER BY i.name;
*/

-- Voir le taux de complétude des listes
/*
SELECT 
    u.name AS utilisateur,
    sl.name AS liste,
    COUNT(li.id) AS total_articles,
    SUM(CASE WHEN li.is_checked = TRUE THEN 1 ELSE 0 END) AS articles_coches,
    ROUND(100 * SUM(CASE WHEN li.is_checked = TRUE THEN 1 ELSE 0 END) / COUNT(li.id), 2) AS pourcentage_complete
FROM users u
JOIN shopping_lists sl ON u.id = sl.user_id
LEFT JOIN list_item li ON sl.id = li.shopping_list_id
GROUP BY u.id, sl.id, u.name, sl.name
ORDER BY u.name, sl.name;
*/
