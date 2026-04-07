<?php include 'includes/header.php'; ?>

<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Entrez dans l'univers de La Galerie EXIT</h1>
            <p>L'art comme moyen de connexion et d'expression humaine.</p>
            <a href="#oeuvres" class="btn-primary">Découvrir les œuvres</a>
        </div>
    </section>

    <section id="philosophie" class="philosophy container">
        <div class="philosophy-text">
            <h2>Notre Philosophie Artistique</h2>
            <p>À la Galerie EXIT, nous croyons en l'importance de l'art comme moyen de connexion et d'expression humaine.</p>
            <p>Notre philosophie repose sur l'ouverture à toutes les formes de créativité. Nous accompagnons nos artistes pour repousser les limites de l'art.</p>
        </div>
        <div class="philosophy-image">
            <div class="image-placeholder"></div>
        </div>
    </section>

    <section id="oeuvres" class="carousel-section container">
        <div class="section-header">
            <h2>Œuvres à la une</h2>
            <a href="#all-oeuvres" class="see-more">Voir tout</a>
        </div>
        <div class="carousel">
            <div class="carousel-card">
                <div class="card-img-placeholder"></div>
                <div class="card-info">
                    <h3>Abondance Dorée</h3>
                    <p class="price">1 200 €</p>
                    <button class="btn-icon"><i class="fas fa-heart"></i></button>
                    <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                </div>
            </div>
            <div class="carousel-card">
                <div class="card-img-placeholder"></div>
                <div class="card-info">
                    <h3>Nuit Cendrée</h3>
                    <p class="price">850 €</p>
                    <button class="btn-icon"><i class="fas fa-heart"></i></button>
                    <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                </div>
            </div>
            <div class="carousel-card">
                <div class="card-img-placeholder"></div>
                <div class="card-info">
                    <h3>Éclat Rouge</h3>
                    <p class="price">2 100 €</p>
                    <button class="btn-icon"><i class="fas fa-heart"></i></button>
                    <button class="btn-icon"><i class="fas fa-shopping-cart"></i></button>
                </div>
            </div>
        </div>
    </section>

    <section id="artistes" class="carousel-section container">
        <div class="section-header">
            <h2>Nos Artistes</h2>
        </div>
        <div class="carousel">
            <div class="carousel-card artist-card">
                <div class="artist-img-placeholder"></div>
                <h3>Jean Dupont</h3>
                <p>Peinture contemporaine</p>
            </div>
            <div class="carousel-card artist-card">
                <div class="artist-img-placeholder"></div>
                <h3>Marie Martin</h3>
                <p>Sculpture</p>
            </div>
            <div class="carousel-card artist-card">
                <div class="artist-img-placeholder"></div>
                <h3>Lucas Leroy</h3>
                <p>Photographie</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>