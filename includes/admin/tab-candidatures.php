<h1 class="admin-header-title">Nouvelles Candidatures</h1>
<p class="admin-subtitle">Artistes en attente d'approbation</p>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr><th>Avatar</th><th>Nom de l'artiste</th><th>Titre Pro</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <tr v-for="artiste in pendingArtists" :key="artiste.id">
                <td><img :src="artiste.image_url || 'assets/img/default-artist.jpg'" class="admin-thumbnail" style="border-radius: 50%;"></td>
                <td><strong style="color: white;">{{ artiste.nom }}</strong></td>
                <td>{{ artiste.titre_pro || '-' }}</td>
                <td>
                    <button @click="approveArtist(artiste.id)" class="action-btn" style="color: #2ecc71; margin-right: 10px;" title="Accepter"><i class="fas fa-check"></i> Accepter</button>
                    <button @click="askDelete('artiste', artiste.id)" class="action-btn delete" title="Rejeter"><i class="fas fa-times"></i> Rejeter</button>
                </td>
            </tr>
            <tr v-if="pendingArtists.length === 0">
                <td colspan="4" style="text-align: center; padding: 40px; color: #888;">Aucune nouvelle candidature pour le moment.</td>
            </tr>
        </tbody>
    </table>
</div>