<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
    <div>
        <h1 class="admin-header-title" style="margin-bottom: 5px;">Ventes & Expéditions</h1>
        <p class="admin-subtitle">Contrôle des transactions et de la logistique des artistes</p>
    </div>
    
    <button @click="exportToCSV" class="btn-primary" style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; font-size: 0.9rem;">
        <i class="fas fa-file-excel" style="font-size: 1.2rem;"></i> 
        EXPORTER EN CSV
    </button>
</div>

<div class="admin-table-container">
    <table class="admin-table" style="min-width: 1000px;">
        <thead>
            <tr>
                <th style="width: 10%;">Date / Montant</th>
                <th style="width: 25%;">Client & Adresse</th>
                <th style="width: 25%;">Œuvre & Artiste</th>
                <th style="width: 20%;">Paiement</th>
                <th style="width: 20%;">Statut d'Expédition</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="cmd in paginatedCommandes" :key="cmd.id">
                
                <!-- Колонки 1: Дати і Гроші -->
                <td>
                    <div style="color: #aaa; font-size: 0.85rem; margin-bottom: 5px;">{{ formatDate(cmd.created_at) }}</div>
                    <div style="font-weight: bold; color: var(--color-gold); font-size: 1.1rem;">{{ cmd.montant_total }} €</div>
                </td>
                
                <!-- Колонка 2: Клієнт і Адреса -->
                <td>
                    <strong style="color: white; display: block; font-size: 1.05rem;">{{ cmd.nom_client }}</strong>
                    <a :href="'mailto:' + cmd.user_email" style="font-size: 0.85rem; color: #3498db; text-decoration: none; display: block; margin-bottom: 10px;">{{ cmd.user_email }}</a>
                    
                    <div v-if="cmd.adresse_livraison" style="background: rgba(255,255,255,0.05); padding: 8px 10px; border-radius: 4px; border-left: 2px solid var(--color-gold);">
                        <span style="display: block; font-size: 0.75rem; color: #888; text-transform: uppercase; margin-bottom: 3px;">Expédier à :</span>
                        <span style="color: #ddd; font-size: 0.85rem; line-height: 1.4; white-space: pre-wrap;">{{ cmd.adresse_livraison }}</span>
                    </div>
                    <span v-else style="color: #e74c3c; font-size: 0.8rem;"><i class="fas fa-exclamation-triangle"></i> Adresse manquante</span>
                </td>

                <!-- Колонка 3: Картина і Художник -->
                <td>
                    <strong style="color: white; display: block; margin-bottom: 5px;">{{ cmd.oeuvres?.titre || 'Œuvre introuvable' }}</strong>
                    <span style="font-size: 0.85rem; color: #888;">
                        <i class="fas fa-paint-brush" style="margin-right: 5px; color: #555;"></i> 
                        {{ cmd.oeuvres?.artistes?.nom || 'Artiste inconnu' }}
                    </span>
                </td>
                
                <!-- Колонка 4: Статус оплати -->
                <td>
                    <span class="status-badge success" style="margin-bottom: 5px;"><i class="fas fa-check-circle"></i> Payé (Stripe)</span>
                </td>

                <!-- Колонка 5: Статус відправки (Єжові рукавиці) -->
                <td>
                    <!-- Якщо в очікуванні -->
                    <div v-if="!cmd.statut_livraison || cmd.statut_livraison === 'en_attente'" style="background: rgba(243, 156, 18, 0.1); border: 1px solid rgba(243, 156, 18, 0.3); padding: 8px 12px; border-radius: 6px;">
                        <span style="color: #f39c12; font-size: 0.85rem; font-weight: bold; display: block; margin-bottom: 4px;">
                            <i class="fas fa-hourglass-half"></i> En attente d'expédition
                        </span>
                        <span style="color: #888; font-size: 0.75rem;">L'artiste doit envoyer le colis.</span>
                    </div>

                    <!-- Якщо відправлено -->
                    <div v-else-if="cmd.statut_livraison === 'expedie'" style="background: rgba(46, 204, 113, 0.1); border: 1px solid rgba(46, 204, 113, 0.3); padding: 8px 12px; border-radius: 6px;">
                        <span style="color: #2ecc71; font-size: 0.85rem; font-weight: bold; display: block; margin-bottom: 4px;">
                            <i class="fas fa-truck"></i> Expédié par l'artiste
                        </span>
                        <div v-if="cmd.tracking_number" style="margin-top: 5px; padding-top: 5px; border-top: 1px solid rgba(46, 204, 113, 0.2);">
                            <span style="color: #aaa; font-size: 0.75rem;">N° Suivi : </span>
                            <strong style="color: white; font-size: 0.85rem; letter-spacing: 1px;">{{ cmd.tracking_number }}</strong>
                        </div>
                    </div>
                </td>

            </tr>
            
            <tr v-if="allCommandes.length === 0">
                <td colspan="5" style="text-align: center; padding: 40px; color: #888;">
                    <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px; display: block; color: #444;"></i>
                    Aucune vente enregistrée pour le moment.
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div v-if="totalPagesCommandes > 1" class="pagination-controls" style="margin-top: 20px;">
    <button @click="prevPageCommandes" :disabled="currentPageCommandes === 1" class="pagination-btn">
        <i class="fas fa-chevron-left"></i> Précédent
    </button>
    <span class="pagination-info">Page {{ currentPageCommandes }} sur {{ totalPagesCommandes }}</span>
    <button @click="nextPageCommandes" :disabled="currentPageCommandes === totalPagesCommandes" class="pagination-btn">
        Suivant <i class="fas fa-chevron-right"></i>
    </button>
</div>