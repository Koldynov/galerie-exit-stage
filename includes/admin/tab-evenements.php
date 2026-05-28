<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 class="admin-header-title">Gestion des Événements</h1>
        <p style="color: #888; font-size: 0.9rem;">Dégustations, shows culinaires et rencontres exclusives.</p>
    </div>
    <button @click="openEvenementModal" class="btn-primary" style="padding: 10px 20px; font-size: 0.8rem;">
        <i class="fas fa-plus"></i> Nouvel Événement
    </button>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Aperçu</th>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Places</th>
                <th style="text-align: center;" title="Afficher sur la page d'accueil">🏠 Accueil</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="ev in evenementsOnly" :key="ev.id">
                <td>
                    <div style="font-weight: bold; color: var(--color-gold);">{{ formatDateShort(ev.date_atelier) }}</div>
                    <div style="font-size: 0.8rem; color: #888;">à {{ getTime(ev.date_atelier) }}</div>
                </td>
                <td>
                    <img :src="ev.image_url || 'https://via.placeholder.com/40'" class="admin-thumbnail" style="object-fit: cover; width: 45px; height: 45px; border-radius: 4px;">
                </td>
                <td><strong style="color: white;">{{ ev.titre }}</strong></td>
                <td>{{ ev.categorie }}</td>
                <td>
                    <span :class="['status-badge', ev.places_reservees >= ev.places_totales ? 'error' : 'success']">
                        {{ ev.places_reservees || 0 }} / {{ ev.places_totales }}
                    </span>
                </td>
                
                <!-- Будиночок для Головної сторінки (Тип: Événement) -->
                <td style="text-align: center;">
                    <button @click="toggleHomepageExperience(ev.id, ev.is_on_homepage, 'Événement')" class="action-btn">
                        <i class="fas fa-home" :style="{ color: ev.is_on_homepage ? '#3498db' : '#666', fontSize: '1.2rem' }"></i>
                    </button>
                </td>

                <td>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-icon" @click="viewParticipants(ev)" title="Voir les participants" style="color: #3498db;"><i class="fas fa-users"></i></button>
                        <button class="btn-icon" @click="editAtelier(ev)" title="Modifier"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon" @click="askDelete('atelier', ev.id)" title="Supprimer" style="color: #ff4d4d;"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
            </tr>
            <tr v-if="evenementsOnly.length === 0">
                <td colspan="7" style="text-align:center; padding: 40px; color: #888;">Aucun événement programmé.</td>
            </tr>
        </tbody>
    </table>
</div>