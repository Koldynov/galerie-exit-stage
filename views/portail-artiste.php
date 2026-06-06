<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="assets/css/portal-style.css">

<main class="portal-container">
    <a href="./" class="back-link"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>

    <div id="artiste-portal" v-cloak class="portal-card" style="max-width: 650px;">
        
        <div class="portal-header">
            <h1>Espace Artiste<span class="gold">.</span></h1>
            <p>Rejoignez nos créateurs résidents</p>
        </div>

        <div class="portal-tabs">
            <button :class="{ active: currentTab === 'login' }" @click="currentTab = 'login'">Connexion</button>
            <button :class="{ active: currentTab === 'apply' }" @click="currentTab = 'apply'">Postuler</button>
        </div>

        <div class="portal-body">
            <div v-if="currentTab === 'login'" class="fade-in">
                <form @submit.prevent="handleLogin">
                    <div class="form-group">
                        <label>EMAIL PROFESSIONNEL</label>
                        <input v-model="loginForm.email" type="email" class="portal-input" placeholder="ex: contact@votre-art.com" required>
                    </div>
                    <div class="form-group">
                        <label>MOT DE PASSE</label>
                        <input v-model="loginForm.password" type="password" class="portal-input" placeholder="Votre mot de passe" required>
                    </div>
                    <button type="submit" class="btn-portal gold-bg" :disabled="isLoading">ACCÉDER À MON STUDIO</button>
                    <p v-if="errorMessage" class="portal-error" style="color: #ff4d4d; margin-top: 15px; text-align: center;">{{ errorMessage }}</p>
                </form>
            </div>

            <div v-if="currentTab === 'apply'" class="fade-in">
                <form @submit.prevent="handleApply">
                    <div class="form-group">
                        <label>PHOTO DE PROFIL</label>
                        <input type="file" @change="handleFileUpload" accept="image/*" class="portal-input" style="padding: 9px;" required>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label>NOM D'ARTISTE</label>
                            <input v-model="regForm.nom" type="text" class="portal-input" placeholder="Votre pseudonyme" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>SPÉCIALITÉ</label>
                            <input v-model="regForm.titre_pro" type="text" class="portal-input" placeholder="ex: Peintre Abstrait, Sculpteur" required>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label>SITE WEB (Optionnel)</label>
                            <input v-model="regForm.website" type="url" class="portal-input" placeholder="https://votre-portfolio.com">
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>INSTAGRAM (Optionnel)</label>
                            <input v-model="regForm.instagram" type="text" class="portal-input" placeholder="@votre_pseudo">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>CITATION</label>
                        <input v-model="regForm.citation" type="text" class="portal-input" placeholder="Une courte phrase qui définit votre vision artistique...">
                    </div>
                    <div class="form-group">
                        <label>BIOGRAPHIE</label>
                        <textarea v-model="regForm.biographie" class="portal-input" placeholder="Parlez-nous de votre parcours, vos inspirations et vos techniques..." style="height: 100px; resize: none;"></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label>EMAIL DE CONNEXION</label>
                            <input v-model="regForm.email" type="email" class="portal-input" placeholder="Email pour vous connecter" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>MOT DE PASSE</label>
                            <!-- ОНОВЛЕНИЙ ПЛЕЙСХОЛДЕР -->
                            <input v-model="regForm.password" type="password" class="portal-input" placeholder="Min. 8 caractères dont 1 majuscule" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-portal" :disabled="isLoading">
                        <span v-if="isLoading">Envoi en cours...</span>
                        <span v-else>SOUMETTRE MA CANDIDATURE</span>
                    </button>
                    
                    <p v-if="errorMessage" class="portal-error" style="color: #ff4d4d; margin-top: 15px; text-align: center;">{{ errorMessage }}</p>
                    <p v-if="successMessage" class="portal-success" style="color: #d4af37; margin-top: 15px; text-align: center;">{{ successMessage }}</p>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/portail-artiste.js"></script>

<?php include 'includes/footer.php'; ?>