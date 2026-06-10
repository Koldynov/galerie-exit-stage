<div v-if="showAddModal" class="modal-overlay" @click.self="closeModal" style="z-index: 500; align-items: flex-start; padding-top: 5vh;">
    <div class="modal-content" style="animation: slideUp 0.4s ease-out; max-height: 90vh; overflow-y: auto; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: var(--color-gold); font-size: 1.5rem; margin: 0;">Nouvelle Œuvre</h2>
            <button @click="closeModal" style="background: none; border: none; color: #888; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#888'"><i class="fas fa-times"></i></button>
        </div>

        <div class="input-group" style="position: relative; margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #d4af37; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Catégorie de l'œuvre</label>
            <select v-model="newArtwork.categorie" style="appearance: none; -webkit-appearance: none; width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: white; outline: none; font-size: 0.95rem; cursor: pointer; font-family: inherit;">
                <option value="" disabled style="background-color: #111; color: #888;">Sélectionnez une catégorie...</option>
                <option value="Peinture" style="background-color: #111; color: white;">Peinture (Toile, Aquarelle...)</option>
                <option value="Sculpture" style="background-color: #111; color: white;">Sculpture</option>
                <option value="Photographie" style="background-color: #111; color: white;">Photographie</option>
                <option value="Autre" style="background-color: #111; color: white;">Autre format</option>
            </select>
            <i class="fas fa-chevron-down" style="position: absolute; right: 15px; bottom: 15px; pointer-events: none; color: #888; font-size: 0.9rem;"></i>
        </div>

        <div v-if="newArtwork.categorie" class="input-group" style="position: relative; margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #d4af37; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">
                Sous-catégories <span style="color: #888; text-transform: none; letter-spacing: normal;">(Plusieurs choix possibles)</span>
            </label>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
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

        <div style="display: flex; gap: 15px; margin-bottom: 20px;">
            <div class="input-group" style="flex: 2; margin-bottom: 0;">
                <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Titre</label>
                <input type="text" v-model="newArtwork.titre" placeholder="Ex: Nuit Cendrée" style="width: 100%; padding: 12px 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; color: white; outline: none;">
            </div>
            <div class="input-group" style="flex: 1; margin-bottom: 0;">
                <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Prix (€) <span style="color:#666; font-size:0.65rem;">(0=Demande)</span></label>
                <input type="number" v-model="newArtwork.prix" placeholder="Ex: 1500" min="0" style="width: 100%; padding: 12px 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; color: white; outline: none;">
            </div>
        </div>
        
        <div class="input-group" style="margin-bottom: 20px;">
            <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Description / Histoire</label>
            <textarea v-model="newArtwork.description" rows="3" placeholder="Racontez l'histoire..." style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical;"></textarea>
        </div>

        <div class="input-group" style="margin-bottom: 20px;">
            <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Photo de l'œuvre</label>
            <input type="file" accept="image/*" @change="handleFileUpload" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 10px 15px; border-radius: 4px; color: var(--color-white); outline: none; cursor: pointer; font-size: 0.85rem;">
            <p v-if="selectedFileName" style="font-size: 0.85rem; color: var(--color-gold); margin-top: 8px;"><i class="fas fa-file-image"></i> {{ selectedFileName }}</p>
        </div>

        <div v-if="modalError" class="error-message" style="margin-bottom: 15px; color: #ff4d4d; font-size: 0.9rem;"><i class="fas fa-exclamation-circle"></i> {{ modalError }}</div>

        <button @click="submitArtwork" class="btn-primary" style="width: 100%; padding: 14px;" :disabled="isSaving">
            <span v-if="isSaving"><i class="fas fa-spinner fa-spin"></i> Publication...</span>
            <span v-else>Publier l'œuvre</span>
        </button>
    </div>
</div>

<div v-if="showEditProfile" class="modal-overlay" @click.self="closeEditProfile" style="z-index: 500; align-items: flex-start; padding-top: 5vh;">
    <div class="modal-content" style="animation: slideUp 0.4s ease-out; max-height: 90vh; overflow-y: auto; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: var(--color-gold); font-size: 1.5rem; margin: 0;">Modifier le profil</h2>
            <button @click="closeEditProfile" style="background: none; border: none; color: #888; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#888'"><i class="fas fa-times"></i></button>
        </div>

        <div class="input-group" style="margin-bottom: 15px;">
            <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Nom d'artiste</label>
            <input type="text" v-model="editProfileForm.nom" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); padding: 12px 15px; border-radius: 4px; color: white; outline: none;">
        </div>

        <div class="input-group" style="margin-bottom: 15px;">
            <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Titre professionnel <span style="color: #666; text-transform: none;">(Optionnel)</span></label>
            <input type="text" v-model="editProfileForm.titre_pro" placeholder="Ex: Sculpteur Contemporain" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); padding: 12px 15px; border-radius: 4px; color: white; outline: none;">
        </div>

        <div class="input-group" style="margin-bottom: 15px;">
            <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Votre citation <span style="color: #666; text-transform: none;">(Optionnel)</span></label>
            <input type="text" v-model="editProfileForm.citation" placeholder="Ex: L'art est le reflet de l'âme..." style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.2); padding: 12px 15px; border-radius: 4px; color: white; outline: none;">
        </div>
        
        <div class="input-group" style="margin-bottom: 15px;">
            <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Biographie</label>
            <textarea v-model="editProfileForm.biographie" rows="4" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical;"></textarea>
        </div>

        <div class="input-group" style="margin-bottom: 20px;">
            <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Photo de profil</label>
            <input type="file" accept="image/*" @change="handleProfileFileUpload" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 10px 15px; border-radius: 4px; color: var(--color-white); outline: none; cursor: pointer; font-size: 0.85rem;">
            <p v-if="profileFileName" style="font-size: 0.85rem; color: var(--color-gold); margin-top: 8px;"><i class="fas fa-file-image"></i> {{ profileFileName }}</p>
        </div>

        <div v-if="profileError" class="error-message" style="margin-bottom: 15px; color: #ff4d4d; font-size: 0.9rem;"><i class="fas fa-exclamation-circle"></i> {{ profileError }}</div>

        <button @click="submitProfileUpdate" class="btn-primary" style="width: 100%; padding: 14px;" :disabled="isSavingProfile">
            <span v-if="isSavingProfile"><i class="fas fa-spinner fa-spin"></i> Mise à jour...</span>
            <span v-else>Enregistrer les modifications</span>
        </button>
    </div>
