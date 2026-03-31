<?php
/**
 * Script de création d'un compte de test
 * À exécuter une seule fois
 * Usage: php create_test_account.php
 */

// Charger la configuration de la base de données
require_once __DIR__ . '/config/database.php';

// Données du compte de test
$testName = 'Test User';
$testEmail = 'test@gmail.com';
$testPassword = 'Test1234@';

// Vérifier si le compte existe déjà
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$testEmail]);
if ($stmt->fetch()) {
    echo "ℹ️  Le compte de test existe déjà.\n";
    exit(0);
}

// Hasher le mot de passe
$hashedPassword = password_hash($testPassword, PASSWORD_BCRYPT);

try {
    // Insérer l'utilisateur de test
    $stmt = $pdo->prepare('
        INSERT INTO users (name, email, password, role, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ');
    
    if ($stmt->execute([$testName, $testEmail, $hashedPassword, 'user'])) {
        $userId = $pdo->lastInsertId();
        echo "\n✅ Compte de test créé avec succès !\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ID:       " . $userId . "\n";
        echo "Nom:      " . $testName . "\n";
        echo "Email:    " . $testEmail . "\n";
        echo "Password: " . $testPassword . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    } else {
        echo "❌ Erreur lors de la création du compte de test.\n";
        exit(1);
    }
} catch (PDOException $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "\n";
    exit(1);
}
?>
