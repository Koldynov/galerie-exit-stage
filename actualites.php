<?php include 'includes/header.php'; ?>

<main id="actualites-app" v-cloak>
    
    <section class="page-header container" style="padding-top: 60px; margin-bottom: 40px;">
        <h1 class="reveal" style="font-size: 4rem; margin-bottom: 10px;">Le Journal<span class="gold">.</span></h1>
        <p class="reveal delay-1" style="color: #bbb; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px;">
            Actualités, expositions et événements
        </p>
    </section>

    <section id="journal-section" class="container" style="margin-bottom: 120px;">

        <div class="journal-controls reveal delay-2">
            <div class="filter-chips">
                <button @click="selectedCategory = 'Tous'" :class="['chip', { active: selectedCategory === 'Tous' }]">Tous</button>
                <button v-for="cat in categories" :key="cat" 
                        @click="selectedCategory = cat" 
                        :class="['chip', { active: selectedCategory === cat }]">
                    {{ cat }}
                </button>
            </div>
            
            <div style="position: relative;">
                <input v-model="searchQuery" type="text" placeholder="Rechercher un article..." class="news-search">
                <i class="fas fa-search" style="position: absolute; right: 15px; top: 12px; color: #555; pointer-events: none;"></i>
            </div>
        </div>

        <div v-if="isLoading" class="empty-state">
            <div class="loader"></div>
            <p>Chargement des actualités...</p>
        </div>

        <div v-else class="news-section">
            
            <div v-if="isDefaultView">
                <div v-if="!featuredArticle && otherArticles.length === 0" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
                    <i class="fas fa-newspaper" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
                    <h2>Aucune actualité</h2>
                    <p>Notre journal est en cours de rédaction. Revenez bientôt !</p>
                </div>

                <article v-if="featuredArticle" class="news-card featured" style="animation: fadeInUp 0.8s ease forwards;">
                    <div class="news-image-wrapper">
                        <img :src="featuredArticle.image_url" :alt="featuredArticle.titre">
                    </div>
                    <div class="news-content">
                    <span class="news-meta">
                        <template v-if="featuredArticle.event_date">
                            <i class="far fa-calendar-alt"></i> {{ formatEventDateRange(featuredArticle.event_date, featuredArticle.event_end_date) }}
                        </template>
                        <template v-else>
                            {{ formatDate(featuredArticle.created_at) }}
                        </template>
                        &nbsp;&bull;&nbsp; {{ featuredArticle.categorie ? featuredArticle.categorie.split(', ').join(' • ') : '' }}
                    </span>                        
                        <h2 class="news-title">{{ featuredArticle.titre }}</h2>
                        <p class="news-excerpt">{{ featuredArticle.description }}</p>
                        <a :href="'article.php?id=' + featuredArticle.id" class="read-more-btn">Lire l'article</a>
                    </div>
                </article>

                <div v-if="otherArticles.length > 0" class="news-grid" style="animation: fadeInUp 1s ease forwards;">
                    <article v-for="news in otherArticles" :key="news.id" class="news-card">
                        <div class="news-image-wrapper">
                            <img :src="news.image_url" :alt="news.titre">
                        </div>
                        <div class="news-content" style="display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="news-meta">
                                <template v-if="news.event_date">
                                    <i class="far fa-calendar-alt"></i> {{ formatEventDateRange(news.event_date, news.event_end_date) }}
                                </template>
                                <template v-else>
                                    {{ formatDate(news.created_at) }}
                                </template>
                                &nbsp;&bull;&nbsp; {{ news.categorie ? news.categorie.split(', ').join(' • ') : '' }}
                            </span>
                            <h3 class="news-title" style="font-size: 1.5rem;">{{ news.titre }}</h3>
                            <p class="news-excerpt" style="font-size: 0.95rem;">{{ truncateText(news.description, 120) }}</p>
                            <a :href="'article.php?id=' + news.id" class="read-more-btn">Lire l'article</a>
                        </div>
                    </article>
                </div>
            </div>

            <div v-else>
                <div v-if="filteredNews.length === 0" class="empty-state">
                    <i class="fas fa-search" style="font-size: 3rem; color: #555; margin-bottom: 20px;"></i>
                    <h2>Aucun article trouvé</h2>
                </div>

                <transition-group name="list" tag="div" class="news-grid">
                    <article v-for="news in filteredNews" :key="news.id" class="news-card">
                        <div class="news-image-wrapper">
                            <img :src="news.image_url" :alt="news.titre">
                        </div>
                        <div class="news-content" style="display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="news-meta">
                                <template v-if="news.event_date">
                                    <i class="far fa-calendar-alt"></i> {{ formatEventDateRange(news.event_date, news.event_end_date) }}
                                </template>
                                <template v-else>
                                    {{ formatDate(news.created_at) }}
                                </template>
                                &nbsp;&bull;&nbsp; {{ news.categorie ? news.categorie.split(', ').join(' • ') : '' }}
                            </span>
                            <h3 class="news-title" style="font-size: 1.5rem;">{{ news.titre }}</h3>
                            <p class="news-excerpt" style="font-size: 0.95rem;">{{ truncateText(news.description, 120) }}</p>
                            <a :href="'article.php?id=' + news.id" class="read-more-btn">Lire l'article</a>
                        </div>
                    </article>
                </transition-group>
            </div>
        </div>
    </section>
</main>

<script src="assets/js/animations.js"></script>

<script>
const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const allNews = ref([]);
        const isLoading = ref(true);
        const searchQuery = ref("");
        const selectedCategory = ref("Tous");
        const categories = ['Actualité', 'Nouvelles', 'Exposition', 'Événement'];

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchNews = async () => {
            const { data } = await supabase.from('journal').select('*').order('is_featured', { ascending: false }).order('created_at', { ascending: false });
            if (data) allNews.value = data;
            isLoading.value = false;
        };

        const isDefaultView = computed(() => selectedCategory.value === 'Tous' && searchQuery.value.trim() === '');

        const featuredArticle = computed(() => {
            if (allNews.value.length === 0) return null;
            const featured = allNews.value.find(a => a.is_featured);
            return featured ? featured : allNews.value[0];
        });

        const otherArticles = computed(() => {
            if (!featuredArticle.value) return [];
            return allNews.value.filter(a => a.id !== featuredArticle.value.id);
        });

        const formatEventDateRange = (startDate, endDate) => {
            const start = new Date(startDate).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
            if (endDate) {
                const end = new Date(endDate).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
                return `Du ${start} au ${end}`;
            }
            return start;
        };

        const filteredNews = computed(() => {
            return allNews.value.filter(news => {
                // ПЕРЕВІРЯЄМО ЧИ РЯДОК З БАЗИ ДАНИХ (наприклад "Actualité, Exposition") ВКЛЮЧАЄ ВИБРАНУ КАТЕГОРІЮ
                const matchesCat = selectedCategory.value === "Tous" || 
                                  (news.categorie && news.categorie.includes(selectedCategory.value));
                
                const matchesSearch = news.titre.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                                    (news.description && news.description.toLowerCase().includes(searchQuery.value.toLowerCase()));
                return matchesCat && matchesSearch;
            });
        });

        const formatDate = (date) => new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
        const truncateText = (t, l) => t && t.length > l ? t.substring(0, l) + "..." : t;

        onMounted(fetchNews);

        return { 
            isLoading, searchQuery, selectedCategory, categories, isDefaultView, 
            featuredArticle, otherArticles, filteredNews, formatDate, formatEventDateRange, truncateText 
        };
    }
}).mount('#actualites-app');
</script>

<?php include 'includes/footer.php'; ?>