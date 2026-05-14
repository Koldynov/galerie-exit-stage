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
                        <button class="btn-icon" @click="viewParticipants(at)" title="Voir les participants" style="color: #3498db;"><i class="fas fa-users"></i></button>
                        
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
    <div v-if="showParticipantsModal" class="modal-overlay" @click.self="showParticipantsModal = false">
        <div class="modal-box" style="background: #151515; border: 1px solid rgba(212, 175, 55, 0.3); max-width: 700px;">
            <div class="modal-header">
                <h3>Participants : {{ currentAtelierName }}</h3>
                <button class="btn-close" @click="showParticipantsModal = false"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <div v-if="isLoadingParticipants" style="text-align: center; padding: 30px;">
                    <i class="fas fa-spinner fa-spin" style="color: var(--color-gold); font-size: 2rem;"></i>
                </div>
                <div v-else-if="currentAtelierParticipants.length === 0" style="text-align: center; padding: 30px; color: #888;">
                    <i class="fas fa-user-slash" style="font-size: 3rem; margin-bottom: 15px; color: #444;"></i>
                    <p>Aucune réservation pour cet atelier pour le moment.</p>
                </div>
                <div v-else>
                    <table class="admin-table" style="width: 100%; text-align: left; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <th style="padding: 10px; color: var(--color-gold);">Client</th>
                                <th style="padding: 10px; color: var(--color-gold);">Places</th>
                                <th style="padding: 10px; color: var(--color-gold);">Date de réservation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in currentAtelierParticipants" :key="p.id" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 15px 10px;">
                                    <div style="font-weight: bold; color: #fff;">{{ p.nom_client }}</div>
                                    <div style="font-size: 0.8rem; color: #aaa;"><i class="fas fa-envelope"></i> {{ p.user_email }}</div>
                                </td>
                                <td style="padding: 15px 10px; font-weight: bold;">{{ p.places_reservees }}</td>
                                <td style="padding: 15px 10px; font-size: 0.85rem; color: #888;">{{ formatDate(p.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</teleport>

