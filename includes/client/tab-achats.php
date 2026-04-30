<div class="client-dashboard-section">
    <div class="section-header" style="margin-bottom: 30px;">
        <h2 style="font-size: 2rem; margin: 0; color: white;">Mes <span class="gold">Acquisitions</span></h2>
        <p style="color: #888; margin-top: 10px;">Suivez l'état d'expédition de vos œuvres d'art.</p>
    </div>

    <div v-if="commandesList.length === 0" class="empty-state" style="border: 1px dashed rgba(212, 175, 55, 0.2); padding: 50px 20px;">
        <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #555; margin-bottom: 15px;"></i>
        <h3 style="font-size: 1.3rem; margin-bottom: 10px;">Aucune acquisition</h3>
        <p style="color: #888; margin-bottom: 20px;">Vous n'avez pas encore acquis d'œuvres sur La Galerie EXIT.</p>
        <a href="/galerie/peintures" class="btn-primary" style="padding: 10px 20px; font-size: 0.9rem;">Parcourir le catalogue</a>
    </div>

    <div v-else class="achats-list" style="display: flex; flex-direction: column; gap: 20px;">
        <div v-for="cmd in commandesList" :key="cmd.id" class="achat-card" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 20px; display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
            
            <img :src="cmd.oeuvres?.image_url || 'https://via.placeholder.com/100'" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;" alt="Œuvre">
            
            <div style="flex: 1; min-width: 200px;">
                <h3 style="color: white; margin: 0 0 5px 0; font-size: 1.2rem;">{{ cmd.oeuvres?.titre || 'Œuvre introuvable' }}</h3>
                <p style="color: var(--color-gold); font-size: 0.9rem; margin: 0 0 10px 0;">
                    <i class="fas fa-paint-brush"></i> Artiste : {{ cmd.oeuvres?.artistes?.nom || 'Inconnu' }}
                </p>
                <p style="color: #888; font-size: 0.85rem; margin: 0;">Date d'achat : {{ formatDate(cmd.created_at) }}</p>
                <p style="color: white; font-weight: bold; margin: 5px 0 0 0;">{{ cmd.montant_total }} €</p>
                
                <div class="purchase-actions" style="margin-top: 15px; display: flex; gap: 10px;">
                    <a :href="'facture/' + cmd.id" class="btn-outline-small" target="_blank" style="padding: 5px 10px; font-size: 0.8rem; border: 1px solid #555; color: #ddd; text-decoration: none; border-radius: 4px;">
                        <i class="fas fa-file-invoice-dollar"></i> Facture
                    </a>
                    <a :href="'certificat/' + cmd.oeuvre_id" class="btn-outline-small" target="_blank" style="padding: 5px 10px; font-size: 0.8rem; border: 1px solid #555; color: #ddd; text-decoration: none; border-radius: 4px;">
                        <i class="fas fa-certificate"></i> Certificat
                    </a>
                </div>
            </div>

            <div style="min-width: 250px; text-align: right; border-left: 1px solid rgba(255,255,255,0.1); padding-left: 20px;">
                
                <!-- Якщо художник ще НЕ відправив -->
                <div v-if="!cmd.statut_livraison || cmd.statut_livraison === 'en_attente'">
                    <div style="display: inline-block; background: rgba(243, 156, 18, 0.1); border: 1px solid rgba(243, 156, 18, 0.3); padding: 8px 15px; border-radius: 20px;">
                        <span style="color: #f39c12; font-size: 0.9rem; font-weight: bold;">
                            <i class="fas fa-box"></i> En cours de préparation
                        </span>
                    </div>
                    <p style="color: #aaa; font-size: 0.8rem; margin-top: 10px; line-height: 1.4; text-align: left;">
                        L'artiste a été notifié et prépare actuellement votre œuvre pour l'expédition.
                    </p>
                </div>

                <!-- Якщо художник ВЖЕ відправив -->
                <div v-else-if="cmd.statut_livraison === 'expedie'">
                    <div style="display: inline-block; background: rgba(46, 204, 113, 0.1); border: 1px solid rgba(46, 204, 113, 0.3); padding: 8px 15px; border-radius: 20px;">
                        <span style="color: #2ecc71; font-size: 0.9rem; font-weight: bold;">
                            <i class="fas fa-truck"></i> Colis expédié
                        </span>
                    </div>
                    <div v-if="cmd.tracking_number" style="margin-top: 15px; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 4px; text-align: left;">
                        <span style="color: #888; font-size: 0.8rem; text-transform: uppercase;">Numéro de suivi :</span>
                        <strong style="color: white; display: block; font-size: 1.1rem; letter-spacing: 2px; margin-top: 5px;">{{ cmd.tracking_number }}</strong>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>