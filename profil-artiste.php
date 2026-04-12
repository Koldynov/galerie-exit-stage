<?php include 'includes/header.php'; ?>

<main id="artist-profile-app" v-cloak>
    
    <div v-if="isLoading" class="empty-state" style="height: 100vh; border: none;">
        <div class="loader"></div>
        <p>Ouverture de la galerie...</p>
    </div>

    <div v-else-if="errorMessage" class="empty-state" style="height: 100vh; border: none; animation: fadeInUp 0.6s ease-out forwards;">
        <i class="fas fa-search" style="font-size: 4rem; color: var(--color-gold); margin-bottom: 20px;"></i>
        <h2>Artiste Introuvable</h2>
        <p>{{ errorMessage }}</p>
        <a href="artistes.php" class="btn-primary" style="margin-top: 20px;">Retour à la liste</a>
    </div>

    <div v-else>
        <section class="page-hero" style="height: 85vh; justify-content: center; animation: smoothFadeIn 1s ease-out forwards;">
            <div class="page-hero-content" style="transform: translateY(0); text-align: center;">
                <h1 style="font-size: 5.5rem; letter-spacing: 0.05em; margin-bottom: 10px; animation: smoothFadeIn 0.8s ease-out forwards;">{{ artist.nom }}<span class="gold">.</span></h1>
                
                <p v-if="artist.titre_pro" style="color: var(--color-gold); font-size: 1.2rem; text-transform: uppercase; letter-spacing: 3px; margin-top: 10px; animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0;">
                    {{ artist.titre_pro }}
                </p>
            </div>
            
            <a href="#artist-story" class="scroll-down" style="bottom: 10vh; animation: bounce 2s infinite 1s; opacity: 0;">
                <span>Découvrir l'univers</span>
                <i class="fas fa-chevron-down"></i>
            </a>
        </section>

        <section id="artist-story" class="container" style="padding-top: 40px; padding-bottom: 100px;">
            <div class="elegant-story-grid" style="animation: fadeInUp 1s ease-out 0.4s forwards; opacity: 0;">
                
                <div class="story-image-refined">
                    <img :src="artist.image_url || 'assets/img/default-artist.jpg' || 'https://images.unsplash.com/photo-1554188248-986adbb73be4?auto=format&fit=crop&w=600&q=80'" :alt="artist.nom">
                </div>
                
                <div class="story-text-refined">
                    
                    <blockquote v-if="artist.citation" class="artist-quote">
                        "{{ artist.citation }}"
                    </blockquote>
                    
                    <h2 v-else style="font-size: 2.5rem; margin-bottom: 30px; color: var(--color-white);">L'Essence de l'art.</h2>

                    <p class="bio-paragraph">{{ artist.biographie || 'Cet artiste explore de nouveaux horizons créatifs avec La Galerie EXIT. Ses œuvres témoignent d\'une recherche esthétique profonde.' }}</p>
                </div>

            </div>
        </section>

        <section class="container" style="margin-bottom: 150px;">
            <div class="section-header" style="justify-content: space-between; align-items: center; margin-bottom: 60px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; animation: fadeInUp 1s ease-out 0.6s forwards; opacity: 0;">
                <h2 style="font-size: 2.5rem; color: var(--color-white); margin: 0;">Collection <span class="gold">Privée</span></h2>
                <span style="color: #666; font-size: 1.2rem;">{{ artworks.length }} œuvre(s)</span>
            </div>

            <div v-if="artworks.length === 0" class="empty-state" style="border: 1px dashed rgba(212, 175, 55, 0.2); padding: 80px 20px; animation: fadeInUp 1s ease-out 0.8s forwards; opacity: 0;">
                <i class="fas fa-palette" style="font-size: 3rem; color: #555; margin-bottom: 15px;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 10px;">Aucune œuvre exposée</h3>
                <p style="font-size: 1rem; color: #888;">L'artiste prépare actuellement sa nouvelle collection.</p>
            </div>

            <div v-else class="favoris-grid" style="animation: fadeInUp 1.2s ease-out 0.8s forwards; opacity: 0;">
                <div v-for="oeuvre in artworks" :key="oeuvre.id" class="carousel-card interactive-card" style="width: 100%;">
                    <div class="image-wrapper">
                        <a :href="'oeuvre.php?id=' + oeuvre.id">
                        <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x600?text=Indisponible'" :alt="oeuvre.titre" class="carousel-img">
                        </a>
                    </div>
                    <div class="card-info">
                        <h3>{{ oeuvre.titre }}</h3>
                        <p class="description" style="word-wrap: break-word;">{{ oeuvre.description }}</p>
                        
                        <button class="btn-icon" @click="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : 'var(--color-white)' }">
                            <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<style>