</div>

<div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false" style="z-index: 500; align-items: flex-start; padding-top: 75px">
    <div class="modal-content" style="animation: slideUp 0.4s ease-out; max-height: 85vh; overflow-y: auto; padding: 25px 30px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: var(--color-gold); font-size: 1.5rem; margin: 0;">Modifier l'Œuvre</h2>
            <button @click="showEditModal = false" style="background: none; border: none; color: #888; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#888'"><i class="fas fa-times"></i></button>
        </div>
        
        <form @submit.prevent="submitEditModal">
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Titre de l'œuvre</label>
                <input type="text" v-model="editForm.titre" required style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 10px 15px; border-radius: 4px; color: white; outline: none; font-size: 0.95rem;">
            </div>
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                <div class="input-group" style="position: relative; flex: 1; margin-bottom: 0;">
                    <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Catégorie</label>
                    <select v-model="editForm.categorie" style="appearance: none; -webkit-appearance: none; width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 10px 15px; border-radius: 4px; color: white; outline: none; font-size: 0.95rem; cursor: pointer; font-family: inherit;" required>
                        <option value="Peinture" style="background-color: #111; color: white;">Peinture</option>
                        <option value="Sculpture" style="background-color: #111; color: white;">Sculpture</option>
                        <option value="Photographie" style="background-color: #111; color: white;">Photographie</option>
                        <option value="Autre" style="background-color: #111; color: white;">Autre format</option>
                    </select>
                    <i class="fas fa-chevron-down" style="position: absolute; right: 15px; bottom: 12px; pointer-events: none; color: #888; font-size: 0.9rem;"></i>
                </div>
                
                <div class="input-group" style="flex: 1; margin-bottom: 0;">
                    <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Prix (€) <span style="color: #666; text-transform: none;">(0 = Sur demande)</span></label>
                    <input type="number" v-model="editForm.prix" min="0" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 10px 15px; border-radius: 4px; color: white; outline: none; font-size: 0.95rem;">
                </div>
            </div>
            
            <div class="input-group" style="margin-bottom: 15px;">
                <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Description / Histoire</label>
                <textarea v-model="editForm.description" rows="3" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 10px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical; font-size: 0.95rem;"></textarea>
            </div>
            
            <div class="input-group" style="margin-bottom: 20px;">
                <label style="margin-bottom: 5px; color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase;">Nouvelle Photo <span style="color: #666; text-transform: none;">(Optionnel)</span></label>
                <input type="file" @change="handleEditFileUpload" accept="image/*" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 8px 15px; border-radius: 4px; color: var(--color-white); outline: none; cursor: pointer; font-size: 0.85rem;">
                <p style="font-size: 0.75rem; color: #888; margin-top: 5px;">Laissez vide pour conserver l'image actuelle.</p>
            </div>
            
            <button type="submit" class="btn-primary" :disabled="isEditing" style="width: 100%; padding: 14px; font-size: 0.95rem;">
                <span v-if="isEditing"><i class="fas fa-spinner fa-spin"></i> Mise à jour...</span>
                <span v-else>Enregistrer les modifications</span>
            </button>
        </form>
    </div>
</div>

<div v-if="showMessagesModal" class="modal-overlay" @click.self="showMessagesModal = false" style="z-index: 500; align-items: flex-start; padding-top: 12vh;">
    <div class="modal-content" style="animation: slideUp 0.4s ease-out; width: 100%; max-width: 600px; margin: 0 auto; padding: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: var(--color-gold); font-size: 1.5rem; margin: 0;"><i class="fas fa-envelope"></i> Boîte de réception</h2>
            <button @click="showMessagesModal = false" style="background: none; border: none; color: #888; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#888'"><i class="fas fa-times"></i></button>
        </div>
        
        <div v-if="adminMessages.length === 0" style="text-align: center; padding: 40px; color: #888;">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
            <p>Aucun message de la galerie.</p>
        </div>
        
        <div v-else style="max-height: 50vh; overflow-y: auto; padding-right: 15px; margin-right: -5px;">
            <div v-for="msg in adminMessages" :key="msg.id" style="background: rgba(255,255,255,0.02); padding: 20px; border-left: 3px solid var(--color-gold); border-radius: 0 8px 8px 0; margin-bottom: 15px;">
                <p style="color: #ddd; font-size: 1rem; line-height: 1.5; margin-bottom: 10px; white-space: pre-wrap;">{{ msg.message }}</p>
                <span style="font-size: 0.75rem; color: #666;"><i class="far fa-clock"></i> {{ formatDate(msg.created_at) }}</span>
            </div>
        </div>
    </div>
</div>