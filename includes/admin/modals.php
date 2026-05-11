<teleport to="body">
    <div v-if="showAdminMsgModal" class="admin-modal-overlay" @click.self="showAdminMsgModal = false">
        <div class="admin-modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="color: white; margin: 0;">Message pour {{ activeMsgArtist?.nom }}</h2>
                <button @click="showAdminMsgModal = false" style="background: none; border: none; color: #888; cursor: pointer; font-size: 1.2rem;"><i class="fas fa-times"></i></button>
            </div>
            <textarea v-model="adminMsgText" rows="4" class="admin-input" placeholder="Écrivez votre message à l'artiste ici... Il le verra dans son Studio."></textarea>
            <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 20px;">
                <button @click="showAdminMsgModal = false" class="admin-modal-btn cancel">Annuler</button>
                <button @click="sendAdminMessage" class="btn-primary" :disabled="isSendingMsg">
                    <span v-if="isSendingMsg">Envoi...</span><span v-else>Envoyer le message</span>
                </button>
            </div>
        </div>
    </div>
</teleport>

<teleport to="body">
    <div v-if="deleteModal.show" class="admin-modal-overlay" @click.self="closeDeleteModal">
        <div class="admin-modal-content">
            <i class="fas fa-exclamation-triangle" style="font-size: 3.5rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
            <h2 style="color: white; margin-bottom: 15px; font-size: 1.8rem;">Êtes-vous sûr ?</h2>
            <p style="color: #bbb; margin-bottom: 30px; font-size: 1rem; line-height: 1.5;">{{ deleteModal.message }}</p>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <button @click="closeDeleteModal" class="admin-modal-btn cancel">Annuler</button>
                <button @click="confirmDelete" class="admin-modal-btn confirm" :disabled="isDeleting">
                    <span v-if="isDeleting"><i class="fas fa-spinner fa-spin"></i> Suppression...</span>
                    <span v-else>Oui, supprimer</span>
                </button>
            </div>
        </div>
    </div>
</teleport>

<teleport to="body">
    <div v-if="showNewsModal" class="admin-modal-overlay" @click.self="closeNewsModal">
        <div class="admin-modal-content" style="max-width: 650px; text-align: left; max-height: 90vh; overflow-y: auto;">
            <h2 style="color: white; margin-bottom: 25px; border-bottom: 1px solid rgba(212, 175, 55, 0.3); padding-bottom: 10px;">{{ isEditingNews ? 'Modifier l\'article' : 'Nouvel Article' }}</h2>
            <div style="margin-bottom: 15px;">
                <label style="display: block; color: #aaa; margin-bottom: 5px; font-size: 0.85rem; text-transform: uppercase;">Titre *</label>
                <input v-model="newNews.titre" type="text" class="admin-input">
            </div>
            <div style="display: flex; gap: 20px; margin-bottom: 15px; align-items: flex-start;">
                <div style="flex: 1;">
                    <label style="display: block; color: #aaa; margin-bottom: 5px; font-size: 0.85rem; text-transform: uppercase;">Catégories</label>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <div v-for="cat in ['Actualité', 'Nouvelles', 'Exposition', 'Événement']" :key="cat" @click="toggleNewsCategory(cat)" class="chip" :style="newNews.categorie.includes(cat) ? 'background: var(--color-gold); color: black; border-color: var(--color-gold); font-weight: bold;' : 'background: rgba(255,255,255,0.05); color: #bbb; border: 1px solid rgba(255,255,255,0.1);'" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; user-select: none;">{{ cat }}</div>
                    </div>
                </div>
                <div v-if="newNews.categorie.includes('Exposition') || newNews.categorie.includes('Événement')" style="flex: 2; display: flex; gap: 10px;">
                    <div style="flex: 1;"><label style="display: block; color: var(--color-gold); margin-bottom: 5px; font-size: 0.85rem; text-transform: uppercase;">Date début *</label><input v-model="newNews.event_date" type="date" class="admin-input" style="border-color: var(--color-gold); color-scheme: dark;"></div>
                    <div style="flex: 1;"><label style="display: block; color: #aaa; margin-bottom: 5px; font-size: 0.85rem; text-transform: uppercase;">Date fin</label><input v-model="newNews.event_end_date" type="date" class="admin-input" style="color-scheme: dark;"></div>
                </div>
            </div>
            <div style="margin-bottom: 15px; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 4px; border: 1px dashed rgba(212,175,55,0.3);">
                <div v-if="isEditingNews && newNews.image_url && !selectedFile" style="margin-bottom: 10px; text-align: center;"><p style="font-size: 0.7rem; color: #888; text-transform: uppercase; margin-bottom: 5px;">Image actuelle</p><img :src="newNews.image_url" style="height: 80px; border-radius: 4px; border: 1px solid var(--color-gold);"></div>
                <input type="file" @change="handleFileUpload" accept="image/*" style="color: white; font-size: 0.8rem;">
                <input v-if="!selectedFile" v-model="newNews.image_url" type="text" placeholder="Ou coller URL..." class="admin-input" style="margin-top: 10px;">
            </div>
            <div style="margin-bottom: 25px;"><label style="display: block; color: #aaa; margin-bottom: 5px; font-size: 0.85rem; text-transform: uppercase;">Texte</label><textarea v-model="newNews.description" rows="5" class="admin-input"></textarea></div>
            <div style="display: flex; gap: 15px; justify-content: flex-end;"><button @click="closeNewsModal" class="admin-modal-btn cancel">Annuler</button><button @click="saveNews" class="btn-primary" :disabled="isSavingNews"><span v-if="isSavingNews">Enregistrement...</span><span v-else>{{ isEditingNews ? 'Mettre à jour' : 'Publier' }}</span></button></div>
        </div>
    </div>
