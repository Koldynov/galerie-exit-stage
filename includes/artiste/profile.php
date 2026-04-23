<div v-if="!artistProfile.is_approved" style="background: rgba(212,175,55,0.1); border: 1px solid var(--color-gold); padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
    <i class="fas fa-hourglass-half" style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 10px;"></i>
    <h3 style="color: var(--color-gold); margin-bottom: 5px;">Profil en attente de validation</h3>
    <p style="color: #bbb; font-size: 0.9rem;">Votre profil est en cours d'examen par la galerie. Vos œuvres ne sont pas encore visibles publiquement sur le site.</p>
</div>

<div class="profile-info" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 40px; margin-bottom: 60px; background: rgba(255,255,255,0.02); padding: 40px; border: 1px solid rgba(212,175,55,0.2); border-radius: 8px;">
    <div style="flex: 0 0 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-gold);">
        <img :src="artistProfile.image_url || 'assets/img/default-artist.jpg' || 'https://via.placeholder.com/400x400?text=Photo'" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div style="flex: 1;">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 5px;">
            <h2 style="color: var(--color-gold); font-size: 2.2rem; margin: 0;">{{ artistProfile.nom }}</h2>
            <button @click="openEditProfile" class="btn-icon" style="color: var(--color-white); font-size: 1rem; margin: 0;" title="Modifier le profil">
                <i class="fas fa-edit"></i> Modifier
            </button>
        </div>
        <p v-if="artistProfile.titre_pro" style="color: #fff; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 20px;">{{ artistProfile.titre_pro }}</p>
        <blockquote v-if="artistProfile.citation" style="font-style: italic; color: #aaa; border-left: 2px solid var(--color-gold); padding-left: 15px; margin-bottom: 20px; font-size: 1.1rem;">
            "{{ artistProfile.citation }}"
        </blockquote>
        <p style="color: #bbb; line-height: 1.8; white-space: pre-wrap; word-wrap: break-word;">{{ artistProfile.biographie || 'Aucune biographie renseignée. Cliquez sur "Modifier" pour en ajouter une.' }}</p>
    </div>
    <div>
        <button @click="logout" class="btn-primary cancel-btn">Quitter</button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 50px;">
    <div style="background: #111; padding: 30px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center; transition: all 0.3s;" class="stat-card">
        <p style="color: #888; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 10px;">Ventes Totales</p>
        <p style="font-size: 2.5rem; color: #2ecc71; font-weight: bold;">{{ mySalesRevenue }} €</p>
    </div>
    <div style="background: #111; padding: 30px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center; transition: all 0.3s;" class="stat-card">
        <p style="color: #888; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 10px;">Œuvres Vendues</p>
        <p style="font-size: 2.5rem; color: white; font-weight: bold;">{{ soldCount }}</p>
    </div>
    <div style="background: #111; padding: 30px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center; transition: all 0.3s;" class="stat-card">
        <p style="color: #888; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 10px;">Œuvres en Vente</p>
        <p style="font-size: 2.5rem; color: var(--color-gold); font-weight: bold;">{{ availableCount }}</p>
    </div>
</div>

<div v-if="adminMessages.length > 0" style="margin-bottom: 60px;">
    <h3 style="margin-bottom: 20px; color: var(--color-gold); font-size: 1.5rem;"><i class="fas fa-bell"></i> Messages de la Galerie</h3>
    <div v-for="msg in adminMessages" :key="msg.id" style="background: rgba(255,255,255,0.02); padding: 20px; border-left: 3px solid var(--color-gold); border-radius: 0 8px 8px 0; margin-bottom: 15px;">
        <p style="color: #ddd; font-size: 1rem; line-height: 1.5; margin-bottom: 10px; white-space: pre-wrap;">{{ msg.message }}</p>
        <span style="font-size: 0.75rem; color: #666;"><i class="far fa-clock"></i> {{ formatDate(msg.created_at) }}</span>
    </div>
</div>