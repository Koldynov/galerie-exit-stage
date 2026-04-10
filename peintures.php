<?php include 'includes/header.php'; ?>

<main id="peintures-app" v-cloak>
    
    <section class="page-hero" style="height: 60vh;">
        <div class="page-hero-content">
            <h1 class="reveal">Peintures<span class="gold">.</span></h1>
            <p class="reveal delay-1">Découvrez notre collection de toiles contemporaines</p>
        </div>
    </section>

    <section class="container" style="margin-bottom: 120px; padding-top: 50px;">
        
        <div v-if="isLoading" class="empty-state">
            <div class="loader"></div>
            <p>Chargement de la collection...</p>
        </div>

        <div v-else-if="errorMessage" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
            <h2>Erreur de Connexion</h2>
            <p>{{ errorMessage }}</p>
        </div>

        <div v-else-if="peinturesList.length === 0" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-palette" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Aucune peinture disponible</h2>
            <p>Notre collection est en cours de renouvellement.</p>
        </div>

        <div v-else class="favoris-grid" style="animation: fadeInUp 0.8s ease-out forwards;">
            
            <div v-for="oeuvre in peinturesList" :key="oeuvre.id" class="carousel-card interactive-card" style="width: 100%;">
                <div class="image-wrapper">
                    <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x600?text=Indisponible'" :alt="oeuvre.titre" class="carousel-img">
                </div>
                <div class="card-info">
                    <h3>{{ oeuvre.titre }}</h3>
                    
                    <p v-if="oeuvre.artistes" style="color: var(--color-gold); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; font-weight: bold;">
                        Par {{ oeuvre.artistes.nom }}
                    </p>
                    
                    <p class="description">{{ oeuvre.description }}</p>
                    
                    <button class="btn-icon" @click="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : 'var(--color-white)' }">
                        <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                    </button>
                </div>
            </div>

        </div>
    </section>
</main>

<style>
[v-cloak] { display: none; }
</style>

<script src="assets/js/animations.js"></script>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const peinturesList = ref([]);
        const favorisSet = ref(new Set());
        const currentUser = ref(null);
        const isLoading = ref(true);
        const errorMessage = ref("");

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        // 1. Завантажуємо ТІЛЬКИ картини (categorie = 'Peinture')
        const fetchPeintures = async () => {
            const { data, error } = await supabase
                .from('oeuvres')
                .select(`
                    id, 
                    titre, 
                    description, 
                    image_url, 
                    categorie,
                    artistes ( nom )
                `)
                .eq('categorie', 'Peinture') // ОСЬ ТУТ ФІЛЬТР! Тільки картини.
                .order('created_at', { ascending: false });

            if (error) {
                errorMessage.value = "Erreur de chargement: " + error.message;
            } else if (data) {
                peinturesList.value = data;
            }
        };

        // 2. Завантажуємо фаворити юзера (щоб сердечка були зафарбовані)
        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            const { data } = await supabase
                .from('favoris')
                .select('oeuvre_id')
                .eq('user_id', currentUser.value.id);
            
            if (data) {
                favorisSet.value = new Set(data.map(f => f.oeuvre_id));
            }
        };

        // 3. Логіка кліку на сердечко
        const toggleFavori = async (oeuvreId) => {
            if (!currentUser.value) {
                alert("Veuillez vous connecter pour ajouter cette œuvre à vos favoris.");
                window.location.href = 'login.php';
                return;
            }

            if (favorisSet.value.has(oeuvreId)) {
                favorisSet.value.delete(oeuvreId);
                const { error } = await supabase
                    .from('favoris')
                    .delete()
                    .match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
                
                if (error) {
                    alert("Erreur: " + error.message);
                    favorisSet.value.add(oeuvreId); 
                }
            } else {
                favorisSet.value.add(oeuvreId);
                const { error } = await supabase
                    .from('favoris')
                    .insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]);
                
                if (error) {
                    alert("Erreur: " + error.message);
                    favorisSet.value.delete(oeuvreId); 
                }
            }
        };

        const isFavori = (id) => favorisSet.value.has(id);

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                currentUser.value = session.user;
                await fetchFavoris();
            }
            await fetchPeintures();
            isLoading.value = false;
        });

        return { peinturesList, toggleFavori, isFavori, isLoading, errorMessage };
    }
}).mount('#peintures-app');
</script>

<?php include 'includes/footer.php'; ?>