<div v-if="showAddModal" class="modal-overlay" @click.self="closeModal" style="z-index: 500;">
    <div class="modal-content" style="animation: slideUp 0.4s ease-out; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="color: var(--color-gold);">Nouvelle Œuvre</h2>
            <button @click="closeModal" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <div class="input-group" style="position: relative; margin-bottom: 25px;">
    <label style="display: block; margin-bottom: 10px; color: #d4af37; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Catégorie de l'œuvre</label>
    
    <select v-model="newArtwork.categorie" style="appearance: none; -webkit-appearance: none; width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 15px; border-radius: 4px; color: white; outline: none; font-size: 1rem; cursor: pointer; font-family: inherit;">
        <option value="" disabled style="background-color: #111; color: #888;">Sélectionnez une catégorie...</option>
        <option value="Peinture" style="background-color: #111; color: white;">Peinture (Toile, Aquarelle...)</option>
        <option value="Sculpture" style="background-color: #111; color: white;">Sculpture</option>
        <option value="Photographie" style="background-color: #111; color: white;">Photographie</option>
        <option value="Autre" style="background-color: #111; color: white;">Autre format</option>
    </select>
    
    <i class="fas fa-chevron-down" style="position: absolute; right: 15px; bottom: 18px; pointer-events: none; color: #888; font-size: 1rem;"></i>
</div>

<div v-if="newArtwork.categorie" class="input-group" style="position: relative; margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 10px; color: #d4af37; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">
                Sous-catégories <span style="color: #888; text-transform: none; letter-spacing: normal;">(Plusieurs choix possibles)</span>
            </label>
            
            <div style="display: flex; flex-wrap: wrap; gap: 12px; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                
                <template v-if="newArtwork.categorie === 'Peinture'">
                    <label class="custom-checkbox"><input type="checkbox" value="Art-Contemporain" v-model="newArtwork.sous_categories"> <span>Art Contemporain</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Abstrait" v-model="newArtwork.sous_categories"> <span>Abstrait</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Figuratif" v-model="newArtwork.sous_categories"> <span>Figuratif</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Street-Art" v-model="newArtwork.sous_categories"> <span>Street Art & Pop Art</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Minimalisme" v-model="newArtwork.sous_categories"> <span>Minimalisme</span></label>
                </template>

                <template v-else-if="newArtwork.categorie === 'Sculpture'">
                    <label class="custom-checkbox"><input type="checkbox" value="Bronze" v-model="newArtwork.sous_categories"> <span>Bronze</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Pierre-Marbre" v-model="newArtwork.sous_categories"> <span>Pierre & Marbre</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Metal-Resine" v-model="newArtwork.sous_categories"> <span>Métal & Résine</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Bois" v-model="newArtwork.sous_categories"> <span>Bois</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Ceramique" v-model="newArtwork.sous_categories"> <span>Céramique</span></label>
                </template>

                <template v-else-if="newArtwork.categorie === 'Photographie'">
                    <label class="custom-checkbox"><input type="checkbox" value="Noir-Blanc" v-model="newArtwork.sous_categories"> <span>Noir et Blanc</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Argentique" v-model="newArtwork.sous_categories"> <span>Argentique</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Portrait" v-model="newArtwork.sous_categories"> <span>Portrait</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Architecture" v-model="newArtwork.sous_categories"> <span>Architecture & Urbain</span></label>
                    <label class="custom-checkbox"><input type="checkbox" value="Paysages" v-model="newArtwork.sous_categories"> <span>Paysages</span></label>
                </template>
                
                <label class="custom-checkbox" style="width: 100%; margin-top: 5px; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 10px;">
                    <input type="checkbox" value="Autre" v-model="newArtwork.sous_categories"> <span style="color: #888;">Autre / Ne pas spécifier</span>
                </label>
            </div>
        </div>

        <div class="input-group">
            <label>Titre de l'œuvre</label>
            <input type="text" v-model="newArtwork.titre" placeholder="Ex: Nuit Cendrée">
        </div>

        <div class="input-group">
            <label>Prix (€) <span style="color: #666; font-size: 0.8rem;">(Laissez 0 pour "Sur demande")</span></label>
            <input type="number" v-model="newArtwork.prix" placeholder="Ex: 1500" min="0">
        </div>
        
        <div class="input-group">
            <label>Description / Histoire</label>
            <textarea v-model="newArtwork.description" rows="3" placeholder="Racontez l'histoire..." style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical;"></textarea>
        </div>

        <div class="input-group">
            <label>Photo de l'œuvre</label>
            <input type="file" accept="image/*" @change="handleFileUpload" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; cursor: pointer;">
            <p v-if="selectedFileName" style="font-size: 0.85rem; color: var(--color-gold); margin-top: 8px;">
                <i class="fas fa-file-image"></i> {{ selectedFileName }}
            </p>
        </div>

        <div v-if="modalError" class="error-message" style="margin-bottom: 15px;"><i class="fas fa-exclamation-circle"></i> {{ modalError }}</div>

        <button @click="submitArtwork" class="btn-primary" style="width: 100%; margin-top: 10px;" :disabled="isSaving">
            <span v-if="isSaving"><i class="fas fa-spinner fa-spin"></i> Publication et upload...</span>
            <span v-else>Publier l'œuvre</span>
        </button>
    </div>
</div>

<div v-if="showEditProfile" class="modal-overlay" @click.self="closeEditProfile" style="z-index: 500;">
    <div class="modal-content" style="animation: slideUp 0.4s ease-out; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="color: var(--color-gold);">Modifier le profil</h2>
            <button @click="closeEditProfile" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>

        <div class="input-group">
            <label>Nom d'artiste</label>
            <input type="text" v-model="editProfileForm.nom">
        </div>

        <div class="input-group">
            <label>Titre professionnel <span style="color: #666; font-size: 0.8rem;">(Optionnel)</span></label>
            <input type="text" v-model="editProfileForm.titre_pro" placeholder="Ex: Sculpteur Contemporain">
        </div>

        <div class="input-group">
            <label>Votre citation ou devise <span style="color: #666; font-size: 0.8rem;">(Optionnel)</span></label>
            <input type="text" v-model="editProfileForm.citation" placeholder="Ex: L'art est le reflet de l'âme...">
        </div>
        
        <div class="input-group">
            <label>Biographie complète</label>
            <textarea v-model="editProfileForm.biographie" rows="5" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical;"></textarea>
        </div>

        <div class="input-group">
            <label>Photo de profil</label>
            <input type="file" accept="image/*" @change="handleProfileFileUpload" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; cursor: pointer;">
            <p v-if="profileFileName" style="font-size: 0.85rem; color: var(--color-gold); margin-top: 8px;">
                <i class="fas fa-file-image"></i> {{ profileFileName }}
            </p>
        </div>

        <div v-if="profileError" class="error-message" style="margin-bottom: 15px;"><i class="fas fa-exclamation-circle"></i> {{ profileError }}</div>

        <button @click="submitProfileUpdate" class="btn-primary" style="width: 100%; margin-top: 10px;" :disabled="isSavingProfile">
            <span v-if="isSavingProfile"><i class="fas fa-spinner fa-spin"></i> Mise à jour...</span>
            <span v-else>Enregistrer les modifications</span>
        </button>
    </div>
</div>