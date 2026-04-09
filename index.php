<?php include 'includes/header.php'; ?>

<main>
    <section class="hero-minimalist">
        <div class="hero-minimalist-content">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 115 40" class="hero-logo-svg hero-animate">
                <g class="hero-power-e" stroke="var(--color-gold)" stroke-width="5" stroke-linecap="round" fill="none">
                    <path d="M 28,10 A 14,14 0 1,0 28,30" />
                    <line x1="21" y1="20" x2="36" y2="20" />
                </g>
                <text x="40" y="32" font-family="'Helvetica Neue', Arial, sans-serif" font-weight="900" font-size="34" fill="var(--color-white)">xit</text>
            </svg>
        </div>
        
        <a href="#exposition" class="scroll-down scroll-animate">
            <span>Découvrir</span>
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    <section id="exposition" class="exposition-section container">
        <div class="expo-grid">
            <div class="expo-text reveal">
                <div class="hero-badge">Exposition du moment</div>
                <h2>L'art comme moyen de connexion.</h2>
                <p>Entrez dans l'univers de La Galerie EXIT. Découvrez notre sélection d'œuvres contemporaines et soutenez la créativité humaine.</p>
                <div style="margin-top: 40px;">
                    <a href="#oeuvres" class="btn-primary">Voir la collection</a>
                </div>
            </div>
            
            <div class="expo-image reveal delay-1">
                <img src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?auto=format&fit=crop&w=1000&q=80" alt="Exposition principale">
                <div class="image-accent"></div>
            </div>
        </div>
    </section>

    <section id="philosophie" class="philosophy container">
        <div class="philosophy-text">
            <h2 class="reveal">Notre Philosophie Artistique</h2>
            <p class="reveal delay-1">À la Galerie EXIT, nous croyons en l'importance de l'art comme moyen de connexion et d'expression humaine.</p>
            <p class="reveal delay-2">Notre philosophie repose sur l'ouverture à toutes les formes de créativité. Nous accompagnons nos artistes pour repousser les limites de l'art.</p>
        </div>
        <div class="philosophy-image reveal delay-3">
            <img src="https://images.unsplash.com/photo-1554188248-986adbb73be4?auto=format&fit=crop&w=800&q=80" alt="Philosophie de la galerie">
            <div class="image-accent"></div>
        </div>
    </section>

    <section id="oeuvres" class="carousel-section container">
        <div class="section-header reveal">
            <h2>Œuvres à la une</h2>
        </div>
        
        <div class="carousel-wrapper reveal delay-1">
            <button class="nav-btn prev" id="prev-oeuvres"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next" id="next-oeuvres"><i class="fas fa-chevron-right"></i></button>
            
            <div class="carousel" id="oeuvres-carousel">
                
                <div v-for="oeuvre in oeuvresList" :key="oeuvre.id" class="carousel-card interactive-card">
                    <div class="image-wrapper">
                        <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x600?text=Image+Indisponible'" :alt="oeuvre.titre" class="carousel-img">
                    </div>
                    <div class="card-info">
                    <h3>{{ oeuvre.titre }}</h3>
                    <p class="description">{{ oeuvre.description }}</p>
                    
                    <button class="btn-icon" @click="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : 'var(--color-white)' }">
                        <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                    </button>
                </div>
                </div>

                <div class="carousel-card voir-tout-card interactive-card">
                    <a href="peintures.php" class="voir-tout-link">
                        <i class="fas fa-arrow-right"></i>
                        <span>Voir toutes les œuvres</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="artistes" class="carousel-section container">
        <div class="section-header reveal">
            <h2>Nos Artistes</h2>
        </div>
        
        <div class="carousel-wrapper reveal delay-1">
            <button class="nav-btn prev" id="prev-artistes"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next" id="next-artistes"><i class="fas fa-chevron-right"></i></button>
            
            <div class="carousel" id="artistes-carousel">
                <div class="carousel-card artist-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80" alt="Julie Martin" class="artist-img">
                    </div>
                    <h3>Julie Martin</h3>
                    <p>Peinture contemporaine</p>
                </div>
                
                <div class="carousel-card artist-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=400&q=80" alt="Lucas Leroy" class="artist-img">
                    </div>
                    <h3>Lucas Leroy</h3>
                    <p>Sculpture Bronze</p>
                </div>

                <div class="carousel-card artist-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80" alt="Sophie Petit" class="artist-img">
                    </div>
                    <h3>Sophie Petit</h3>
                    <p>Photographie</p>
                </div>

                <div class="carousel-card artist-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80" alt="Jean Dupont" class="artist-img">
                    </div>
                    <h3>Jean Dupont</h3>
                    <p>Art Brut</p>
                </div>

                <div class="carousel-card voir-tout-card interactive-card">
                    <a href="artistes.php" class="voir-tout-link">
                        <i class="fas fa-arrow-right"></i>
                        <span>Voir tous les artistes</span>
                    </a>
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
        const oeuvresList = ref([]);
        const favorisSet = ref(new Set());
        const currentUser = ref(null);

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchOeuvres = async () => {
    const { data, error } = await supabase.from('oeuvres').select('id, titre, description, image_url').limit(6);
    if (data) oeuvresList.value = data;
};

        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            const { data, error } = await supabase
                .from('favoris')
                .select('oeuvre_id')
                .eq('user_id', currentUser.value.id);
            
            if (data) {
                favorisSet.value = new Set(data.map(f => f.oeuvre_id));
            }
        };

        // ОНОВЛЕНА ЛОГІКА З ПЕРЕВІРКОЮ ПОМИЛОК
        const toggleFavori = async (oeuvreId) => {
            if (!currentUser.value) {
                alert("Veuillez vous connecter pour ajouter cette œuvre à vos favoris.");
                window.location.href = 'login.php';
                return;
            }

            if (favorisSet.value.has(oeuvreId)) {
                // Візуально відразу знімаємо сердечко
                favorisSet.value.delete(oeuvreId);
                
                // Відправляємо запит на видалення з бази
                const { error } = await supabase
                    .from('favoris')
                    .delete()
                    .match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
                
                // ЯКЩО БАЗА ДАЛА ПОМИЛКУ:
                if (error) {
                    alert("Помилка видалення: " + error.message);
                    favorisSet.value.add(oeuvreId); // Повертаємо сердечко назад
                }
            } else {
                // Візуально відразу ставимо сердечко
                favorisSet.value.add(oeuvreId);
                
                // Відправляємо запит на збереження в базу
                const { error } = await supabase
                    .from('favoris')
                    .insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]);
                
                // ЯКЩО БАЗА ДАЛА ПОМИЛКУ (наприклад RLS блокує):
                if (error) {
                    alert("Помилка бази даних: " + error.message);
                    console.error("Деталі помилки:", error);
                    favorisSet.value.delete(oeuvreId); // Знімаємо сердечко назад
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
            await fetchOeuvres();
        });

        return { oeuvresList, toggleFavori, isFavori };
    }
}).mount('#oeuvres');
</script>   

<?php include 'includes/footer.php'; ?>