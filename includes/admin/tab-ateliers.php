<div class="admin-tab-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2>Gestion des Ateliers</h2>
        <p>Créez et gérez les cours de peinture et masterclasses de la galerie.</p>
    </div>
    <button class="btn-primary" @click="openAtelierModal">
        <i class="fas fa-plus"></i> Ajouter un atelier
    </button>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date & Heure</th>
                <th>Atelier</th>
                <th>Professeur</th>
                <th>Places</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="at in allAteliers" :key="at.id">
                <td>
                    <div style="font-weight: bold; color: var(--color-gold);">{{ formatDate(at.date_atelier) }}</div>
                    <div style="font-size: 0.8rem; color: #888;">à {{ getTime(at.date_atelier) }}</div>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img :src="at.image_url || 'https://via.placeholder.com/40'" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                        <strong>{{ at.titre }}</strong>
                    </div>
                </td>
                <td>{{ at.professeur }}</td>
                <td>
                    <span :class="['status-badge', at.places_reservees >= at.places_totales ? 'error' : 'success']">
                        {{ at.places_reservees || 0 }} / {{ at.places_totales }}
                    </span>
                </td>
                <td><strong>{{ at.prix }} €</strong></td>
                <td>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-icon" @click="editAtelier(at)" title="Modifier"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon" @click="askDelete('atelier', at.id)" title="Supprimer" style="color: #ff4d4d;"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
            <tr v-if="allAteliers.length === 0">
                <td colspan="6" style="text-align:center; padding: 40px; color: #888;">Aucun atelier créé pour le moment.</td>
            </tr>
        </tbody>
    </table>
</div>

<teleport to="body">
    <div v-if="showAtelierModal" class="modal-overlay">
        <div class="modal-box" style="background: #0f0f0f; border: 1px solid rgba(212, 175, 55, 0.2); max-width: 650px;">
            <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
                <h3 style="font-size: 1.2rem; font-weight: 500;">{{ isEditingAtelier ? 'Modifier l\'atelier' : 'Nouvel Atelier' }}</h3>
                <button class="btn-close" @click="closeAtelierModal"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="modal-body" style="padding-top: 20px;">
                <form @submit.prevent="saveAtelier" class="exit-form">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label style="color: #666; font-size: 0.7rem; letter-spacing: 1px;">Titre de l'atelier *</label>
                            <input type="text" v-model="atelierForm.titre" required placeholder="Ex: Masterclass Aquarelle" style="background: #1a1a1a; border: 1px solid #2a2a2a; padding: 12px; font-size: 0.9rem;">
                        </div>
                        <div class="form-group">
                            <label style="color: #666; font-size: 0.7rem; letter-spacing: 1px;">Professeur</label>
                            <input type="text" v-model="atelierForm.professeur" required placeholder="Ex: Sabrina" style="background: #1a1a1a; border: 1px solid #2a2a2a; padding: 12px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="color: #666; font-size: 0.7rem; letter-spacing: 1px;">Description</label>
                        <textarea v-model="atelierForm.description" rows="3" required placeholder="Détails du cours..." style="background: #1a1a1a; border: 1px solid #2a2a2a; padding: 12px; font-size: 0.9rem; resize: vertical;"></textarea>
                    </div>

                    <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr;">
                        <div class="form-group">
                            <label style="color: #666; font-size: 0.7rem; letter-spacing: 1px;">Date & Heure</label>
                            <input type="datetime-local" v-model="atelierForm.date_atelier" required style="background: #1a1a1a; border: 1px solid #2a2a2a; padding: 12px; font-size: 0.9rem; color-scheme: dark;">
                        </div>
                        <div class="form-group">
                            <label style="color: #666; font-size: 0.7rem; letter-spacing: 1px;">Prix (€)</label>
                            <input type="number" v-model="atelierForm.prix" required style="background: #1a1a1a; border: 1px solid #2a2a2a; padding: 12px; font-size: 0.9rem;">
                        </div>
                        <div class="form-group">
                            <label style="color: #666; font-size: 0.7rem; letter-spacing: 1px;">Places totales</label>
                            <input type="number" v-model="atelierForm.places_totales" required style="background: #1a1a1a; border: 1px solid #2a2a2a; padding: 12px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div class="form-group" style="border: 1px dashed rgba(255,255,255,0.1); padding: 15px; border-radius: 4px; background: rgba(0,0,0,0.2);">
                        <input type="file" @change="handleAtelierFile" accept="image/*" style="width: 100%; font-size: 0.8rem;">
                        <p v-if="atelierForm.image_url" style="font-size: 0.8rem; color: var(--color-gold); margin-top: 10px;">Image actuelle configurée</p>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px;">
                        <button type="button" @click="closeAtelierModal" style="background: #2a2a2a; color: #fff; border: none; padding: 10px 25px; font-size: 0.8rem; font-weight: bold; cursor: pointer; border-radius: 2px;">
                            ANNULER
                        </button>
                        <button type="submit" :disabled="isSavingAtelier" style="background: transparent; color: var(--color-gold); border: 1px solid var(--color-gold); padding: 10px 25px; font-size: 0.8rem; font-weight: bold; cursor: pointer; border-radius: 2px;">
                            <span v-if="isSavingAtelier"><i class="fas fa-spinner fa-spin"></i> ENREGISTREMENT...</span>
                            <span v-else>{{ isEditingAtelier ? 'METTRE À JOUR' : 'CRÉER L\'ATELIER' }}</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</teleport>