/* Елегантний макет */
.elegant-story-grid {
    display: flex;
    align-items: flex-start;
    gap: 80px;
    background: rgba(255, 255, 255, 0.01);
    padding: 60px;
    border-radius: 8px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.story-image-refined {
    flex: 0 0 400px;
}

.story-image-refined img {
    width: 100%;
    aspect-ratio: 3/4;
    object-fit: cover;
    border-radius: 4px;
    box-shadow: -15px 15px 30px rgba(0,0,0,0.4);
    border: 1px solid rgba(212, 175, 55, 0.3);
}

.story-text-refined {
    flex: 1;
    padding-top: 20px;
}

.artist-quote {
    font-size: 2rem;
    line-height: 1.4;
    color: var(--color-gold);
    font-style: italic;
    font-weight: 300;
    margin-bottom: 40px;
    position: relative;
}

.artist-quote::before {
    content: '"';
    font-size: 5rem;
    color: rgba(212, 175, 55, 0.2);
    position: absolute;
    top: -30px;
    left: -20px;
    font-family: serif;
}

.bio-paragraph {
    font-size: 1.1rem;
    line-height: 1.9;
    color: #bbb;
    white-space: pre-wrap; 
    overflow-wrap: break-word; /* Рятує від довгого тексту */
    word-wrap: break-word;
}

@media (max-width: 992px) {
    .elegant-story-grid {
        flex-direction: column;
        padding: 30px 20px;
        align-items: center;
        text-align: center;
    }
    .story-image-refined {
        flex: 0 0 auto;
        width: 100%;
        max-width: 400px;
        margin-bottom: 30px;
    }
    .artist-quote::before {
        left: 50%;
        transform: translateX(-50%);
    }
    .page-hero h1 {
        font-size: 3.5rem !important;
    }
}

[v-cloak] { display: none; }
</style>

<script src="assets/js/animations.js"></script>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const artist = ref(null);
        const artworks = ref([]);
        const isLoading = ref(true);
        const errorMessage = ref("");
        const currentUser = ref(null);
        const favorisSet = ref(new Set());

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchProfileData = async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const artistId = urlParams.get('id');

            if (!artistId) {
                errorMessage.value = "Aucun artiste spécifié."; isLoading.value = false; return;
            }

            const { data: artistData, error: artistError } = await supabase.from('artistes').select('*').eq('id', artistId).single();
            if (artistError || !artistData) {
                errorMessage.value = "Cet artiste n'existe pas."; isLoading.value = false; return;
            }
            artist.value = artistData;

            const { data: worksData } = await supabase.from('oeuvres').select('*').eq('artiste_id', artistId).order('created_at', { ascending: false });
            if (worksData) artworks.value = worksData;

            isLoading.value = false;
        };

        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            const { data } = await supabase.from('favoris').select('oeuvre_id').eq('user_id', currentUser.value.id);
            if (data) favorisSet.value = new Set(data.map(f => f.oeuvre_id));
        };

        const toggleFavori = async (oeuvreId) => {
            if (!currentUser.value) { alert("Veuillez vous connecter."); window.location.href = 'login.php'; return; }
            if (favorisSet.value.has(oeuvreId)) {
                favorisSet.value.delete(oeuvreId);
                await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
            } else {
                favorisSet.value.add(oeuvreId);
                await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]);
            }
        };

        const isFavori = (id) => favorisSet.value.has(id);

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchFavoris(); }
            await fetchProfileData();
        });

        return { artist, artworks, isLoading, errorMessage, toggleFavori, isFavori };
    }
}).mount('#artist-profile-app');
</script>

<?php include 'includes/footer.php'; ?>