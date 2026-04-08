<?php include 'includes/header.php'; ?>


<main class="auth-page">
    <div id="register-app" class="auth-container" v-cloak>
        <form @submit.prevent="handleRegister" class="auth-form active">
            <h2>Créer un compte</h2>
            <p class="auth-subtitle">Rejoignez La Galerie EXIT.</p>

            <div v-if="message.text" :class="['alert', message.type]">
                {{ message.text }}
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Prénom</label>
                    <input type="text" v-model="user.firstName" required :disabled="loading">
                </div>
                <div class="input-group">
                    <label>Nom</label>
                    <input type="text" v-model="user.lastName" required :disabled="loading">
                </div>
            </div>

            <div class="input-group">
                <label>Téléphone</label>
                <input type="tel" v-model="user.phone" required :disabled="loading">
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" v-model="user.email" required :disabled="loading">
            </div>

            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" v-model="user.password" required minlength="6" :disabled="loading">
            </div>

            <button type="submit" class="btn-primary auth-submit" :disabled="loading">
                <span v-if="loading">Chargement...</span>
                <span v-else>S'inscrire</span>
            </button>
            
            <div class="auth-footer">
                <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
            </div>
        </form>
    </div>
</main>

<script src="assets/js/auth/register-vue.js"></script>

<style>
[v-cloak] { display: none; }
.alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; }
.alert.error { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid rgba(255, 77, 77, 0.3); }
.alert.success { background: rgba(77, 255, 140, 0.1); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.3); }
</style>

<?php include 'includes/footer.php'; ?>