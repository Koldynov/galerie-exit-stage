<h1 class="admin-header-title">Vue d'ensemble</h1>
<p class="admin-subtitle">Statistiques en temps réel de la galerie</p>

<div class="admin-grid grid-4" style="margin-bottom: 40px;">
    <div class="admin-card" style="border-left: 3px solid #2ecc71;">
        <i class="fas fa-wallet stat-icon" style="color: #2ecc71;"></i>
        <p class="stat-label">Revenus Réalisés</p>
        <p class="stat-number">{{ realRevenue }} €</p>
    </div>
    <div class="admin-card">
        <i class="fas fa-user-check stat-icon"></i>
        <p class="stat-label">Artistes Approuvés</p>
        <p class="stat-number">{{ approvedArtists.length }}</p>
    </div>
    <div class="admin-card" style="border-left: 3px solid var(--color-gold);">
        <i class="fas fa-user-plus stat-icon" style="color: var(--color-gold);"></i>
        <p class="stat-label">Candidatures</p>
        <p class="stat-number" style="color: var(--color-gold);">{{ pendingArtists.length }}</p>
    </div>
    <div class="admin-card">
        <i class="fas fa-check-circle stat-icon"></i>
        <p class="stat-label">Œuvres Vendues</p>
        <p class="stat-number">{{ soldOeuvresCount }}</p>
    </div>
</div>

<div class="admin-card" style="margin-bottom: 40px; position: relative;">
    <h3 style="margin-bottom: 15px;">Évolution des Ventes (7 derniers jours)</h3>
    <div style="height: 300px;">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<div class="admin-grid grid-2">
    <div class="admin-card" style="padding: 0;">
        <h3 style="padding: 25px; border-bottom: 1px solid rgba(255,255,255,0.05);">Dernières Ventes</h3>
        <table class="admin-table">
            <tbody>
                <tr v-for="cmd in allCommandes.slice(0, 5)" :key="cmd.id">
                    <td>
                        <strong style="color: var(--color-white); display: block;">{{ cmd.nom_client }}</strong>
                        <span style="font-size: 0.8rem; color: #888;">{{ formatDate(cmd.created_at) }}</span>
                    </td>
                    <td>{{ cmd.oeuvres?.titre || 'Œuvre' }}</td>
                    <td style="color: #2ecc71; font-weight: bold;">+{{ cmd.montant_total }} €</td>
                </tr>
                <tr v-if="allCommandes.length === 0">
                    <td colspan="3" style="text-align: center; padding: 20px; color: #666;">Aucune vente pour le moment.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="admin-card">
        <h3 style="margin-bottom: 20px;">Répartition du Catalogue</h3>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span>Peintures</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Peinture') }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span>Sculptures</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Sculpture') }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span>Photographies</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Photographie') }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <span>Autres</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Autre') }}</strong>
            </div>
        </div>
    </div>
</div>

<div style="background: rgba(255,255,255,0.02); padding: 20px; border-radius: 8px; border: 1px solid rgba(212,175,55,0.3); margin-top: 40px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h3 style="color: var(--color-gold); margin-bottom: 5px;">Section "Philosophie"</h3>
        <p style="color: #aaa; font-size: 0.85rem;">Modifiez le texte et l'image de la section Philosophie sur la page d'accueil de la galerie.</p>
    </div>
    <button @click="openPhiloModal" class="btn-secondary" style="padding: 10px 20px;">
        <i class="fas fa-edit"></i> Modifier
    </button>
</div>