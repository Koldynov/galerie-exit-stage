<?php include 'includes/header.php'; ?>

<main class="account-page">
    <div id="account-app" class="account-container" v-cloak>
        
        <div v-if="loading" class="loading-state">
            Chargement de votre profil...
        </div>

        <div v-else-if="userProfile" class="profile-card">
            <h2>Mon Compte</h2>
            <p class="auth-subtitle">Gérez vos informations personnelles.</p>

            <div class="profile-info">
                <div class="info-group">
                    <span class="info-label">Prénom:</span>
                    <span class="info-value">{{ userProfile.first_name }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Nom:</span>
                    <span class="info-value">{{ userProfile.last_name }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Téléphone:</span>
                    <span class="info-value">{{ userProfile.phone }}</span>
                </div>
            </div>

            <button @click="handleLogout" class="btn-primary logout-btn">Se déconnecter</button>
        </div>

        <div v-else class="error-state">
            <p>Vous devez être connecté pour voir cette page.</p>
            <a href="login.php" class="btn-primary">Aller à la connexion</a>
        </div>

    </div>
</main>

<script src="assets/js/auth/account-vue.js"></script>

<style>
.account-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    z-index: 1;
    position: relative;
}

.account-container {
    background: rgba(26, 26, 26, 0.85);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 8px;
    padding: 40px;
    width: 100%;
    max-width: 600px;
    color: white;
}

.account-container h2 {
    color: var(--color-gold);
    margin-bottom: 20px;
    text-align: center;
    font-size: 2rem;
}

.profile-info {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.info-group {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.info-group:last-child {
    border-bottom: none;
}

.info-label {
    color: #ccc;
    font-weight: bold;
}

.info-value {
    color: var(--color-gold);
}

.logout-btn {
    background: transparent;
    border: 1px solid #ff4d4d;
    color: #ff4d4d;
    width: 100%;
}

.logout-btn:hover {
    background: rgba(255, 77, 77, 0.1);
}

[v-cloak] { display: none; }
</style>

<?php include 'includes/footer.php'; ?>