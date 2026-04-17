<?php include 'includes/header.php'; ?>

<main id="oeuvre-app" v-cloak style="padding-top: 120px; padding-bottom: 100px; min-height: 100vh;">
    
    <div v-if="isLoading" class="empty-state" style="height: 60vh; border: none;">
        <div class="loader"></div>
        <p>Préparation de l'œuvre...</p>
    </div>

    <div v-else-if="errorMessage" class="empty-state" style="height: 60vh; border: none; animation: fadeInUp 0.5s forwards;">
        <i class="fas fa-unlink" style="font-size: 4rem; color: var(--color-gold); margin-bottom: 20px;"></i>
        <h2>Œuvre Introuvable</h2>
        <p>{{ errorMessage }}</p>
        <a href="peintures.php" class="btn-primary" style="margin-top: 20px;">Retour à la galerie</a>
    </div>

    <div v-else-if="oeuvre" class="oeuvre-editorial" style="animation: fadeInUp 0.8s ease-out forwards;">
        
        <a href="javascript:history.back()" class="back-link" style="margin-bottom: 30px; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="oeuvre-header-center">
            <span class="oeuvre-cat-label">{{ oeuvre.categorie }}</span>
            <h1 class="oeuvre-title-huge">{{ oeuvre.titre }}</h1>
            
            <a :href="'profil-artiste.php?id=' + oeuvre.artiste_id" class="oeuvre-artist-link" v-if="oeuvre.artistes">
                Une œuvre de <span class="gold">{{ oeuvre.artistes.nom }}</span>
            </a>
        </div>

        <div class="oeuvre-hero-img-container">
            <img :src="oeuvre.image_url" :alt="oeuvre.titre">
        </div>

        <div class="oeuvre-story-center">
            <p>{{ oeuvre.description || 'L\'artiste n\'a pas fourni de description pour cette œuvre.' }}</p>
        </div>

        <div class="oeuvre-purchase-footer">
            <span class="oeuvre-final-price">
                {{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Prix sur demande' }}
            </span>
            
            <div class="oeuvre-small-actions">
                <button @click="handleAcquisition" class="btn-acquire-small">
                    {{ oeuvre.prix > 0 ? 'Acheter maintenant' : 'Demander le prix' }}
                </button>
                
                <button @click="toggleFavori" class="btn-fav-small" :style="{ color: isFavori ? 'var(--color-red-heart)' : '' }" title="Ajouter aux favoris">
                    <i :class="isFavori ? 'fas fa-heart' : 'far fa-heart'"></i>
                </button>
            </div>
            
            <p style="color: #555; font-size: 0.7rem; margin-top: 20px; text-transform: uppercase; letter-spacing: 1px;">
                Certificat d'authenticité inclus
            </p>
        </div>
    </div>

    <div v-if="showAcquireModal" class="inquiry-modal-overlay" @click.self="closeModal">
        <div class="inquiry-modal-content">
            <div v-if="!isSuccess">
                <button @click="closeModal" class="close-modal-btn"><i class="fas fa-times"></i></button>
                <h2 style="font-size: 1.8rem; margin-bottom: 10px; color: white;">Demande d'acquisition</h2>
                
                <div v-if="currentUser" style="margin-bottom: 25px;">
                    <p style="color: #888; font-size: 0.95rem; line-height: 1.5;">
                        Vous êtes connecté en tant que <strong style="color: var(--color-gold);">{{ currentUser.email }}</strong>.<br>
                        Votre demande pour <strong style="color: white;">"{{ oeuvre.titre }}"</strong> sera transmise à la galerie.
                    </p>
                </div>
                <div v-else>
                    <p style="color: #888; font-size: 0.9rem; margin-bottom: 25px;">Laissez-nous vos coordonnées pour cette œuvre.</p>
                    <div class="form-group">
                        <label>Nom et Prénom *</label>
                        <input v-model="inquiryForm.nom" type="text" class="inquiry-input" placeholder="Votre nom">
                    </div>
                    <div class="form-group">
                        <label>Adresse Email *</label>
                        <input v-model="inquiryForm.email" type="email" class="inquiry-input" placeholder="votre@email.com">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label>Message (Optionnel)</label>
                    <textarea v-model="inquiryForm.message" rows="4" class="inquiry-input" placeholder="Précisez votre demande..."></textarea>
                </div>

                <button @click="submitInquiry" class="btn-primary" style="width: 100%; padding: 15px;" :disabled="isSubmitting">
                    <span v-if="isSubmitting"><i class="fas fa-spinner fa-spin"></i> Envoi...</span>
                    <span v-else>Envoyer la demande</span>
                </button>
                <p v-if="formError" style="color: var(--color-red-heart); font-size: 0.85rem; margin-top: 15px; text-align: center;">{{ formError }}</p>
            </div>

            <div v-else style="text-align: center; padding: 30px 0;">
                <i class="far fa-check-circle" style="font-size: 5rem; color: var(--color-gold); margin-bottom: 20px;"></i>
                <h2 style="color: white; margin-bottom: 15px;">Demande envoyée !</h2>
                <p style="color: #bbb; line-height: 1.6; margin-bottom: 30px;">Nous vous contacterons très prochainement.</p>
                <button @click="closeModal" class="btn-primary">Fermer</button>
            </div>
        </div>
    </div>

</main>

<style>
.main-header {
    transform: translateY(0) !important;
    background-color: rgba(20, 20, 20, 0.98) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.inquiry-modal-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px); z-index: 10000;
    display: block; overflow-y: auto; padding: 120px 20px 60px 20px; box-sizing: border-box;
}

.inquiry-modal-content {
    background: #111; border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 8px;
    padding: 40px; width: 100%; max-width: 500px; margin: 0 auto; position: relative;
    box-shadow: 0 30px 60px rgba(0,0,0,0.9); animation: slideUpModal 0.4s ease-out forwards;
}

.close-modal-btn { position: absolute; top: 20px; right: 20px; background: none; border: none; color: #888; font-size: 1.2rem; cursor: pointer; }
.form-group { margin-bottom: 20px; text-align: left; }
.form-group label { display: block; color: #888; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; }
.inquiry-input { width: 100%; padding: 14px 15px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 4px; box-sizing: border-box; }
.inquiry-input:focus { border-color: var(--color-gold); outline: none; }

@keyframes fadeInOverlay { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUpModal { from { transform: translateY(40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
[v-cloak] { display: none; }
</style>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const oeuvre = ref(null);
        const isLoading = ref(true);
        const errorMessage = ref("");
        const currentUser = ref(null);
        const isFavori = ref(false);

        const showAcquireModal = ref(false);
        const inquiryForm = ref({ nom: '', email: '', message: '' });
        const isSubmitting = ref(false);
        const isSuccess = ref(false);
        const formError = ref("");

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchOeuvre = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get('id');
            if (!id) { errorMessage.value = "Identifiant manquant."; isLoading.value = false; return; }

            const { data, error } = await supabase.from('oeuvres').select('*, artistes(id, nom)').eq('id', id).single();
            if (error || !data) {
                errorMessage.value = "Œuvre introuvable.";
            } else {
                oeuvre.value = data;
                if (currentUser.value) checkFavoriStatus(id);
            }
            isLoading.value = false;
        };

        // ПОВЕРНУТО: Функція перевірки статусу сердечка
        const checkFavoriStatus = async (oeuvreId) => {
            const { data } = await supabase.from('favoris').select('id').eq('user_id', currentUser.value.id).eq('oeuvre_id', oeuvreId).single();
            if (data) isFavori.value = true;
        };

        // ПОВЕРНУТО: Функція кліку по сердечку
        const toggleFavori = async () => {
            if (!currentUser.value) { window.location.href = 'login.php'; return; }
            if (isFavori.value) {
                isFavori.value = false;
                await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvre.value.id });
            } else {
                isFavori.value = true;
                await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvre.value.id }]);
            }
        };

        const handleAcquisition = () => {
            if (oeuvre.value.prix > 0) {
                window.location.href = `paiement.php?id=${oeuvre.value.id}`;
            } else {
                showAcquireModal.value = true;
            }
        };

        const submitInquiry = async () => {
            formError.value = "";
            let email = currentUser.value ? currentUser.value.email : inquiryForm.value.email;
            let nom = currentUser.value ? 'Membre Connecté' : inquiryForm.value.nom;

            if (!email) { formError.value = "Email requis."; return; }
            isSubmitting.value = true;
            const { error } = await supabase.from('demandes').insert([{
                oeuvre_id: oeuvre.value.id, nom, email, message: inquiryForm.value.message
            }]);
            isSubmitting.value = false;
            if (error) formError.value = "Erreur."; else isSuccess.value = true;
        };

        const closeModal = () => { showAcquireModal.value = false; };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) currentUser.value = session.user;
            await fetchOeuvre();
        });

        return { 
            oeuvre, isLoading, errorMessage, handleAcquisition, toggleFavori, isFavori, 
            showAcquireModal, inquiryForm, isSubmitting, isSuccess, formError, submitInquiry, closeModal, currentUser 
        };
    }
}).mount('#oeuvre-app');
</script>

<?php include 'includes/footer.php'; ?>