<h1 class="admin-header-title">Gestion Artistes (Approuvés)</h1>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Avatar</th>
                <th>Nom de l'artiste</th>
                <th>Titre Pro</th>
                <th style="text-align: center;">À la une</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="artiste in approvedArtists" :key="artiste.id">
                <td><img :src="artiste.image_url || 'assets/img/default-artist.jpg'" class="admin-thumbnail" style="border-radius: 50%;"></td>
                <td><strong style="color: white;">{{ artiste.nom }}</strong></td>
                <td>{{ artiste.titre_pro || '-' }}</td>
                <td style="text-align: center;">
                    <button @click="toggleFeatured('artiste', artiste.id, artiste.is_featured)" class="action-btn">
                        <i :class="artiste.is_featured ? 'fas fa-star' : 'far fa-star'" :style="{ color: artiste.is_featured ? 'var(--color-gold)' : '#666', fontSize: '1.2rem' }"></i>
                    </button>
                </td>
                <td>
                    <button @click="openMessageModal(artiste)" class="action-btn" style="color: #3498db;" title="Envoyer un message"><i class="fas fa-comment-dots"></i></button>
                    <a :href="'artiste/' + artiste.id" target="_blank" class="action-btn"><i class="fas fa-eye"></i></a>
                    <button @click="askDelete('artiste', artiste.id)" class="action-btn delete"><i class="fas fa-user-times"></i></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>