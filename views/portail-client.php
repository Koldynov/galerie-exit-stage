<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="assets/css/portal-style.css">

<main class="portal-container">
    <a href="./" class="back-link"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>

    <div id="client-portal" v-cloak class="portal-card" style="max-width: 450px;">
        <div class="portal-header">
            <h1>Espace Client<span class="gold">.</span></h1>
            <p>Gérez votre collection</p>
        </div>

        <div class="portal-tabs">
            <button :class="{ active: currentTab === 'login' }" @click="currentTab = 'login'">Connexion</button>
            <button :class="{ active: currentTab === 'register' }" @click="currentTab = 'register'">Inscription</button>
        </div>

        <div class="portal-body">
            <div v-if="currentTab === 'login'" class="fade-in">
                <form @submit.prevent="handleLogin">
                    <div class="form-group">
                        <label>EMAIL</label>
                        <input v-model="loginForm.email" type="email" class="portal-input" placeholder="ex: jean.dupont@mail.com" required>
                    </div>
                    <div class="form-group">
                        <label>MOT DE PASSE</label>
                        <input v-model="loginForm.password" type="password" class="portal-input" placeholder="Votre mot de passe secret" required>
                    </div>
                    <button type="submit" class="btn-portal gold-bg" :disabled="isLoading">SE CONNECTER</button>
                </form>
            </div>

            <div v-if="currentTab === 'register'" class="fade-in">
                <form @submit.prevent="handleRegister">
                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label>PRÉNOM</label>
                            <input v-model="regForm.first_name" type="text" class="portal-input" placeholder="Jean" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>NOM</label>
                            <input v-model="regForm.last_name" type="text" class="portal-input" placeholder="Dupont" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>TÉLÉPHONE</label>
                        <input v-model="regForm.phone" type="tel" class="portal-input" placeholder="ex: 06 12 34 56 78" required>
                    </div>
                    <div class="form-group">
                        <label>EMAIL</label>
                        <input v-model="regForm.email" type="email" class="portal-input" placeholder="jean.dupont@mail.com" required>
                    </div>
                    <div class="form-group">
                        <label>MOT DE PASSE</label>
                        <input v-model="regForm.password" type="password" class="portal-input" placeholder="6 caractères minimum" required>
                    </div>
                    <button type="submit" class="btn-portal" :disabled="isLoading">CRÉER MON COMPTE</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/portail-client.js"></script>

<?php include 'includes/footer.php'; ?>