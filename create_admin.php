<?php
/**
 * Script de création d'un utilisateur administrateur
 * À exécuter une seule fois pour créer le premier admin
 * Usage: php create_admin.php
 */

// Charger la configuration de la base de données
require_once __DIR__ . '/config/database.php';

// Vérifier si un utilisateur admin existe déjà
$stmt = $pdo->query('SELECT COUNT(*) as count FROM users WHERE role = "admin"');
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] > 0) {
    echo "❌ Il existe déjà un administrateur dans la base de données.\n";
    echo "   Vous pouvez supprimer les admins existants manuellement en SQL si nécessaire.\n";
    exit(1);
}

// Données de l'admin par défaut
$adminName = 'Administrateur';
$adminEmail = 'admin@panier.local';
$adminPassword = 'admin123'; // À CHANGER ABSOLUMENT EN PRODUCTION !

// Hasher le mot de passe
$hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);

try {
    // Insérer l'utilisateur admin
    $stmt = $pdo->prepare('
        INSERT INTO users (name, email, password, role, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ');
    
    if ($stmt->execute([$adminName, $adminEmail, $hashedPassword, 'admin'])) {
        $adminId = $pdo->lastInsertId();
        echo "\n✅ Administrateur créé avec succès !\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID Admin:     " . $adminId . "\n";
        echo "Nom:          " . $adminName . "\n";
        echo "Email:        " . $adminEmail . "\n";
        echo "Mot de passe: " . $adminPassword . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "\n⚠️  IMPORTANT: Changez le mot de passe dès la première connexion !\n";
        echo "⚠️  Cette information n'apparaît qu'une seule fois.\n\n";
    } else {
        echo "❌ Erreur lors de la création de l'administrateur.\n";
        exit(1);
    }
} catch (PDOException $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "\n";
    exit(1);
}
?>
