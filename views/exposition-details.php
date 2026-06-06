<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="/assets/css/exposition.css?v=<?php echo time(); ?>">

<main id="exposition-app" v-cloak>
    
    <div v-if="isLoading" style="height: 100vh; display: flex; align-items: center; justify-content: center; background: #0a0a0a;">
        <div class="loader"></div>
    </div>

    <div v-else-if="exposition">
        
        <section class="expo-hero">
            <div class="expo-hero-bg" :style="{ backgroundImage: 'url(' + exposition.image_url + ')' }"></div>
            <div class="expo-hero-overlay"></div>
            <div class="expo-hero-content vue-reveal">
                <div style="color: var(--color-gold); letter-spacing: 3px; text-transform: uppercase; margin-bottom: 15px; font-size: 0.9rem; font-weight: 600;">
                    Exposition Exclusive
                </div>
                <h1 class="expo-title">{{ exposition.titre }}</h1>
            </div>
        </section>

        <section class="expo-description-section">
            <div class="expo-description-text vue-reveal">
                <p style="white-space: pre-wrap;">{{ exposition.description }}</p>
                <div style="width: 2px; height: 50px; background: var(--color-gold); margin: 40px auto 0 auto;"></div>
            </div>
        </section>

        <section class="expo-gallery">
            <div v-if="oeuvres.length === 0" style="text-align: center; color: #888; padding: 60px;">
                <i class="fas fa-image" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Aucune œuvre n'est encore associée à cette exposition.</p>
            </div>
            
            <div class="expo-grid">
                <div v-for="(oeuvre, index) in oeuvres" :key="oeuvre.id" class="expo-artwork vue-reveal" :style="{ transitionDelay: (index % 3 * 0.15) + 's' }">
                    <a :href="'/oeuvre/' + oeuvre.id" style="text-decoration: none;">
                        <div class="expo-artwork-img-wrapper">
                            <img :src="oeuvre.image_url || 'https://via.placeholder.com/400x500'" :alt="oeuvre.titre" loading="lazy">
                        </div>
                        <div class="expo-artwork-info">
                            <h3 class="expo-artwork-title">{{ oeuvre.titre }}</h3>
                            <p class="expo-artwork-artist">{{ oeuvre.artistes?.nom }}</p>
                            <p class="expo-artwork-price">{{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Sur demande' }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>
    
    <div v-else style="height: 70vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: white;">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Exposition introuvable</h2>
        <p style="color: #888; margin-bottom: 30px;">Cette exposition n'existe plus ou a été retirée.</p>
        <a href="/" class="btn-primary">Retour à l'accueil</a>
    </div>

</main>

<script>
const { createApp, ref, onMounted, nextTick } = Vue;

createApp({
    setup() {
        // Отримуємо ID виставки з URL (через PHP)
        const expoId = <?php echo json_encode($id ?? null); ?>;
        
        const exposition = ref(null);
        const oeuvres = ref([]);
        const isLoading = ref(true);
        
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const fetchExpositionData = async () => {
            if (!expoId) {
                isLoading.value = false;
                return;
            }

            try {
                // 1. Отримуємо інформацію про саму виставку
                const { data: expoData, error: expoError } = await supabase
                    .from('expositions')
                    .select('*')
                    .eq('id', expoId)
                    .single();
                    
                if (expoError) throw expoError;
                exposition.value = expoData;

                // 2. Отримуємо ID картин, прив'язаних до цієї виставки
                const { data: linkData, error: linkError } = await supabase
                    .from('exposition_oeuvres')
                    .select('oeuvre_id')
                    .eq('exposition_id', expoId);
                    
                if (linkError) throw linkError;

                // 3. Якщо є картини, витягуємо їхні деталі
                if (linkData && linkData.length > 0) {
                    const oeuvreIds = linkData.map(link => link.oeuvre_id);
                    
                    const { data: oeuvresData, error: oeuvresError } = await supabase
                        .from('oeuvres')
                        .select('*, artistes(nom)')
                        .in('id', oeuvreIds)
                        .neq('statut', 'vendu'); // Не показуємо вже продані картини
                        
                    if (oeuvresError) throw oeuvresError;
                    oeuvres.value = oeuvresData;
                }
            } catch (error) {
                console.error("Помилка завантаження виставки:", error);
            } finally {
                isLoading.value = false;
                
                // Запускаємо плавну анімацію появи елементів при скролі
                nextTick(() => {
                    setTimeout(() => {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    entry.target.classList.add('is-visible');
                                } else {
                                    entry.target.classList.remove('is-visible');
                                }
                            });
                        }, { threshold: 0.1 }); 

                        document.querySelectorAll('.vue-reveal').forEach(el => observer.observe(el));
                    }, 300); // Даємо час на рендер Vue
                });
            }
        };

        onMounted(() => {
            fetchExpositionData();
        });

        return { exposition, oeuvres, isLoading };
    }
}).mount('#exposition-app');
</script>

<?php include 'includes/footer.php'; ?>