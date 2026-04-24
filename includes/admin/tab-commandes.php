<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
    <div>
        <h1 class="admin-header-title" style="margin-bottom: 5px;">Ventes & Transactions</h1>
        <p class="admin-subtitle">Historique des achats directs</p>
    </div>
    
    <button @click="exportToCSV" class="btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; font-size: 0.9rem;">
        <i class="fas fa-file-excel" style="font-size: 1.2rem;"></i> 
        EXPORTER EN CSV
    </button>
</div>

<div class="admin-table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Œuvre</th>
                <th>Montant</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="cmd in paginatedCommandes" :key="cmd.id">
                <td style="color: #aaa; font-size: 0.85rem;">{{ formatDate(cmd.created_at) }}</td>
                <td>
                    <strong style="color: white; display: block;">{{ cmd.nom_client }}</strong>
                    <span style="font-size: 0.8rem; color: #888;">{{ cmd.user_email }}</span>
                </td>
                <td>{{ cmd.oeuvres?.titre || 'Œuvre supprimée' }}</td>
                <td style="font-weight: bold; color: var(--color-gold);">{{ cmd.montant_total }} €</td>
                <td><span class="status-badge success"><i class="fas fa-check"></i> {{ cmd.status }}</span></td>
            </tr>
            <tr v-if="allCommandes.length === 0">
                <td colspan="5" style="text-align: center; padding: 30px; color: #888;">Aucune commande pour le moment.</td>
            </tr>
        </tbody>
    </table>
</div>

<div v-if="totalPagesCommandes > 1" class="pagination-controls">
    <button @click="prevPageCommandes" :disabled="currentPageCommandes === 1" class="pagination-btn">
        <i class="fas fa-chevron-left"></i> Précédent
    </button>
    <span class="pagination-info">Page {{ currentPageCommandes }} sur {{ totalPagesCommandes }}</span>
    <button @click="nextPageCommandes" :disabled="currentPageCommandes === totalPagesCommandes" class="pagination-btn">
        Suivant <i class="fas fa-chevron-right"></i>
    </button>
</div>