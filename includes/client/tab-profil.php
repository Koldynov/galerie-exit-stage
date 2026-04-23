<div class="client-header-row">
    <h2 class="client-title">Informations Personnelles</h2>
    <button v-if="!isEditing" @click="startEdit" class="btn-primary small-btn">
        <i class="fas fa-edit"></i> Modifier
    </button>
</div>

<div v-if="!isEditing" class="profile-info-box">
    <div class="info-group">
        <span class="info-label">Prénom</span>
        <span class="info-value">{{ userProfile.first_name || 'Non renseigné' }}</span>
    </div>
    <div class="info-group">
        <span class="info-label">Nom</span>
        <span class="info-value">{{ userProfile.last_name || 'Non renseigné' }}</span>
    </div>
    <div class="info-group">
        <span class="info-label">Téléphone</span>
        <span class="info-value">{{ userProfile.phone || 'Non renseigné' }}</span>
    </div>
</div>

<div v-else class="profile-edit-form">
    <div class="input-row">
        <div class="input-group">
            <label>Prénom</label>
            <input type="text" v-model="editForm.first_name" class="client-input">
        </div>
        <div class="input-group">
            <label>Nom</label>
            <input type="text" v-model="editForm.last_name" class="client-input">
        </div>
    </div>
    <div class="input-group" style="margin-bottom: 25px;">
        <label>Téléphone</label>
        <input type="tel" v-model="editForm.phone" class="client-input">
    </div>
    
    <div v-if="successMsg" class="msg-success"><i class="fas fa-check-circle"></i> {{ successMsg }}</div>
    <div v-if="errorMsg" class="msg-error"><i class="fas fa-exclamation-circle"></i> {{ errorMsg }}</div>
    
    <div class="client-actions-row">
        <button @click="saveProfile" class="btn-primary" :disabled="isSaving">
            <span v-if="isSaving"><i class="fas fa-spinner fa-spin"></i></span>
            <span v-else>Enregistrer</span>
        </button>
        <button @click="cancelEdit" class="btn-cancel" :disabled="isSaving">Annuler</button>
    </div>
</div>

<div v-if="recommendedList.length > 0" style="margin-top: 60px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 40px;">
    <h3 style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 20px;"><i class="fas fa-magic"></i> Sélection pour vous</h3>
    <div class="client-grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));">
        <div v-for="rec in recommendedList" :key="rec.id" class="fav-card">
            <a :href="'oeuvre/' + rec.id" class="card-img-link" style="height: 180px;">
    <img :src="rec.image_url" :alt="rec.titre">
</a>
            <div class="card-details" style="display: block; padding-top: 10px;">
                <h4 style="font-size: 1rem; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ rec.titre }}</h4>
                <p class="price" style="font-size: 0.9rem;">{{ rec.prix }} €</p>
            </div>
        </div>
    </div>
</div>