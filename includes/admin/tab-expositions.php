<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 class="admin-header-title">Gestion des Expositions</h1>
        <p style="color: #888; font-size: 0.9rem;">Créez des expositions thématiques et choisissez celle qui apparaîtra sur la page d'accueil.</p>
    </div>
    <button @click="openExpositionModal" class="btn-primary" style="padding: 10px 20px; font-size: 0.8rem;">
        <i class="fas fa-plus"></i> Nouvelle Exposition
    </button>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Couverture</th>
                <th>Titre de l'exposition</th>
                <th style="text-align: center;" title="Afficher sur la page d'accueil">🏠 Page d'accueil</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="expo in allExpositions" :key="expo.id">
                <td style="font-size: 0.85rem; color: #aaa;">{{ formatDate(expo.created_at) }}</td>
                <td>
                    <img :src="expo.image_url || 'https://via.placeholder.com/80x50'" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                </td>
                <td><strong style="color: white;">{{ expo.titre }}</strong></td>
                
                <td style="text-align: center;">
                    <button @click="toggleHomepageExposition(expo.id, expo.is_active)" class="action-btn">
                        <i class="fas fa-home" :style="{ color: expo.is_active ? '#3498db' : '#666', fontSize: '1.2rem' }"></i>
                    </button>
                </td>

                <td>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-icon" @click="editExposition(expo)" title="Modifier"><i class="fas fa-edit"></i></button>
                        <button class="btn-icon" @click="askDelete('exposition', expo.id)" title="Supprimer" style="color: #ff4d4d;"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </td>
            </tr>
            <tr v-if="allExpositions && allExpositions.length === 0">
                <td colspan="5" style="text-align:center; padding: 40px; color: #888;">Aucune exposition créée.</td>
            </tr>
        </tbody>
    </table>
</div>