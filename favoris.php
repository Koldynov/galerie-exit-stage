<?php include 'includes/header.php'; ?>

<main id="favoris-app">
    
    <section class="page-hero">
        <div class="page-hero-content">
            <h1 class="reveal">Collection Privée<span class="gold">.</span></h1>
            <p class="reveal delay-1">Vos œuvres d'art sauvegardées</p>
        </div>
        
        <a href="#collection-grid" class="scroll-down scroll-animate">
            <span>Regarder</span>
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    <section id="collection-grid" class="container" style="margin-bottom: 120px; padding-top: 50px;">
        
        <div v-if="isLoading" class="empty-state">
            <div class="loader"></div>
            <p>Chargement de votre collection...</p>
        </div>

        <div v-else-if="errorMessage" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
            <h2>Erreur de Connexion</h2>
            <p>{{ errorMessage }}</p>
        </div>

        <div v-else-if="!currentUser" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="far fa-user-circle" style="font-size: 4rem; color: var(--color-gold); margin-bottom: 20px;"></i>
            <h2>Accès Restreint</h2>
            <p>Veuillez vous connecter pour voir votre collection privée.</p>
            <a href="login.php" class="btn-primary" style="margin-top: 20px;">Se connecter</a>
        </div>

        <div v-else-if="favorisList.length === 0" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="far fa-heart" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Votre collection est vide</h2>
            <p>Explorez notre galerie et ajoutez des œuvres à votre collection.</p>
            <a href="index.php#oeuvres" class="btn-primary" style="margin-top: 20px;">Découvrir les œuvres</a>
        </div>

        <div v-else class="favoris-grid" style="animation: fadeInUp 0.8s ease-out forwards;">
            <div v-for="item in favorisList" :key="item.oeuvre_id" class="carousel-card interactive-card" style="width: 100%;">
                <div class="image-wrapper">
                    <img :src="item.oeuvres.image_url || 'https://via.placeholder.com/600x600?text=Indisponible'" :alt="item.oeuvres.titre" class="carousel-img">
                </div>
                <div class="card-info">
                    <h3>{{ item.oeuvres.titre }}</h3>
                    <p class="description">{{ item.oeuvres.description }}</p>
                    
                    <button class="btn-icon" @click="removeFavori(item.oeuvre_id)" style="color: var(--color-red-heart);">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>

    </section>
</main>

<script src="assets/js/animations.js"></script>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const favorisList = ref([]);
        const currentUser = ref(null);
        const isLoading = ref(true);
        const errorMessage = ref("");

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            
            const { data, error } = await supabase
                .from('favoris')
                .select(`
                    oeuvre_id,
                    oeuvres ( id, titre, description, image_url )
                `)
                .eq('user_id', currentUser.value.id);
            
            if (error) {
                errorMessage.value = "Помилка бази даних: " + error.message;
                return;
            }

            if (data && data.length > 0) {
                const validFavoris = data.filter(item => item.oeuvres !== null);
                if (validFavoris.length === 0) {
                    errorMessage.value = "Картини збережено, але таблиця oeuvres заблокована (RLS).";
                } else {
                    favorisList.value = validFavoris;
                }
            }
        };

        const removeFavori = async (oeuvreId) => {
            favorisList.value = favorisList.value.filter(item => item.oeuvre_id !== oeuvreId);
            await supabase
                .from('favoris')
                .delete()
                .match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
        };

        onMounted(async () => {
            try {
                const { data: { session }, error } = await supabase.auth.getSession();
                if (error) throw error;
                
                if (session) {
                    currentUser.value = session.user;
                    await fetchFavoris();
                }
            } catch (err) {
                errorMessage.value = "Помилка авторизації: " + err.message;
            } finally {
                isLoading.value = false;
            }
        });

        return { favorisList, currentUser, isLoading, errorMessage, removeFavori };
    }
}).mount('#favoris-app');
</script>

<?php include 'includes/footer.php'; ?>