<?php
/**
 * Configuration de la base de données
 * Panier - Gestionnaire de Listes de Courses
 */

$host = 'localhost';
$db = 'panier_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la base de données: " . $e->getMessage());
}
?>
