<?php include 'includes/header.php'; ?>

<main id="artistes-page-app" v-cloak>
    
    <section class="page-header container" style="padding-top: 160px; margin-bottom: 40px;">
        <h1 class="reveal" style="font-size: 4rem; margin-bottom: 10px;">Nos Artistes<span class="gold">.</span></h1>
        <p class="reveal delay-1" style="color: #bbb; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px;">
            Les voix créatives qui définissent La Galerie EXIT
        </p>
    </section>

    <section class="container" style="margin-bottom: 120px;">
        
        <div v-if="isLoading" class="empty-state">
            <div class="loader"></div>
            <p>Présentation de nos talents...</p>
        </div>

        <div v-else-if="errorMessage" class="empty-state">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
            <h2>Erreur</h2>
            <p>{{ errorMessage }}</p>
        </div>

        <div v-else class="artistes-grid" style="animation: fadeInUp 0.8s ease-out forwards;">
            
            <div v-for="artiste in artistesList" :key="artiste.id" class="artist-card-full">
                <div class="artist-image-container">
                    <img :src="artiste.image_url || 'assets/img/default-artist.jpg' || 'https://via.placeholder.com/400x400?text=Artist'" 
                         :alt="artiste.nom" class="artist-full-img">
                </div>
                
                <div class="artist-details">
                    <h3>{{ artiste.nom }}</h3>
                    <div class="artist-divider"></div>
                    <p class="artist-bio-short">{{ artiste.biographie || 'Artiste contemporain de la Galerie EXIT.' }}</p>
                    
                    <a :href="'profil-artiste.php?id=' + artiste.id" class="btn-primary" style="padding: 10px 20px; font-size: 0.8rem; margin-top: 15px;">
                        Voir le profil
                    </a>
                </div>
            </div>

        </div>
    </section>
</main>

<style>
/* Сітка для артистів */
.artistes-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 50px 30px;
}

/* Картка артиста */
.artist-card-full {
    text-align: center;
    background: rgba(255, 255, 255, 0.02);
    padding: 40px 20px;
    border: 1px solid rgba(212, 175, 55, 0.1);
    transition: all 0.4s ease;
    border-radius: 4px;
}

.artist-card-full:hover {
    border-color: var(--color-gold);
    background: rgba(212, 175, 55, 0.03);
    transform: translateY(-10px);
}

.artist-image-container {
    width: 200px;
    height: 200px;
    margin: 0 auto 25px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--color-gold);
    padding: 5px;
    background: var(--color-black-wood);
}

.artist-full-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    transition: transform 0.6s ease;
}

.artist-card-full:hover .artist-full-img {
    transform: scale(1.1);
}

.artist-details h3 {
    font-size: 1.8rem;
    color: var(--color-white);
    margin-bottom: 10px;
}

.artist-divider {
    width: 40px;
    height: 2px;
    background: var(--color-gold);
    margin: 0 auto 20px;
}

.artist-bio-short {
    color: #bbb;
    font-size: 0.95rem;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 992px) {
    .artistes-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .artistes-grid {
        grid-template-columns: 1fr;
    }
}

[v-cloak] { display: none; }
</style>

<script src="assets/js/animations.js"></script>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const artistesList = ref([]);
        const isLoading = ref(true);
        const errorMessage = ref("");

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchArtistes = async () => {
            isLoading.value = true;
            const { data, error } = await supabase
                .from('artistes')
                .select('*')
                .order('nom', { ascending: true });

            if (error) {
                errorMessage.value = "Impossible de charger les artistes : " + error.message;
            } else if (data) {
                artistesList.value = data;
            }
            isLoading.value = false;
        };

        onMounted(() => {
            fetchArtistes();
        });

        return { artistesList, isLoading, errorMessage };
    }
}).mount('#artistes-page-app');
</script>

<?php include 'includes/footer.php'; ?>