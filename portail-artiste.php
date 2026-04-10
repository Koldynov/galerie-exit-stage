<?php include 'includes/header.php'; ?>

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

        <div v-if="errorMsg" class="error-message" style="animation: fadeInUp 0.3s ease-out;"><i class="fas fa-exclamation-circle"></i> {{ errorMsg }}</div>
        <div v-if="successMsg" class="success-message" style="animation: fadeInUp 0.3s ease-out;"><i class="fas fa-check-circle"></i> {{ successMsg }}</div>

        <div v-if="isLoginMode" class="form-wrapper" style="animation: fadeInUp 0.4s ease-out forwards;">
            <div class="input-group">
                <label>Email professionnel</label>
                <input type="email" v-model="loginForm.email" placeholder="Entrez votre email">
            </div>
            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" v-model="loginForm.password" placeholder="Votre mot de passe">
            </div>
            <button @click="handleLogin" class="btn-primary auth-submit" :disabled="isLoading">
                <span v-if="isLoading"><i class="fas fa-spinner fa-spin"></i> Connexion...</span>
                <span v-else>Se connecter</span>
            </button>
        </div>

        <div v-else class="form-wrapper" style="animation: fadeInUp 0.4s ease-out forwards;">
            <div class="input-group">
                <label>Nom d'artiste</label>
                <input type="text" v-model="registerForm.nom" placeholder="Comment signez-vous vos œuvres ?">
            </div>
            <div class="input-group">
                <label>Email professionnel</label>
                <input type="email" v-model="registerForm.email" placeholder="Pour vous contacter">
            </div>
            <div class="input-group">
                <label>Mot de passe</label>
                <input type="password" v-model="registerForm.password" placeholder="Créez un mot de passe sécurisé">
            </div>
            <div class="input-group">
                <label>Courte Biographie</label>
                <textarea v-model="registerForm.biographie" rows="4" placeholder="Parlez-nous de votre démarche artistique..." style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical;"></textarea>
            </div>
            <button @click="handleRegister" class="btn-primary auth-submit" :disabled="isLoading">
                <span v-if="isLoading"><i class="fas fa-spinner fa-spin"></i> Envoi en cours...</span>
                <span v-else>Soumettre ma candidature</span>
            </button>
        </div>

    </div>
</main>

<style>
/* Стилі для табів (перемикачів) */
.tabs-container {
    display: flex;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.tab-btn {
    flex: 1;
    background: transparent;
    border: none;
    color: #888;
    padding: 15px 0;
    font-size: 1.1rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.tab-btn:hover {
    color: var(--color-white);
}

.tab-btn.active {
    color: var(--color-gold);
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: var(--color-gold);
    box-shadow: 0 -2px 10px rgba(212, 175, 55, 0.5);
}

textarea:focus {
    border-color: var(--color-gold) !important;
    background: rgba(212, 175, 55, 0.05) !important;
}

[v-cloak] { display: none; }
</style>

<script>
const { createApp, ref } = Vue;

createApp({
    setup() {
        const isLoginMode = ref(true);
        const isLoading = ref(false);
        const errorMsg = ref('');
        const successMsg = ref('');

        const loginForm = ref({ email: '', password: '' });
        const registerForm = ref({ nom: '', email: '', password: '', biographie: '' });

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        // ЛОГІКА ВХОДУ
        const handleLogin = async () => {
            errorMsg.value = '';
            successMsg.value = '';
            
            if (!loginForm.value.email || !loginForm.value.password) {
                errorMsg.value = "Veuillez remplir tous les champs.";
                return;
            }

            isLoading.value = true;

            try {
                const { data, error } = await supabase.auth.signInWithPassword({
                    email: loginForm.value.email,
                    password: loginForm.value.password,
                });

                if (error) throw error;

                // Перевіряємо, чи є цей юзер у таблиці artistes
                const { data: artistData, error: artistError } = await supabase
                    .from('artistes')
                    .select('id')
                    .eq('auth_user_id', data.user.id)
                    .single();

                if (artistError || !artistData) {
                    // Якщо він звичайний клієнт, виходимо з акаунту і даємо помилку
                    await supabase.auth.signOut();
                    throw new Error("Ce compte n'est pas enregistré comme artiste.");
                }

                // Якщо все ок, пускаємо в кабінет артиста (ми його створимо наступним)
                window.location.href = 'espace-artiste.php';

            } catch (error) {
                errorMsg.value = error.message;
            } finally {
                isLoading.value = false;
            }
        };

        // ЛОГІКА РЕЄСТРАЦІЇ
        const handleRegister = async () => {
            errorMsg.value = '';
            successMsg.value = '';

            if (!registerForm.value.nom || !registerForm.value.email || !registerForm.value.password) {
                errorMsg.value = "Veuillez remplir les champs obligatoires (Nom, Email, Mot de passe).";
                return;
            }

            isLoading.value = true;

            try {
                // 1. Створюємо юзера в системі авторизації Supabase
                const { data: authData, error: authError } = await supabase.auth.signUp({
                    email: registerForm.value.email,
                    password: registerForm.value.password,
                });

                if (authError) throw authError;

                // 2. Якщо реєстрація успішна, додаємо його в таблицю artistes
                if (authData.user) {
                    const { error: dbError } = await supabase
                        .from('artistes')
                        .insert([{ 
                            auth_user_id: authData.user.id,
                            nom: registerForm.value.nom,
                            biographie: registerForm.value.biographie
                        }]);

                    if (dbError) {
                        // Якщо сталася помилка при записі профілю
                        console.error("Помилка запису профілю:", dbError);
                        throw new Error("Erreur lors de la création du profil artiste.");
                    }
                }

                successMsg.value = "Candidature envoyée avec succès ! Bienvenue.";
                
                // Перекидаємо в кабінет через 2 секунди
                setTimeout(() => {
                    window.location.href = 'espace-artiste.php';
                }, 2000);

            } catch (error) {
                errorMsg.value = error.message;
            } finally {
                isLoading.value = false;
            }
        };

        return { 
            isLoginMode, isLoading, errorMsg, successMsg, 
            loginForm, registerForm, handleLogin, handleRegister 
        };
    }
}).mount('#portail-artiste-app');
</script>

<?php include 'includes/footer.php'; ?>