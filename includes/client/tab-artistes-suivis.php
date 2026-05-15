<div v-if="artistesSuivis.length === 0" class="empty-state" style="text-align: center; padding: 50px 0;">
    <i class="fas fa-user-plus" style="font-size: 4rem; color: #333; margin-bottom: 20px;"></i>
    <h2 style="color: white; margin-bottom: 10px;">Aucun artiste suivi</h2>
    <p style="color: #888; margin-bottom: 30px;">Découvrez nos talents et suivez vos artistes préférés pour ne rien manquer de leurs nouveautés.</p>
    <a href="/galerie/artistes" class="btn-primary">Découvrir les artistes</a>
</div>

<div v-else>
    <h2 style="font-size: 2rem; color: var(--color-gold); margin-bottom: 30px; font-weight: 300;">Mes Artistes Suivis</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 30px;">
        <div v-for="suivi in artistesSuivis" :key="suivi.id" style="background: #111; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 20px rgba(0,0,0,0.5)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            
            <div style="width: 100px; height: 100px; margin: 25px auto 15px auto; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-gold);">
                <img :src="suivi.artistes?.image_url || 'https://via.placeholder.com/150?text=Photo'" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <div style="padding: 0 20px 25px 20px;">
                <h3 style="color: white; margin: 0 0 5px 0; font-size: 1.2rem;">{{ suivi.artistes?.nom }}</h3>
                <p style="color: #888; font-size: 0.85rem; margin: 0 0 20px 0; text-transform: uppercase; letter-spacing: 1px;">{{ suivi.artistes?.titre_pro || 'Artiste' }}</p>
                
                <a :href="'/galerie/artiste/' + suivi.artistes?.id" class="btn-primary" style="display: block; width: 100%; font-size: 0.8rem; padding: 10px; text-align: center; text-decoration: none;">Voir le profil</a>
            </div>
        </div>
    </div>
</div>