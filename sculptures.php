<?php include 'includes/header.php'; ?>

<main id="sculptures-app" v-cloak>
    
    <section class="page-header container" style="padding-top: 160px; margin-bottom: 40px;">
        <h1 class="reveal" style="font-size: 4rem; margin-bottom: 10px;">Sculptures<span class="gold">.</span></h1>
        <p class="reveal delay-1" style="color: #bbb; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px;">
            Découvrez notre collection de sculptures contemporaines
        </p>
    </section>

    <section id="gallery-section" class="container" style="margin-bottom: 120px;">
        
        <div v-if="isLoading" class="empty-state">
            <div class="loader"></div>
            <p>Chargement de la collection...</p>
        </div>

        <div v-else-if="errorMessage" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
            <h2>Erreur de Connexion</h2>
            <p>{{ errorMessage }}</p>
        </div>

        <div v-else-if="sculpturesList.length === 0" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-cube" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Aucune sculpture disponible</h2>
            <p>Notre collection est en cours de renouvellement.</p>
        </div>

        <div v-else class="gallery-grid" style="animation: fadeInUp 0.8s ease-out forwards;">
            
            <div v-for="oeuvre in sculpturesList" :key="oeuvre.id" class="gallery-card">
                
                <div class="gallery-image-wrapper">
                    <a :href="'oeuvre.php?id=' + oeuvre.id">
                        <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x800?text=Indisponible'" :alt="oeuvre.titre" class="gallery-img">
                    </a>
                </div>
                
                <div class="gallery-card-info">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3>
                                <a :href="'oeuvre.php?id=' + oeuvre.id" style="color: var(--color-white); text-decoration: none; transition: color 0.3s;">
                                    {{ oeuvre.titre }}
                                </a>
                            </h3>
                            
                            <p v-if="oeuvre.artistes" class="artist-name">
                                Par {{ oeuvre.artistes.nom }}
                            </p>
                        </div>
                        
                        <button class="btn-icon" @click="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : '#666', marginTop: '-5px' }">
                            <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                    </div>

                    <p class="oeuvre-price">
                        {{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Sur demande' }}
                    </p>
                    
                    <p class="gallery-description">{{ oeuvre.description }}</p>
                </div>
            </div>

        </div>
    </section>
</main>

<style>
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 50px 30px;
}

.gallery-card {
    background: transparent;
    transition: transform 0.4s ease;
}

.gallery-card:hover {
    transform: translateY(-10px);
}

.gallery-card:hover h3 a {
    color: var(--color-gold) !important;
}

.gallery-image-wrapper {
    width: 100%;
    aspect-ratio: 3 / 4; 
    overflow: hidden;
    border-radius: 4px;
    background: #111;
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    margin-bottom: 20px;
}

.gallery-img {
    width: 100%;
    height: 100%;
    object-fit: cover; 
    transition: transform 0.8s ease;
}

.gallery-card:hover .gallery-img {
    transform: scale(1.08); 
}

.gallery-card-info h3 {
    font-size: 1.5rem;
    margin-bottom: 5px;
    line-height: 1.2;
}

.artist-name {
    color: var(--color-gold);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
}

.oeuvre-price {
    font-size: 1.3rem;
    font-weight: bold;
    color: var(--color-white);
    margin-bottom: 10px;
}

.gallery-description {
    color: #888;
    font-size: 0.95rem;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

[v-cloak] { display: none; }
</style>

<script src="assets/js/animations.js"></script>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const sculpturesList = ref([]);
        const favorisSet = ref(new Set());
        const currentUser = ref(null);
        const isLoading = ref(true);
        const errorMessage = ref("");

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchSculptures = async () => {
            const { data, error } = await supabase
                .from('oeuvres')
                .select(`
                    id, 
                    titre, 
                    description, 
                    image_url, 
                    categorie,
                    prix,
                    artistes ( nom )
                `)
                .eq('categorie', 'Sculpture') 
                .order('created_at', { ascending: false });

            if (error) {
                errorMessage.value = "Erreur de chargement: " + error.message;
            } else if (data) {
                sculpturesList.value = data;
            }
        };

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
            await fetchSculptures();
            isLoading.value = false;
        });

        return { sculpturesList, toggleFavori, isFavori, isLoading, errorMessage };
    }
}).mount('#sculptures-app');
</script>

<?php include 'includes/footer.php'; ?>