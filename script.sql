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
    is_public BOOLEAN DEFAULT FALSE,
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_public (is_public),
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
    price DECIMAL(15, 2) NOT NULL,
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

