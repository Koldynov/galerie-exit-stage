<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <div>
        <h1 class="admin-header-title" style="margin:0;">Kanban des Leads</h1>
        <p class="admin-subtitle" style="margin-top: 5px;">Glissez et déposez les cartes pour changer leur statut</p>
    </div>
</div>

<div class="kanban-board">
    <div class="kanban-col" @dragover.prevent @drop="dropCard($event, 'Nouveau')">
        <div class="kanban-col-header" style="border-bottom: 3px solid var(--color-red-heart);">🔴 Nouveaux ({{ getDemandesByStatut('Nouveau').length }})</div>
        <div class="kanban-cards">
            <div v-for="dem in getDemandesByStatut('Nouveau')" :key="dem.id" class="kanban-card" draggable="true" @dragstart="dragStart($event, dem)">
                <div class="kanban-card-date">{{ formatDateShort(dem.created_at) }}</div>
                <h4 style="color: white; margin: 5px 0; font-size: 1rem;">{{ displayClientName(dem.nom, dem.email) }}</h4>
                <p style="color: var(--color-gold); font-size: 0.85rem; margin-bottom: 10px;">{{ dem.oeuvres?.titre || 'Œuvre inconnue' }}</p>
                <button @click="openDemandeModal(dem)" class="btn-primary" style="width: 100%; padding: 8px; font-size: 0.75rem;">Gérer</button>
            </div>
        </div>
    </div>

    <div class="kanban-col" @dragover.prevent @drop="dropCard($event, 'En cours')">
        <div class="kanban-col-header" style="border-bottom: 3px solid #f1c40f;">🟡 En Négociation ({{ getDemandesByStatut('En cours').length }})</div>
        <div class="kanban-cards">
            <div v-for="dem in getDemandesByStatut('En cours')" :key="dem.id" class="kanban-card" draggable="true" @dragstart="dragStart($event, dem)">
                <div class="kanban-card-date">{{ formatDateShort(dem.created_at) }}</div>
                <h4 style="color: white; margin: 5px 0; font-size: 1rem;">{{ displayClientName(dem.nom, dem.email) }}</h4>
                <p style="color: var(--color-gold); font-size: 0.85rem; margin-bottom: 10px;">{{ dem.oeuvres?.titre || 'Œuvre inconnue' }}</p>
                <button @click="openDemandeModal(dem)" class="btn-primary" style="width: 100%; padding: 8px; font-size: 0.75rem;">Gérer</button>
            </div>
        </div>
    </div>

    <div class="kanban-col" @dragover.prevent @drop="dropCard($event, 'Conclu')">
        <div class="kanban-col-header" style="border-bottom: 3px solid #2ecc71;">🟢 Conclus ({{ getDemandesByStatut('Conclu').length }})</div>
        <div class="kanban-cards">
            <div v-for="dem in getDemandesByStatut('Conclu')" :key="dem.id" class="kanban-card" style="opacity: 0.7;" draggable="true" @dragstart="dragStart($event, dem)">
                <h4 style="color: white; margin: 5px 0; font-size: 1rem;">{{ displayClientName(dem.nom, dem.email) }}</h4>
                <p style="color: #2ecc71; font-size: 0.85rem; margin-bottom: 10px;"><i class="fas fa-check"></i> Vendu</p>
                <button @click="openDemandeModal(dem)" class="btn-primary" style="width: 100%; padding: 8px; font-size: 0.75rem; background: transparent; border: 1px solid #666; color: #888;">Voir historique</button>
            </div>
        </div>
    </div>

    <div class="kanban-col" @dragover.prevent @drop="dropCard($event, 'Annulé')">
        <div class="kanban-col-header" style="border-bottom: 3px solid #666;">⚪ Annulés ({{ getDemandesByStatut('Annulé').length }})</div>
        <div class="kanban-cards">
            <div v-for="dem in getDemandesByStatut('Annulé')" :key="dem.id" class="kanban-card" style="opacity: 0.5;" draggable="true" @dragstart="dragStart($event, dem)">
                <h4 style="color: white; margin: 5px 0; font-size: 1rem;">{{ displayClientName(dem.nom, dem.email) }}</h4>
                <button @click="openDemandeModal(dem)" class="btn-primary" style="width: 100%; padding: 8px; font-size: 0.75rem; background: transparent; border: 1px solid #666; color: #888; margin-top: 10px;">Détails</button>
            </div>
        </div>
    </div>
</div>