</teleport>

<teleport to="body">
    <div v-if="showDemandeModal" class="admin-modal-overlay" @click.self="closeDemandeModal">
        <div class="admin-modal-content" style="max-width: 650px; text-align: left; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(212,175,55,0.3); padding-bottom: 10px;">
                <h2 style="color: white; margin: 0;">Gestion de la demande</h2>
                <button @click="closeDemandeModal" style="background: none; border: none; color: #888; cursor: pointer; font-size: 1.2rem;"><i class="fas fa-times"></i></button>
            </div>
            <div style="display: flex; gap: 20px; margin-bottom: 25px; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 8px;">
                <div style="flex: 1;"><p style="color: #aaa; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 5px;">Client</p><p style="color: white; font-weight: bold; margin-bottom: 5px;">{{ displayClientName(activeDemande.nom, activeDemande.email) }}</p><p style="color: #888; font-size: 0.9rem; margin-bottom: 5px;"><i class="fas fa-envelope"></i> {{ activeDemande.email }}</p></div>
                <div style="flex: 1; text-align: right;"><p style="color: #aaa; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 5px;">Œuvre</p><p style="color: var(--color-gold); font-weight: bold;">{{ activeDemande.oeuvres?.titre || 'Inconnue' }}</p></div>
            </div>
            <div v-if="activeDemande.message" style="margin-bottom: 25px;">
                <p style="color: #aaa; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 5px;">Message du client</p>
                <div style="background: rgba(0,0,0,0.5); padding: 15px; border-left: 3px solid var(--color-gold); color: #ddd; font-style: italic; border-radius: 0 4px 4px 0;">"{{ activeDemande.message }}"</div>
            </div>
            <div style="margin-bottom: 25px; border: 1px solid rgba(212,175,55,0.3); border-radius: 8px; overflow: hidden;">
                <div @click="toggleEmailComposer" style="background: rgba(212,175,55,0.1); padding: 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                    <strong style="color: var(--color-gold);"><i class="fas fa-paper-plane"></i> Répondre au client par email</strong>
                    <i :class="showEmailComposer ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" style="color: var(--color-gold);"></i>
                </div>
                <div v-if="showEmailComposer" style="padding: 20px; background: rgba(0,0,0,0.3);">
                    <div v-if="emailSentSuccess" style="text-align: center; color: #2ecc71; padding: 20px;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 10px;"></i>
                        <p>Email envoyé avec succès à {{ activeDemande.email }} !</p>
                    </div>
                    <div v-else>
                        <label style="display: block; color: #888; margin-bottom: 5px; font-size: 0.8rem;">Sujet de l'email</label>
                        <input v-model="emailSubject" type="text" class="admin-input" style="margin-bottom: 15px;">
                        <label style="display: block; color: #888; margin-bottom: 5px; font-size: 0.8rem;">Votre réponse</label>
                        <textarea v-model="emailBody" rows="6" class="admin-input" style="margin-bottom: 15px;"></textarea>
                        <button @click="sendEmail" class="btn-primary" style="width: 100%; padding: 12px;" :disabled="isSendingEmail">
                            <span v-if="isSendingEmail"><i class="fas fa-spinner fa-spin"></i> Envoi en cours...</span><span v-else>Envoyer l'email</span>
                        </button>
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #aaa; margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;">Statut de la demande</label>
                <select v-model="activeDemande.statut" class="admin-input">
                    <option value="Nouveau">🔴 Nouveau</option>
                    <option value="En cours">🟡 En cours (Email envoyé / Négociation)</option>
                    <option value="Conclu">🟢 Conclu (Vendu)</option>
                    <option value="Annulé">⚪ Annulé</option>
                </select>
            </div>
            <div style="margin-bottom: 25px;">
                <label style="display: block; color: var(--color-gold); margin-bottom: 8px; font-size: 0.85rem; text-transform: uppercase;"><i class="fas fa-lock"></i> Notes internes (Privé)</label>
                <textarea v-model="activeDemande.notes_internes" rows="3" class="admin-input" style="border: 1px dashed rgba(212,175,55,0.5); background: rgba(212,175,55,0.02);"></textarea>
            </div>
            <button @click="saveDemandeStatus" class="btn-primary" style="width: 100%; padding: 15px; background: white; color: black;" :disabled="isSavingDemande">
                <span v-if="isSavingDemande">Mise à jour...</span><span v-else>Enregistrer les modifications internes</span>
            </button>
        </div>
    </div>
</teleport>