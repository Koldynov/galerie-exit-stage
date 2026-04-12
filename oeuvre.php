<?php include 'includes/header.php'; ?>

<main id="oeuvre-app" v-cloak style="padding-top: 100px; padding-bottom: 100px; min-height: 100vh;">
    
    <div v-if="isLoading" class="empty-state" style="height: 60vh; border: none;">
        <div class="loader"></div>
        <p>Préparation de l'œuvre...</p>
    </div>

    <div v-else-if="errorMessage" class="empty-state" style="height: 60vh; border: none;">
        <i class="fas fa-unlink" style="font-size: 4rem; color: var(--color-gold); margin-bottom: 20px;"></i>
        <h2>Œuvre Introuvable</h2>
        <p>{{ errorMessage }}</p>
        <a href="peintures.php" class="btn-primary" style="margin-top: 20px;">Retour à la galerie</a>
    </div>

    <div v-else-if="oeuvre" class="container">
        
        <div class="oeuvre-layout">
            
            <div class="oeuvre-image-section">
                <div class="image-frame">
                    <img :src="oeuvre.image_url" :alt="oeuvre.titre">
                </div>
            </div>

            <div class="oeuvre-info-section">
                
                <p style="color: #888; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; margin-bottom: 10px;">
                    {{ oeuvre.categorie }}
                </p>
                
                <h1 style="font-size: 3.5rem; margin-bottom: 15px; color: var(--color-white); line-height: 1.1;">{{ oeuvre.titre }}</h1>
                
                <a :href="'profil-artiste.php?id=' + oeuvre.artiste_id" class="artist-link" v-if="oeuvre.artistes">
                    Par <span class="gold">{{ oeuvre.artistes.nom }}</span>
                </a>

                <div class="oeuvre-price" style="margin-top: 30px; margin-bottom: 30px;">
                    <span style="font-size: 2.5rem; color: var(--color-gold); font-weight: bold;">
                        {{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Prix sur demande' }}
                    </span>
                </div>

                <div class="divider"></div>

                <div class="oeuvre-description" style="margin-top: 30px; margin-bottom: 40px;">
                    <h3 style="font-size: 1.2rem; color: var(--color-white); margin-bottom: 15px;">L'Histoire de l'œuvre</h3>
                    <p style="color: #bbb; line-height: 1.8; white-space: pre-wrap; overflow-wrap: break-word;">{{ oeuvre.description || 'L\'artiste n\'a pas fourni de description pour cette œuvre.' }}</p>
                </div>

                <div class="action-buttons" style="display: flex; gap: 20px; align-items: center;">
                    
                    <button class="btn-primary" style="flex: 1; padding: 15px; font-size: 1.1rem;">
                        <i class="fas fa-envelope"></i> Contacter la galerie
                    </button>
                    
                    <button @click="toggleFavori" class="btn-icon" :style="{ color: isFavori ? 'var(--color-red-heart)' : 'var(--color-white)', background: 'rgba(255,255,255,0.05)', width: '55px', height: '55px', borderRadius: '4px', border: '1px solid rgba(255,255,255,0.1)', display: 'flex', justifyContent: 'center', alignItems: 'center' }">
                        <i :class="isFavori ? 'fas fa-heart' : 'far fa-heart'" style="font-size: 1.5rem;"></i>
                    </button>

                </div>

                <div style="margin-top: 40px; padding: 20px; background: rgba(255,255,255,0.02); border-radius: 4px; border: 1px solid rgba(255,255,255,0.05);">
                    <p style="color: #888; font-size: 0.9rem; margin: 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-shield-alt" style="color: var(--color-gold);"></i> Certificat d'authenticité inclus.
                    </p>
                </div>

            </div>
        </div>

    </div>
</main>

<style>
/* Макет сторінки витвору */
.oeuvre-layout {
    display: flex;
    gap: 60px;
    align-items: flex-start;
}

.oeuvre-image-section {
    flex: 1;
    position: sticky;
    top: 120px;
}

.image-frame {
    width: 100%;
    padding: 20px;
    background: #111;
    border: 1px solid rgba(212, 175, 55, 0.2);
    box-shadow: 0 30px 60px rgba(0,0,0,0.6);
}

.image-frame img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
    max-height: 70vh;
}

.oeuvre-info-section {
    flex: 1;
    padding-top: 20px;
}

.artist-link {
    display: inline-block;
    color: #fff;
    text-decoration: none;
    font-size: 1.2rem;
    transition: all 0.3s;
}

.artist-link:hover .gold {
    text-decoration: underline;
}

.divider {
    height: 1px;
    width: 100%;
    background: rgba(255, 255, 255, 0.1);
}

@media (max-width: 992px) {
    .oeuvre-layout {
        flex-direction: column;
    }
    .oeuvre-image-section {
        position: relative;
        top: 0;
        width: 100%;
    }
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

            // Робимо JOIN щоб дістати ім'я артиста
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