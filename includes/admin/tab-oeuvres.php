<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 class="admin-header-title">Inventaire Œuvres</h1>
        <p class="admin-subtitle">Gestion de tout le catalogue</p>
    </div>
</div>
<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Aperçu</th>
                <th>Titre</th>
                <th>Artiste</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Statut</th>
                <th style="text-align: center;">À la une</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="oeuvre in paginatedOeuvres" :key="oeuvre.id">
                <td><img :src="oeuvre.image_url" class="admin-thumbnail" loading="lazy" decoding="async"></td>
                <td><strong style="color: white;">{{ oeuvre.titre }}</strong></td>
                <td>{{ oeuvre.artistes?.nom || 'Inconnu' }}</td>
                <td>{{ oeuvre.categorie }}</td>
                <td>{{ oeuvre.prix }} €</td>
                <td>
                    <select v-model="oeuvre.statut" @change="updateStatut(oeuvre.id, oeuvre.statut)" class="admin-input" style="padding: 6px; font-size: 0.8rem; width: auto; min-width: 100px;">
                        <option value="disponible">En vente</option>
                        <option value="vendu">Vendu</option>
                    </select>
                </td>
                <td style="text-align: center;">
                    <button @click="toggleFeatured('oeuvre', oeuvre.id, oeuvre.is_featured)" class="action-btn">
                        <i :class="oeuvre.is_featured ? 'fas fa-star' : 'far fa-star'" :style="{ color: oeuvre.is_featured ? 'var(--color-gold)' : '#666', fontSize: '1.2rem' }"></i>
                    </button>
                </td>
                <td style="white-space: nowrap;">
                    <a :href="'oeuvre/' + oeuvre.id" target="_blank" class="action-btn"><i class="fas fa-eye"></i></a>
                    <button @click="askDelete('oeuvre', oeuvre.id)" class="action-btn delete"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="pagination-controls" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
    <button @click="prevPageOeuvres" :disabled="currentPageOeuvres === 1" class="btn-secondary" style="padding: 10px 20px;">Précédent</button>
    <span style="color: #888;">Page {{ currentPageOeuvres }} sur {{ totalPagesOeuvres }}</span>
    <button @click="nextPageOeuvres" :disabled="currentPageOeuvres === totalPagesOeuvres" class="btn-secondary" style="padding: 10px 20px;">Suivant</button>
</div>