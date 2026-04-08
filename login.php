<?php include 'includes/header.php'; ?>

<main class="auth-page">
    <div id="login-app" class="auth-container" v-cloak>
        <form @submit.prevent="handleLogin" class="auth-form active">
            <h2>Bon retour</h2>
            <p class="auth-subtitle">Connectez-vous à votre espace collectionneur.</p>

            <div v-if="message.text" :class="['alert', message.type]">
                {{ message.text }}
            </div>

            <div class="input-group">
                <label>Adresse e-mail</label>
                <input type="email" v-model="credentials.email" required :disabled="loading">
            </div>

            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" v-model="credentials.password" required :disabled="loading">
            </div>

            <div class="auth-actions">
                <label class="remember-me">
                    <input type="checkbox"> Se souvenir de moi
                </label>
                <a href="#" class="forgot-password">Mot de passe oublié ?</a>
            </div>

            <button type="submit" class="btn-primary auth-submit" :disabled="loading">
                <span v-if="loading">Connexion...</span>
                <span v-else>Se connecter</span>
            </button>

            <div class="auth-footer">
                <p>Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a></p>
            </div>
        </form>
    </div>
</main>

<script src="assets/js/auth/login-vue.js"></script>

<style>
[v-cloak] { display: none; }
.alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; }
.alert.error { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.3); }
</style>

<?php include 'includes/footer.php'; ?>