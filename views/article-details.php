<?php include 'includes/header.php'; ?>

<main id="article-app" v-cloak style="background: #0a0a0a;">
    <section class="article-hero reveal" :style="{ backgroundImage: 'linear-gradient(to bottom, rgba(0,0,0,0.2), #0a0a0a), url(' + (article?.image_url || '') + ')' }">
        <div class="container hero-content">
            <div class="article-meta reveal delay-1">
                <span class="category-tag">{{ article?.categorie ? article.categorie.split(', ').join(' • ') : 'Journal' }}</span>
                <span class="date-tag">
                    <i class="far fa-calendar-alt"></i> 
                    <template v-if="article?.event_date">{{ formatEventDateRange(article.event_date, article.event_end_date) }}</template>
                    <template v-else>{{ formatDateFull(article?.created_at) }}</template>
                </span>
            </div>
            <h1 class="article-title reveal delay-2">{{ article?.titre }}</h1>
        </div>
    </section>

    <section class="container article-layout" style="padding-bottom: 120px;">
        <div v-if="isLoading" style="text-align: center; padding: 100px;">
            <div class="loader" style="margin: 0 auto;"></div>
        </div>

        <div v-else class="editorial-wrapper">
            <div v-for="(para, index) in articleParagraphs" :key="index">
                <p class="editorial-p reveal" :class="{ 'dropcap': index === 0 }">
                    {{ para }}
                </p>
                
                <div v-if="shouldShowImage(index)" class="inline-gallery-item reveal">
                    <img :src="article.images_secondaires[getImageIndex(index)]" class="journal-inner-img">
                </div>
            </div>
            
            <div class="final-gallery" v-if="remainingImages.length > 0">
                <div v-for="img in remainingImages" :key="img" class="reveal" style="margin-top: 30px;">
                    <img :src="img" class="journal-inner-img">
                </div>
            </div>
        </div>

        <div class="back-footer reveal" style="margin-top: 80px; text-align: center;">
            <a href="/galerie/journal" class="btn-journal-back">← Retour au Journal</a>
        </div>
    </section>
</main>

<style>
/* Журнальна типографіка */
.article-hero { height: 80vh; background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; text-align: center; position: relative; }
.hero-content { z-index: 2; width: 100%; max-width: 1000px; }
.article-meta { display: flex; justify-content: center; gap: 20px; margin-bottom: 25px; align-items: center; }
.category-tag { background: var(--color-gold); color: #000; padding: 6px 18px; font-weight: bold; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
.date-tag { color: #fff; font-size: 0.9rem; letter-spacing: 1px; }
.article-title { font-size: 5rem; color: #fff; line-height: 1.1; font-weight: 300; font-family: 'Playfair Display', serif; }

.editorial-wrapper { max-width: 800px; margin: 0 auto; padding-top: 80px; }
.editorial-p { font-size: 1.25rem; line-height: 1.9; color: #ccc; margin-bottom: 40px; text-align: justify; font-family: 'Lora', serif; }

/* Велика літера */
.dropcap::first-letter { float: left; font-size: 5.5rem; line-height: 0.7; padding-right: 15px; color: var(--color-gold); font-family: 'Playfair Display', serif; }

.journal-inner-img { width: 100%; height: auto; border-radius: 2px; margin: 40px 0; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
.inline-gallery-item { margin: 60px -100px; } /* Виступає за межі тексту для ефекту */

.btn-journal-back { color: var(--color-gold); text-decoration: none; text-transform: uppercase; letter-spacing: 3px; font-size: 0.85rem; border-bottom: 1px solid; padding-bottom: 5px; transition: opacity 0.3s; }
.btn-journal-back:hover { opacity: 0.7; }

@media (max-width: 1000px) {
    .inline-gallery-item { margin: 40px 0; }
    .article-title { font-size: 3rem; }
    .editorial-p { font-size: 1.1rem; text-align: left; }
}
</style>

<script>
const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        const article = ref(null);
        const isLoading = ref(true);

        const fetchArticle = async () => {
            const pathParts = window.location.pathname.split('/');
            const id = pathParts[pathParts.length - 1];
            const { data } = await supabase.from('journal').select('*').eq('id', id).single();
            if (data) {
                if (typeof data.images_secondaires === 'string') data.images_secondaires = JSON.parse(data.images_secondaires);
                article.value = data;
            }
            isLoading.value = false;
        };

        // Розбиття на абзаци (використовуємо contenu, якщо є, інакше description)
        const articleParagraphs = computed(() => {
            if (!article.value?.contenu) return article.value?.description ? [article.value.description] : [];
            return article.value.contenu.split('\n').filter(p => p.trim() !== '');
        });

        const shouldShowImage = (index) => {
            const images = article.value?.images_secondaires || [];
            return images.length > 0 && (index === 1 || index === 4); // Показуємо після 2-го та 5-го абзаців
        };

        const getImageIndex = (index) => index === 1 ? 0 : 1;

        const remainingImages = computed(() => {
            const images = article.value?.images_secondaires || [];
            if (articleParagraphs.value.length > 5) return images.slice(2);
            if (articleParagraphs.value.length > 2) return images.slice(1);
            return images;
        });

        const formatDateFull = (date) => date ? new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
        const formatEventDateRange = (start, end) => {
            if (!start) return '';
            const s = new Date(start).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long' });
            if (!end) return `À partir du ${s}`;
            const e = new Date(end).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
            return `Du ${s} au ${e}`;
        };

        onMounted(() => {
            fetchArticle();
            setTimeout(() => {
                if (typeof initAnimations === 'function') initAnimations();
            }, 500);
        });

        return { article, isLoading, articleParagraphs, shouldShowImage, getImageIndex, remainingImages, formatDateFull, formatEventDateRange };
    }
}).mount('#article-app');
</script>

<script src="/galerie/assets/js/animations.js"></script>
<?php include 'includes/footer.php'; ?>