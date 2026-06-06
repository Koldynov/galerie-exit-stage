<?php include 'includes/header.php'; ?>

<main id="home-app" v-cloak>
    
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

    <section id="exposition" class="container" v-if="activeExposition">
        <div class="editorial-grid">
            <div class="vue-reveal">
                <div class="editorial-label">
                    <div class="editorial-label-line"></div>
                    <span class="editorial-label-text">Exposition du moment</span>
                </div>
                <h2 class="editorial-title">{{ activeExposition.titre }}</h2>
                
                <p class="editorial-desc" style="display: -webkit-box; -webkit-line-clamp: 5; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                    {{ activeExposition.description }}
                </p>
                
                <div style="margin-top: 40px;">
                    <a :href="'/exposition/' + activeExposition.id" class="btn-editorial">Voir la collection</a>
                </div>
            </div>
            
            <div class="editorial-img-container vue-reveal vue-delay">
                <div class="editorial-img-frame-right"></div>
                <img :src="activeExposition.image_url" :alt="activeExposition.titre" class="editorial-img">
            </div>
        </div>
    </section>

    <section id="philosophie" class="container" v-if="homepageSettings">
        <div class="editorial-grid">
            <div class="vue-reveal">
                <h2 style="color: var(--color-gold); font-size: 2.2rem; font-weight: 300; margin-bottom: 25px;">
                    {{ homepageSettings.philo_title }}
                </h2>
                <p class="editorial-desc" style="margin-bottom: 15px; color: #ddd;">
                    {{ homepageSettings.philo_text1 }}
                </p>
                <p class="editorial-desc">
                    {{ homepageSettings.philo_text2 }}
                </p>
            </div>
            
            <div class="editorial-img-container vue-reveal vue-delay">
                <div class="editorial-img-frame-right"></div>
                <img :src="homepageSettings.philo_media" alt="Philosophie" class="editorial-img">
            </div>
        </div>
    </section>

    <section v-if="featuredEvenement" class="container">
        <div class="editorial-grid">
            <div class="editorial-img-container editorial-img-reverse vue-reveal vue-delay">
                <div class="editorial-img-frame-left"></div>
                <img :src="featuredEvenement.image_url || 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622'" alt="Événement" class="editorial-img">
            </div>
            <div class="editorial-text-reverse vue-reveal">
                <div class="editorial-label">
                    <div class="editorial-label-line"></div>
                    <span class="editorial-label-text">Événement Spécial</span>
                </div>
                <h2 class="editorial-title">{{ featuredEvenement.titre }}</h2>
                <p style="color: var(--color-gold); font-size: 1rem; margin-bottom: 15px; font-weight: 500;">
                    <i class="far fa-calendar-alt"></i> {{ new Date(featuredEvenement.date_atelier).toLocaleDateString('fr-FR', {day: 'numeric', month: 'long', hour: '2-digit', minute:'2-digit'}) }}
                </p>
                <p class="editorial-desc">{{ featuredEvenement.description }}</p>
                <a :href="'/evenement/' + featuredEvenement.id" class="btn-editorial">Participer</a>
            </div>
        </div>
    </section>

    <section v-if="featuredAtelier" class="container">
        <div class="editorial-grid">
            <div class="vue-reveal">
                <div class="editorial-label">
                    <div class="editorial-label-line"></div>
                    <span class="editorial-label-text">Prochain Atelier</span>
                </div>
                <h2 class="editorial-title">{{ featuredAtelier.titre }}</h2>
                <p style="color: var(--color-gold); font-size: 1rem; margin-bottom: 15px; font-weight: 500;">
                    <i class="far fa-calendar-alt"></i> {{ new Date(featuredAtelier.date_atelier).toLocaleDateString('fr-FR', {day: 'numeric', month: 'long', hour: '2-digit', minute:'2-digit'}) }}
                </p>
                <p class="editorial-desc">{{ featuredAtelier.description }}</p>
                <a :href="'/atelier/' + featuredAtelier.id" class="btn-editorial">Réserver ma place</a>
            </div>
            <div class="editorial-img-container vue-reveal vue-delay">
                <div class="editorial-img-frame-right"></div>
                <img :src="featuredAtelier.image_url || 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b'" alt="Atelier" class="editorial-img">
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
                <div v-for="artiste in artistesList" :key="artiste.id" class="carousel-card artist-card interactive-card" style="position: relative;">
                    <div class="image-wrapper">
                        <a :href="'/artiste/' + artiste.id">
                            <img :src="artiste.image_url || 'https://via.placeholder.com/400x400?text=Artiste'" :alt="artiste.nom" class="artist-img" loading="lazy" decoding="async">
                        </a>
                    </div>
                    <h3 style="margin-top: 15px; margin-bottom: 5px;">{{ artiste.nom }}</h3>
                    <p style="color: var(--color-gold); font-size: 0.9rem;">{{ artiste.titre_pro || 'Artiste de la galerie' }}</p>
                    <a :href="'/artiste/' + artiste.id" style="position: absolute; top:0; left:0; width:100%; height:100%; z-index:10;"></a>
                </div>
                <a href="/artistes" class="voir-tout-end">
                    <div class="voir-tout-circle">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <span class="voir-tout-text">Tous les artistes</span>
                </a>
            </div>
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
                        <a :href="'/oeuvre/' + oeuvre.id">
                            <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x600?text=Indisponible'" :alt="oeuvre.titre" class="carousel-img" loading="lazy" decoding="async">
                        </a>
                    </div>
                    <div class="card-info" style="position: relative;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <p style="color: #888; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin: 0;">{{ oeuvre.categorie }}</p>
        
        <button class="btn-icon" @click.prevent="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : 'var(--color-white)', margin: '0', padding: '0', background: 'transparent', border: 'none', cursor: 'pointer', fontSize: '1.2rem' }">
            <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
        </button>
    </div>
    
    <h3 style="margin-bottom: 5px;">
        <a :href="'/oeuvre/' + oeuvre.id" style="color: var(--color-white); text-decoration: none;">{{ oeuvre.titre }}</a>
    </h3>
    <p v-if="oeuvre.artistes" style="color: var(--color-gold); font-size: 0.85rem; text-transform: uppercase; margin-bottom: 5px;">Par {{ oeuvre.artistes.nom }}</p>
    <p style="color: var(--color-white); font-weight: bold; font-size: 1.1rem; margin-bottom: 0;">{{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Sur demande' }}</p>
    
</div>
                </div>
                <a href="/peintures" class="voir-tout-end">
                    <div class="voir-tout-circle">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <span class="voir-tout-text">Voir tout</span>
                </a>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="editorial-grid">
            <div class="vue-reveal">
                <div class="editorial-label">
                    <div class="editorial-label-line"></div>
                    <span class="editorial-label-text">Collaboration Artistique</span>
                </div>
                <h2 class="editorial-title">Résidence d'Artistes EXIT</h2>
                <p class="editorial-desc" style="margin-bottom: 25px;">Un espace de création collaborative où les artistes de différents horizons fusionnent leurs talents. Créez des projets uniques main dans la main sous la direction artistique de la galerie.</p>
                
                <a href="/residence-artistes" class="btn-editorial">Découvrir le programme</a>
                
            </div>
            <div class="editorial-img-container vue-reveal vue-delay">
                <div class="editorial-img-frame-right"></div>
                <img src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=800&q=80" alt="Résidence" class="editorial-img">
            </div>
        </div>
    </section>

    <section v-if="latestPost" class="container">
        <div class="editorial-grid">
            <div class="editorial-img-container editorial-img-reverse vue-reveal vue-delay">
                <div class="editorial-img-frame-left"></div>
                <img :src="latestPost.image_url" alt="Blog" class="editorial-img">
            </div>
            <div class="editorial-text-reverse vue-reveal">
                <div class="editorial-label">
                    <div class="editorial-label-line"></div>
                    <span class="editorial-label-text">Le Blog de la Galerie</span>
                </div>
                <h2 class="editorial-title">{{ latestPost.titre }}</h2>
                <p class="editorial-desc">{{ latestPost.description }}</p>
                
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a :href="'/article/' + latestPost.id" class="btn-editorial">Lire l'article</a>
                    <a href="/journal" class="btn-editorial" style="background: rgba(212,175,55,0.05);">Voir tout le blog</a>
                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="editorial-grid">
            <div class="editorial-text-reverse vue-reveal">
                <div class="editorial-label">
                    <div class="editorial-label-line"></div>
                    <span class="editorial-label-text">Rejoindre la Galerie</span>
                </div>
                <h2 class="editorial-title">Devenez un artiste EXIT</h2>
                <p class="editorial-desc">Vous êtes artiste peintre, sculpteur ou photographe ? Présentez vos créations à notre comité de sélection et intégrez une communauté vibrante dédiée à l'art contemporain.</p>
                <a href="/portail-artiste" class="btn-editorial">Soumettre mon portfolio</a>
            </div>
            <div class="editorial-img-container editorial-img-reverse vue-reveal vue-delay">
                <div class="editorial-img-frame-left"></div>
                <img src="https://images.unsplash.com/photo-1536924940846-227afb31e2a5?auto=format&fit=crop&w=800&q=80" alt="Devenir Artiste" class="editorial-img">
            </div>
        </div>
    </section>

</main>

<style>
[v-cloak] { display: none; }

/* Зменшення відстаней між секціями на головній */
#home-app section.container, 
#home-app section.carousel-section {
    margin-top: 60px !important;
    margin-bottom: 60px !important;
}

/* На мобілках робимо ще компактніше */
@media (max-width: 768px) {
    #home-app section.container, 
    #home-app section.carousel-section {
        margin-top: 40px !important;
        margin-bottom: 40px !important;
    }
}
</style>

<script src="/assets/js/animations.js"></script>
<script src="/assets/js/index.js"></script>

<?php include 'includes/footer.php'; ?>