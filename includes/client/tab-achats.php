<h2 class="client-title" style="margin-bottom: 30px;">Mes Achats</h2>

<div v-if="commandesList.length === 0" class="client-empty-box">
    <i class="fas fa-shopping-bag"></i>
    <p>Aucun achat effectué pour le moment.</p>
</div>

<div v-else class="list-container">
    <div v-for="cmd in commandesList" :key="cmd.id" class="list-item purchase-item">
        <div class="item-content">
            <div class="item-info" style="flex: 1;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span class="order-id">Commande #{{ cmd.id.substring(0,8).toUpperCase() }}</span>
                    <span class="item-date">{{ formatDate(cmd.created_at) }}</span>
                </div>
                
                <h4 style="margin-bottom: 5px;">{{ cmd.oeuvres?.titre || 'Œuvre' }}</h4>
                <p class="price-paid">{{ cmd.montant_total }} € <span style="color: #888; font-size: 0.8rem; font-weight: normal;">(Payé)</span></p>
                
                <div class="purchase-actions" style="margin-top: 15px; display: flex; gap: 10px;">
                    <a :href="'facture/' + cmd.id" class="btn-outline-small" target="_blank">
                        <i class="fas fa-file-invoice-dollar"></i> Facture
                    </a>
                    <a :href="'certificat/' + cmd.oeuvre_id" class="btn-outline-small" target="_blank">
                        <i class="fas fa-certificate"></i> Certificat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>