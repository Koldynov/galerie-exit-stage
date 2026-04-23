<h2 class="client-title" style="margin-bottom: 30px;">Mes Demandes de Prix</h2>

<div v-if="demandesList.length === 0" class="client-empty-box">
    <i class="far fa-envelope-open"></i>
    <p>Aucune demande envoyée pour le moment.</p>
</div>

<div v-else class="list-container">
    <div v-for="dem in demandesList" :key="dem.id" class="list-item">
        <div class="item-content">
            <img v-if="dem.oeuvres" :src="dem.oeuvres.image_url" class="item-thumb">
            <div v-else class="item-thumb placeholder"><i class="fas fa-image"></i></div>
            
            <div class="item-info">
                <h4>{{ dem.oeuvres?.titre || 'Œuvre inconnue' }}</h4>
                <p class="date"><i class="far fa-calendar-alt"></i> Envoyée le {{ formatDate(dem.created_at) }}</p>
            </div>
            
            <div class="item-status">
                <span :class="['status-badge', getDemandeStatusClass(dem.statut)]">{{ dem.statut || 'Nouveau' }}</span>
            </div>
        </div>
    </div>
</div>