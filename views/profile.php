<?php
// Profil utilisateur
$pageTitle = 'Profil';

if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté.';
    header('Location: index.php?page=login');
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Utilisateur';
$userEmail = $_SESSION['user_email'] ?? '';
$isAdmin = isAdmin();
?>

<div class="profile">
    <div class="profile-header">
        <h1>👤 Mon Profil</h1>
    </div>

    <div class="profile-card">
        <div class="profile-info">
            <div class="info-item">
                <label>Nom</label>
                <p><?php echo escape($userName); ?></p>
            </div>

            <div class="info-item">
                <label>Email</label>
                <p><?php echo escape($userEmail); ?></p>
            </div>

            <div class="info-item">
                <label>Rôle</label>
                <p>
                    <?php echo $isAdmin ? '⚙️ Administrateur' : '👤 Utilisateur'; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="profile-actions">
        <a href="index.php?page=home" class="btn btn-primary">
            ← Retour à l'accueil
        </a>
    </div>
</div>

<style>
.profile {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.profile-header {
    text-align: center;
    margin-bottom: 1rem;
}

.profile-header h1 {
    font-size: 2rem;
    color: var(--dark);
}

.profile-card {
    background-color: white;
    border-radius: var(--radius);
    padding: 2rem;
    box-shadow: var(--shadow);
    max-width: 500px;
    margin: 0 auto;
    width: 100%;
}

.profile-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item label {
    font-weight: 600;
    color: var(--primary);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item p {
    color: var(--text);
    font-size: 1rem;
    padding: 0.75rem;
    background-color: var(--light);
    border-radius: var(--radius);
}

.profile-actions {
    text-align: center;
}
</style>
