<?php include 'includes/header.php'; ?>

<main>
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">Exposition du moment</div>
                <h1>L'art comme moyen de connexion.</h1>
                <p>Entrez dans l'univers de La Galerie EXIT. Découvrez notre sélection d'œuvres contemporaines et soutenez la créativité humaine.</p>
                <div class="hero-actions">
                    <a href="#oeuvres" class="btn-primary">Découvrir les œuvres</a>
                    <a href="#artistes" class="btn-secondary">Nos artistes</a>
                </div>
            </div>
            
            <div class="hero-visual">
                <img src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?auto=format&fit=crop&w=1000&q=80" alt="Exposition principale">
                <div class="visual-accent"></div>
            </div>
        </div>
    </section>

    <section id="philosophie" class="philosophy container">
        <div class="philosophy-text">
            <h2>Notre Philosophie Artistique</h2>
            <p>À la Galerie EXIT, nous croyons en l'importance de l'art comme moyen de connexion et d'expression humaine.</p>
            <p>Notre philosophie repose sur l'ouverture à toutes les formes de créativité. Nous accompagnons nos artistes pour repousser les limites de l'art.</p>
        </div>
        <div class="philosophy-image">
            <img src="https://images.unsplash.com/photo-1554188248-986adbb73be4?auto=format&fit=crop&w=800&q=80" alt="Philosophie de la galerie">
            <div class="image-accent"></div>
        </div>
    </section>

    <section id="oeuvres" class="carousel-section container">
        <div class="section-header">
            <h2>Œuvres à la une</h2>
        </div>
        
        <div class="carousel-wrapper">
            <button class="nav-btn prev" id="prev-oeuvres"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next" id="next-oeuvres"><i class="fas fa-chevron-right"></i></button>
            
            <div class="carousel" id="oeuvres-carousel">
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?auto=format&fit=crop&w=600&q=80" alt="Abondance Dorée" class="carousel-img">
                    <div class="card-info">
                        <h3>Abondance Dorée</h3>
                        <p class="price">1 200 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1513364776144-60967b0f800f?auto=format&fit=crop&w=600&q=80" alt="Nuit Cendrée" class="carousel-img">
                    <div class="card-info">
                        <h3>Nuit Cendrée</h3>
                        <p class="price">850 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1574182245536-392562f073c6?auto=format&fit=crop&w=600&q=80" alt="Éclat Rouge" class="carousel-img">
                    <div class="card-info">
                        <h3>Éclat Rouge</h3>
                        <p class="price">2 100 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?auto=format&fit=crop&w=600&q=80" alt="Azur Profond" class="carousel-img">
                    <div class="card-info">
                        <h3>Azur Profond</h3>
                        <p class="price">1 500 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1544413660-299165566b1d?auto=format&fit=crop&w=600&q=80" alt="Lumière Dorée" class="carousel-img">
                    <div class="card-info">
                        <h3>Lumière Dorée</h3>
                        <p class="price">3 000 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1580136608260-4ebf15facdaf?auto=format&fit=crop&w=600&q=80" alt="Reflet Argenté" class="carousel-img">
                    <div class="card-info">
                        <h3>Reflet Argenté</h3>
                        <p class="price">950 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1549887552-cb1071d3e5ca?auto=format&fit=crop&w=600&q=80" alt="Horizon Brumeux" class="carousel-img">
                    <div class="card-info">
                        <h3>Horizon Brumeux</h3>
                        <p class="price">1 800 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="carousel-card">
                    <img src="https://images.unsplash.com/photo-1578301978693-85fa9c026b47?auto=format&fit=crop&w=600&q=80" alt="Contraste Urbain" class="carousel-img">
                    <div class="card-info">
                        <h3>Contraste Urbain</h3>
                        <p class="price">2 500 €</p>
                        <button class="btn-icon"><i class="fas fa-heart"></i></button>
                        <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                
                <div class="carousel-card voir-tout-card">
                    <a href="peintures.php" class="voir-tout-link">
                        <i class="fas fa-arrow-right"></i>
                        <span>Voir toutes les œuvres</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="artistes" class="carousel-section container">
        <div class="section-header">
            <h2>Nos Artistes</h2>
        </div>
        
        <div class="carousel-wrapper">
            <button class="nav-btn prev" id="prev-artistes"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next" id="next-artistes"><i class="fas fa-chevron-right"></i></button>
            
            <div class="carousel" id="artistes-carousel">
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80" alt="Julie Martin" class="artist-img">
                    <h3>Julie Martin</h3>
                    <p>Peinture contemporaine</p>
                </div>
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=400&q=80" alt="Lucas Leroy" class="artist-img">
                    <h3>Lucas Leroy</h3>
                    <p>Sculpture Bronze</p>
                </div>
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=80" alt="Sophie Petit" class="artist-img">
                    <h3>Sophie Petit</h3>
                    <p>Photographie</p>
                </div>
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80" alt="Jean Dupont" class="artist-img">
                    <h3>Jean Dupont</h3>
                    <p>Art Brut</p>
                </div>
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?auto=format&fit=crop&w=400&q=80" alt="Marie Dubois" class="artist-img">
                    <h3>Marie Dubois</h3>
                    <p>Art numérique</p>
                </div>
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=400&q=80" alt="Emma Laurent" class="artist-img">
                    <h3>Emma Laurent</h3>
                    <p>Street Art</p>
                </div>
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80" alt="Thomas Roux" class="artist-img">
                    <h3>Thomas Roux</h3>
                    <p>Aquarelle</p>
                </div>
                <div class="carousel-card artist-card">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=400&q=80" alt="Alice Moreau" class="artist-img">
                    <h3>Alice Moreau</h3>
                    <p>Gravure</p>
                </div>

                <div class="carousel-card voir-tout-card">
                    <a href="artistes.php" class="voir-tout-link">
                        <i class="fas fa-arrow-right"></i>
                        <span>Voir tous les artistes</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>