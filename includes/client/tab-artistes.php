<div class="client-dashboard-section">
    <div class="section-header" style="margin-bottom: 30px;">
        <h2 style="font-size: 2rem; margin: 0; color: white;">Artistes <span class="gold">Suivis</span></h2>
        <p style="color: #888; margin-top: 10px;">Retrouvez ici les artistes que vous suivez.</p>
    </div>

    <div v-if="loadingArtistes" style="text-align: center; padding: 40px;">
        <div class="loader" style="margin: 0 auto; width: 30px; height: 30px; border-width: 3px;"></div>
    </div>

    <div v-else-if="followedArtistes.length === 0" class="empty-state" style="border: 1px dashed rgba(212, 175, 55, 0.2); padding: 50px 20px;">
        <i class="fas fa-user-plus" style="font-size: 3rem; color: #555; margin-bottom: 15px;"></i>
        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Aucun artiste suivi</h3>
        <p style="color: #888; margin-bottom: 20px;">Découvrez nos talents et suivez-les pour ne rien manquer de leurs nouveautés.</p>
        <a href="/galerie/artistes" class="btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">Découvrir les artistes</a>
    </div>

    <div v-else class="artists-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <div v-for="artiste in followedArtistes" :key="artiste.id" class="artist-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; overflow: hidden; transition: transform 0.3s;">
            <a :href="'/galerie/artiste/' + artiste.id" style="display: block; text-decoration: none;">
                <div style="aspect-ratio: 1; overflow: hidden; position: relative;">
                    <img :src="artiste.image_url || 'assets/img/default-artist.jpg'" :alt="artiste.nom" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                </div>
                <div style="padding: 20px; text-align: center;">
                    <h3 style="color: white; margin: 0 0 5px 0; font-size: 1.2rem;">{{ artiste.nom }}</h3>
                    <p style="color: var(--color-gold); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 15px 0;">{{ artiste.titre_pro }}</p>
                    <button @click.prevent="unfollowArtist(artiste.id)" class="btn-icon" style="color: var(--color-red-heart); background: transparent; border: 1px solid rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 20px; font-size: 0.9rem; cursor: pointer; transition: all 0.3s;">
                        <i class="fas fa-heart-broken" style="margin-right: 5px;"></i> Ne plus suivre
                    </button>
                </div>
            </a>
        </div>
    </div>
</div>