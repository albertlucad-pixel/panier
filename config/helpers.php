<?php
/**
 * HELPERS - Fonctions utilitaires réutilisables dans les vues
 */

/**
 * Échapper HTML (prévention XSS)
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Formater devise (€)
 */
function formatCurrency($amount, $decimals = 2) {
    return '€' . number_format($amount, $decimals, ',', ' ');
}

/**
 * Vérifier si connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Obtenir l'ID utilisateur connecté
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtenir le rôle utilisateur
 */
function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Vérifier si admin
 */
function isAdmin() {
    return getUserRole() === 'admin';
}

/**
 * Obtenir le nom utilisateur
 */
function getUserName() {
    return $_SESSION['user_name'] ?? 'Utilisateur';
}

/**
 * Afficher message d'erreur
 */
function displayError($message) {
    return '<div class="alert alert-error">' . escape($message) . '</div>';
}

/**
 * Afficher message de succès
 */
function displaySuccess($message) {
    return '<div class="alert alert-success">' . escape($message) . '</div>';
}

/**
 * Afficher message d'information
 */
function displayInfo($message) {
    return '<div class="alert alert-info">' . escape($message) . '</div>';
}

/**
 * Afficher tableau d'erreurs
 */
function displayErrors($errors) {
    if (empty($errors)) {
        return '';
    }
    
    $html = '<div class="alert alert-error"><ul>';
    foreach ($errors as $error) {
        $html .= '<li>' . escape($error) . '</li>';
    }
    $html .= '</ul></div>';
    return $html;
}

/**
 * Créer URL avec paramètres
 */
function createUrl($page, $params = []) {
    $url = 'index.php?page=' . urlencode($page);
    foreach ($params as $key => $value) {
        $url .= '&' . urlencode($key) . '=' . urlencode($value);
    }
    return $url;
}

/**
 * Badge Nutri-Score avec couleur
 */
function getNutriScoreBadge($score) {
    $colors = [
        'A' => '#15a857', // Vert
        'B' => '#8cc63f', // Vert clair
        'C' => '#ffd700', // Jaune
        'D' => '#ff8c00', // Orange
        'E' => '#e74c3c'  // Rouge
    ];
    
    $color = $colors[$score] ?? '#888';
    return '<span class="badge" style="background-color: ' . $color . ';">' . escape($score) . '</span>';
}

/**
 * Badge Bio
 */
function getBioBadge($isBio) {
    if ($isBio) {
        return '<span class="badge badge-bio">🌱 Bio</span>';
    }
    return '';
}

/**
 * Format date en français
 */
function formatDateFR($date) {
    $months = [
        'January' => 'janvier', 'February' => 'février', 'March' => 'mars',
        'April' => 'avril', 'May' => 'mai', 'June' => 'juin',
        'July' => 'juillet', 'August' => 'août', 'September' => 'septembre',
        'October' => 'octobre', 'November' => 'novembre', 'December' => 'décembre'
    ];
    
    $dateTime = new DateTime($date);
    $day = $dateTime->format('d');
    $month = $months[$dateTime->format('F')] ?? $dateTime->format('m');
    $year = $dateTime->format('Y');
    
    return $day . ' ' . $month . ' ' . $year;
}

/**
 * Obtenir nombre de jours écoulés
 */
function getTimeAgo($date) {
    $dateTime = new DateTime($date);
    $now = new DateTime();
    $interval = $now->diff($dateTime);
    
    if ($interval->y > 0) return 'il y a ' . $interval->y . ' an(s)';
    if ($interval->m > 0) return 'il y a ' . $interval->m . ' mois';
    if ($interval->d > 0) return 'il y a ' . $interval->d . ' jour(s)';
    if ($interval->h > 0) return 'il y a ' . $interval->h . ' heure(s)';
    if ($interval->i > 0) return 'il y a ' . $interval->i . ' minute(s)';
    return 'à l\'instant';
}
?>
