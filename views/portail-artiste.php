<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="/galerie/galerie/assets/css/portail-artiste-style.css">

<main class="auth-page" style="min-height: 100vh; padding-top: 120px;" id="portail-artiste-app" v-cloak>
    <div style="position: absolute; top: 20%; left: 10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 70%); z-index: -1;"></div>
    <div style="position: absolute; bottom: 10%; right: 10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(69, 123, 157, 0.05) 0%, transparent 70%); z-index: -1;"></div>

    <div class="auth-container reveal active" style="max-width: 600px;">
        <h2 style="font-size: 2.5rem; margin-bottom: 10px;">Espace Artiste<span class="gold">.</span></h2>
        <p class="auth-subtitle">Rejoignez La Galerie EXIT et partagez votre vision avec le monde.</p>

        <div class="tabs-container">
            <button :class="['tab-btn', { active: isLoginMode }]" @click="isLoginMode = true">Connexion</button>
            <button :class="['tab-btn', { active: !isLoginMode }]" @click="isLoginMode = false">Postuler</button>
        </div>

        <div v-if="errorMsg" class="error-message"><i class="fas fa-exclamation-circle"></i> {{ errorMsg }}</div>
        <div v-if="successMsg" class="success-message"><i class="fas fa-check-circle"></i> {{ successMsg }}</div>

        <div v-if="isLoginMode" class="form-wrapper">
            <div class="input-group"><label>Email professionnel</label><input type="email" v-model="loginForm.email"></div>
            <div class="input-group"><label>Mot de passe</label><input type="password" v-model="loginForm.password"></div>
            <button @click="handleLogin" class="btn-primary auth-submit" :disabled="isLoading">
                <span v-if="isLoading">Connexion...</span><span v-else>Se connecter</span>
            </button>
        </div>

        <div v-else class="form-wrapper">
            <div class="input-group"><label>Nom d'artiste</label><input type="text" v-model="registerForm.nom"></div>
            <div class="input-group"><label>Email professionnel</label><input type="email" v-model="registerForm.email"></div>
            <div class="input-group"><label>Mot de passe</label><input type="password" v-model="registerForm.password"></div>
            <div class="input-group"><label>Courte Biographie</label><textarea v-model="registerForm.biographie" rows="4" class="custom-textarea"></textarea></div>
            <button @click="handleRegister" class="btn-primary auth-submit" :disabled="isLoading">
                <span v-if="isLoading">Envoi en cours...</span><span v-else>Soumettre ma candidature</span>
            </button>
        </div>
    </div>
</main>

<script src="/galerie/assets/js/portail-artiste.js"></script>

<?php include 'includes/footer.php'; ?>