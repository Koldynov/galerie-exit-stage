<?php include 'includes/header.php'; ?>

<main id="actualites-app" v-cloak>
    <section class="page-header container" style="padding-top: 60px; margin-bottom: 40px;">
        <h1 class="reveal" style="font-size: 4rem; margin-bottom: 10px;">Le Journal<span class="gold">.</span></h1>
        <p class="reveal delay-1" style="color: #bbb; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px;">Actualités, expositions et événements</p>
    </section>

    <section id="journal-section" class="container" style="margin-bottom: 120px;">
        <div class="journal-controls reveal delay-2">
            <div class="filter-chips">
                <button @click="selectedCategory = 'Tous'" :class="['chip', { active: selectedCategory === 'Tous' }]">Tous</button>
                <button v-for="cat in categories" :key="cat" @click="selectedCategory = cat" :class="['chip', { active: selectedCategory === cat }]">{{ cat }}</button>
            </div>
            <div style="position: relative;">
                <input v-model="searchQuery" type="text" placeholder="Rechercher un article..." class="news-search">
                <i class="fas fa-search" style="position: absolute; right: 15px; top: 12px; color: #555; pointer-events: none;"></i>
            </div>
        </div>

        <div v-if="isLoading" class="news-section">
            <article class="news-card featured skeleton-card" style="margin-bottom: 40px; display: flex;">
                <div class="news-image-wrapper skeleton skeleton-image" style="flex: 1; height: 400px; border-radius: 0;"></div>
                <div class="news-content" style="flex: 1; padding: 40px; width: 100%;">
                    <div class="skeleton skeleton-text short" style="margin-bottom: 25px;"></div>
                    <div class="skeleton skeleton-text title" style="height: 35px; width: 80%; margin-bottom: 25px;"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text" style="width: 60%; margin-bottom: 40px;"></div>
                    <div class="skeleton skeleton-text" style="height: 25px; width: 130px; border-radius: 4px;"></div>
                </div>
            </article>

            <div class="news-grid">
                <article v-for="n in 3" :key="n" class="news-card skeleton-card">
                    <div class="news-image-wrapper skeleton skeleton-image" style="height: 250px; border-radius: 0;"></div>
                    <div class="news-content" style="display: flex; flex-direction: column; flex-grow: 1; width: 100%; padding: 25px;">
                        <div class="skeleton skeleton-text short" style="margin-bottom: 15px;"></div>
                        <div class="skeleton skeleton-text title" style="height: 24px; width: 90%; margin-bottom: 15px;"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text" style="width: 70%; margin-bottom: 25px;"></div>
                        <div class="skeleton skeleton-text" style="height: 20px; width: 110px; margin-top: auto;"></div>
                    </div>
                </article>
            </div>
        </div>

        <div v-else class="news-section">
            <div v-if="isDefaultView">
                <div v-if="!featuredArticle && otherArticles.length === 0" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
                    <i class="fas fa-newspaper" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
                    <h2>Aucune actualité</h2>
                </div>

                <article v-if="featuredArticle" class="news-card featured" style="animation: fadeInUp 0.8s ease forwards;">
                    <div class="news-image-wrapper"><img :src="featuredArticle.image_url" :alt="featuredArticle.titre"></div>
                    <div class="news-content">
                        <span class="news-meta">
                            <template v-if="featuredArticle.event_date"><i class="far fa-calendar-alt"></i> {{ formatEventDateRange(featuredArticle.event_date, featuredArticle.event_end_date) }}</template>
                            <template v-else>{{ formatDate(featuredArticle.created_at) }}</template>
                            &nbsp;&bull;&nbsp; {{ featuredArticle.categorie ? featuredArticle.categorie.split(', ').join(' • ') : '' }}
                        </span>                        
                        <h2 class="news-title">{{ featuredArticle.titre }}</h2>
                        <p class="news-excerpt">{{ featuredArticle.description }}</p>
                        <a :href="'/galerie/article/' + featuredArticle.id" class="read-more-btn">Lire l'article</a>
                    </div>
                </article>

                <div v-if="otherArticles.length > 0" class="news-grid" style="animation: fadeInUp 1s ease forwards;">
                    <article v-for="news in otherArticles" :key="news.id" class="news-card">
                        <div class="news-image-wrapper"><img :src="news.image_url" :alt="news.titre"></div>
                        <div class="news-content" style="display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="news-meta">
                                <template v-if="news.event_date"><i class="far fa-calendar-alt"></i> {{ formatEventDateRange(news.event_date, news.event_end_date) }}</template>
                                <template v-else>{{ formatDate(news.created_at) }}</template>
                                &nbsp;&bull;&nbsp; {{ news.categorie ? news.categorie.split(', ').join(' • ') : '' }}
                            </span>
                            <h3 class="news-title" style="font-size: 1.5rem;">{{ news.titre }}</h3>
                            <p class="news-excerpt" style="font-size: 0.95rem;">{{ truncateText(news.description, 120) }}</p>
                            <a :href="'/galerie/article/' + news.id" class="read-more-btn">Lire l'article</a>
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
                        <div class="news-image-wrapper"><img :src="news.image_url" :alt="news.titre"></div>
                        <div class="news-content" style="display: flex; flex-direction: column; flex-grow: 1;">
                            <span class="news-meta">
                                <template v-if="news.event_date"><i class="far fa-calendar-alt"></i> {{ formatEventDateRange(news.event_date, news.event_end_date) }}</template>
                                <template v-else>{{ formatDate(news.created_at) }}</template>
                            </span>
                            <h3 class="news-title" style="font-size: 1.5rem;">{{ news.titre }}</h3>
                            <p class="news-excerpt" style="font-size: 0.95rem;">{{ truncateText(news.description, 120) }}</p>
                            <a :href="'/galerie/article/' + news.id" class="read-more-btn">Lire l'article</a>
                        </div>
                    </article>
                </transition-group>
            </div>
        </div>
    </section>
</main>

<style>
[v-cloak] { display: none; }

@keyframes skeleton-loading {
    0% { background-color: #1a1a1a; }
    50% { background-color: #2a2a2a; }
    100% { background-color: #1a1a1a; }
}
.skeleton {
    animation: skeleton-loading 1.5s infinite ease-in-out;
    border-radius: 4px;
}
.skeleton-card {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 8px;
    overflow: hidden;
}
.skeleton-text {
    height: 12px;
    margin-bottom: 12px;
    width: 100%;
}
.skeleton-text.title {
    height: 24px;
    width: 70%;
}
.skeleton-text.short {
    width: 40%;
}
</style>

<script src="/assets/js/animations.js"></script>
<script src="/assets/js/actualites.js"></script>

<?php include 'includes/footer.php'; ?>