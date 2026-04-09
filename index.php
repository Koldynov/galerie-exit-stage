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
                <div class="carousel-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?auto=format&fit=crop&w=600&q=80" alt="Abondance Dorée" class="carousel-img">
                    </div>
                    <div class="card-info">
                        <h3>Abondance Dorée</h3>
                        <p class="price">1 200 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                
                <div class="carousel-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=600&q=80" alt="Nuit Cendrée" class="carousel-img">
                    </div>
                    <div class="card-info">
                        <h3>Nuit Cendrée</h3>
                        <p class="price">850 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>

                <div class="carousel-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1574182245536-392562f073c6?auto=format&fit=crop&w=600&q=80" alt="Éclat Rouge" class="carousel-img">
                    </div>
                    <div class="card-info">
                        <h3>Éclat Rouge</h3>
                        <p class="price">2 100 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>

                <div class="carousel-card interactive-card">
                    <div class="image-wrapper">
                        <img src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?auto=format&fit=crop&w=600&q=80" alt="Azur Profond" class="carousel-img">
                    </div>
                    <div class="card-info">
                        <h3>Azur Profond</h3>
                        <p class="price">1 500 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
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

<?php include 'includes/footer.php'; ?>