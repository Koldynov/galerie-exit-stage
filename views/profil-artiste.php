<?php include 'includes/header.php'; ?>
<script>window.CURRENT_ITEM_ID = "<?= $item_id ?>";</script>

<main id="artist-profile-app" v-cloak>
    <div v-if="isLoading" class="empty-state" style="height: 100vh; border: none;">
        <div class="loader"></div><p>Ouverture de la galerie...</p>
    </div>

    <div v-else-if="errorMessage" class="empty-state" style="height: 100vh; border: none; animation: fadeInUp 0.6s ease-out forwards;">
        <i class="fas fa-search" style="font-size: 4rem; color: var(--color-gold); margin-bottom: 20px;"></i>
        <h2>Artiste Introuvable</h2><p>{{ errorMessage }}</p>
        <a href="/galerie/artistes" class="btn-primary" style="margin-top: 20px;">Retour à la liste</a>
    </div>

    <div v-else>
        <section class="page-hero" style="height: 100vh; justify-content: center; position: relative;">
            <div class="page-hero-content" style="transform: translateY(-5vh); text-align: center;">
                <h1 class="reveal active" style="font-size: 6rem; letter-spacing: 0.05em; margin-bottom: 10px;">{{ artist.nom }}<span class="gold">.</span></h1>
                <p v-if="artist.titre_pro" class="reveal delay-1 active" style="color: var(--color-gold); font-size: 1.2rem; text-transform: uppercase; letter-spacing: 3px; margin-top: 10px;">{{ artist.titre_pro }}</p>
            </div>
            <a href="#collection" class="scroll-down" style="position: absolute; bottom: 8vh; animation: bounce 2s infinite;">
                <span style="margin-bottom: 10px; display: block;">Découvrir les œuvres</span><i class="fas fa-chevron-down" style="font-size: 1.5rem;"></i>
            </a>
        </section>

        <section id="collection" class="container" style="margin-bottom: 100px; padding-top: 50px;">
            <div class="section-header" style="justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px;">
                <h2 class="reveal active" style="font-size: 2.5rem; color: var(--color-white); margin: 0;">Sélection <span class="gold">Privée</span></h2>
                <span class="reveal delay-1 active" style="color: #666; font-size: 1.2rem;">{{ artworks.length }} œuvre(s)</span>
            </div>

            <div v-if="artworks.length === 0" class="empty-state" style="border: 1px dashed rgba(212, 175, 55, 0.2); padding: 80px 20px;">
                <i class="fas fa-palette" style="font-size: 3rem; color: #555; margin-bottom: 15px;"></i>
                <h3 style="font-size: 1.5rem; margin-bottom: 10px;">Aucune œuvre exposée</h3>
                <p style="font-size: 1rem; color: #888;">L'artiste prépare actuellement sa nouvelle collection.</p>
            </div>

            <div v-else class="masonry-gallery reveal delay-2 active">
                <div v-for="oeuvre in artworks" :key="oeuvre.id" class="masonry-item">
                    <div class="masonry-img-wrapper">
                        <a :href="'/galerie/oeuvre/' + oeuvre.id" style="display: block;">
                            <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x800?text=Indisponible'" :alt="oeuvre.titre">
                        </a>
                    </div>
                    <div class="masonry-info">
                        <h3><a :href="'/galerie/oeuvre/' + oeuvre.id" style="color: var(--color-white); text-decoration: none; transition: color 0.3s;" class="masonry-title-link">{{ oeuvre.titre }}</a></h3>
                        <p class="description">{{ oeuvre.description }}</p>
                        <button class="btn-icon" @click="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : '#666', marginTop: '0' }">
                            <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section id="artist-story" class="container" style="padding-bottom: 120px;">
            <div class="elegant-story-grid reveal active">
                <div class="story-image-refined">
                    <img :src="artist.image_url || 'assets/img/default-artist.jpg'" :alt="artist.nom">
                </div>
                <div class="story-text-refined">
                    <blockquote v-if="artist.citation" class="artist-quote">"{{ artist.citation }}"</blockquote>
                    <h2 v-else style="font-size: 2.5rem; margin-bottom: 30px; color: var(--color-white);">L'Essence de l'art.</h2>
                    <p class="bio-paragraph">{{ artist.biographie || 'Cet artiste explore de nouveaux horizons créatifs avec La Galerie EXIT.' }}</p>
                </div>
            </div>
        </section>
    </div>
</main>

<style>[v-cloak] { display: none; }</style>
<script src="/galerie/assets/js/animations.js"></script>
<script src="/galerie/assets/js/profil-artiste.js"></script>

<?php include 'includes/footer.php'; ?>