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
            <p>{{ oeuvre.description || 'L\'artiste n\'a pas fourni de description pour cette œuvre. Elle parle d\'elle-même à travers ses formes et ses couleurs, invitant le spectateur à une interprétation intime et personnelle.' }}</p>
        </div>

        <div class="oeuvre-purchase-footer">
            <span class="oeuvre-final-price">
                {{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Prix sur demande' }}
            </span>
            
            <div class="oeuvre-small-actions">
                <button class="btn-acquire-small">
                    Acquérir
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
</main>

<style>
.main-header {
    transform: translateY(0) !important;
    background-color: rgba(20, 20, 20, 0.98) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}
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

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchOeuvre = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const oeuvreId = urlParams.get('id');

            if (!oeuvreId) {
                errorMessage.value = "Identifiant de l'œuvre manquant.";
                isLoading.value = false; return;
            }

            const { data, error } = await supabase
                .from('oeuvres')
                .select('*, artistes(id, nom)')
                .eq('id', oeuvreId)
                .single();

            if (error || !data) {
                errorMessage.value = "Cette œuvre n'est plus disponible.";
                isLoading.value = false; return;
            }

            oeuvre.value = data;
            
            if (currentUser.value) {
                checkFavoriStatus(oeuvreId);
            }

            isLoading.value = false;
        };

        const checkFavoriStatus = async (oeuvreId) => {
            const { data } = await supabase
                .from('favoris')
                .select('id')
                .eq('user_id', currentUser.value.id)
                .eq('oeuvre_id', oeuvreId)
                .single();
            
            if (data) isFavori.value = true;
        };

        const toggleFavori = async () => {
            if (!currentUser.value) {
                alert("Veuillez vous connecter pour ajouter cette œuvre à vos favoris.");
                window.location.href = 'login.php'; return;
            }

            if (isFavori.value) {
                isFavori.value = false;
                await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvre.value.id });
            } else {
                isFavori.value = true;
                await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvre.value.id }]);
            }
        };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) currentUser.value = session.user;
            await fetchOeuvre();
        });

        return { oeuvre, isLoading, errorMessage, toggleFavori, isFavori };
    }
}).mount('#oeuvre-app');
</script>

<?php include 'includes/footer.php'; ?> 