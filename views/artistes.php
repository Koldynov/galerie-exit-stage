<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="assets/css/artistes-style.css">

<main id="artistes-page-app" v-cloak>
    
    <section class="page-header container" style="padding-top: 60px; margin-bottom: 40px;">
        <h1 class="reveal" style="font-size: 4rem; margin-bottom: 10px;">Nos Artistes<span class="gold">.</span></h1>
        <p class="reveal delay-1" style="color: #bbb; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px;">
            Les voix créatives qui définissent La Galerie EXIT
        </p>
    </section>

    <section class="container" style="margin-bottom: 120px;">
        
        <div v-if="isLoading" class="artistes-grid">
            <div v-for="n in 4" :key="n" class="artist-card-full skeleton-card" style="display: flex;">
                <div class="artist-image-container" style="flex: 1; height: 400px;">
                    <div class="skeleton skeleton-image" style="height: 100%; border-radius: 0;"></div>
                </div>
                <div class="artist-details" style="flex: 1; padding: 40px;">
                    <div class="skeleton skeleton-text title" style="height: 32px; width: 60%; margin-bottom: 20px;"></div>
                    <div class="artist-divider" style="background: rgba(255,255,255,0.05); margin-bottom: 20px;"></div>
                    <div class="skeleton skeleton-text"></div>
                    <div class="skeleton skeleton-text" style="width: 80%;"></div>
                    <div class="skeleton skeleton-text" style="width: 50%; margin-bottom: 40px;"></div>
                    <div class="skeleton skeleton-text" style="height: 40px; width: 140px; border-radius: 4px;"></div>
                </div>
            </div>
        </div>

        <div v-else-if="errorMessage" class="empty-state">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
            <h2>Erreur</h2>
            <p>{{ errorMessage }}</p>
        </div>

        <div v-else-if="artistesList.length === 0" class="empty-state" style="animation: fadeInUp 0.8s ease-out forwards;">
            <i class="fas fa-users" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Aucun artiste pour le moment</h2>
            <p>Notre galerie est en train de sélectionner ses talents.</p>
        </div>

        <div v-else class="artistes-grid" style="animation: fadeInUp 0.8s ease-out forwards;">
            
            <div v-for="artiste in artistesList" :key="artiste.id" class="artist-card-full">
                <div class="artist-image-container">
                    <img :src="artiste.image_url || '/assets/img/default-artist.jpg' || 'https://via.placeholder.com/400x400?text=Artist'" 
                         :alt="artiste.nom" class="artist-full-img">
                </div>
                
                <div class="artist-details">
                    <h3>{{ artiste.nom }}</h3>
                    <div class="artist-divider"></div>
                    <p class="artist-bio-short">{{ artiste.biographie || 'Artiste contemporain de la Galerie EXIT.' }}</p>
                    
                    <a :href="'/galerie/artiste/' + artiste.id" class="btn-primary" style="padding: 10px 20px; font-size: 0.8rem; margin-top: 15px;">
                        Voir le profil
                    </a>
                </div>
            </div>

        </div>
    </section>
</main>

<style>
/* Специфічний CSS для скелетонів художників */
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
    margin-bottom: 30px;
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
</style>

<script src="/galerie/assets/js/animations.js"></script>
<script src="/galerie/assets/js/artistes.js"></script>

<?php include 'includes/footer.php'; ?>