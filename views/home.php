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
                        <a :href="'/galerie/oeuvre/' + oeuvre.id">
                            <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x600?text=Indisponible'" :alt="oeuvre.titre" class="carousel-img" loading="lazy" decoding="async">
                        </a>
                    </div>
                    <div class="card-info" style="position: relative;">
                        <p style="color: #888; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">{{ oeuvre.categorie }}</p>
                        <h3 style="margin-bottom: 5px;">
                            <a :href="'/galerie/oeuvre/' + oeuvre.id" style="color: var(--color-white); text-decoration: none;">{{ oeuvre.titre }}</a>
                        </h3>
                        <p v-if="oeuvre.artistes" style="color: var(--color-gold); font-size: 0.85rem; text-transform: uppercase; margin-bottom: 5px;">Par {{ oeuvre.artistes.nom }}</p>
                        <p style="color: var(--color-white); font-weight: bold; font-size: 1.1rem;">{{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Sur demande' }}</p>
                        
                        <button class="btn-icon" @click="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : 'var(--color-white)', position: 'absolute', right: '0', bottom: '0' }">
                            <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                    </div>
                </div>

                <a href="/galerie/peintures" class="voir-tout-end">
                    <div class="voir-tout-circle">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <span class="voir-tout-text">Voir tout</span>
                </a>
            </div>
        </div>
    </section>

    <section id="artistes" class="carousel-section container" style="margin-bottom: 100px;">
        <div class="section-header reveal">
            <h2>Nos Artistes</h2>
        </div>
        
        <div class="carousel-wrapper reveal delay-1">
            <button class="nav-btn prev" id="prev-artistes"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next" id="next-artistes"><i class="fas fa-chevron-right"></i></button>
            
            <div class="carousel" id="artistes-carousel">
                
                <div v-for="artiste in artistesList" :key="artiste.id" class="carousel-card artist-card interactive-card" style="position: relative;">
                    <div class="image-wrapper">
                        <a :href="'/galerie/artiste/' + artiste.id">
                            <img :src="artiste.image_url || 'https://via.placeholder.com/400x400?text=Artiste'" :alt="artiste.nom" class="artist-img" loading="lazy" decoding="async">
                        </a>
                    </div>
                    <h3 style="margin-top: 15px; margin-bottom: 5px;">{{ artiste.nom }}</h3>
                    <p style="color: var(--color-gold); font-size: 0.9rem;">{{ artiste.titre_pro || 'Artiste de la galerie' }}</p>
                    
                    <a :href="'/galerie/artiste/' + artiste.id" style="position: absolute; top:0; left:0; width:100%; height:100%; z-index:10;"></a>
                </div>

                <a href="/galerie/artistes" class="voir-tout-end">
                    <div class="voir-tout-circle">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <span class="voir-tout-text">Tous les artistes</span>
                </a>
            </div>
        </div>
    </section>
</main>

<style>
[v-cloak] { display: none; }
</style>

<script src="/galerie/assets/js/animations.js"></script>
<script src="/galerie/assets/js/index.js"></script>

<?php include 'includes/footer.php'; ?>