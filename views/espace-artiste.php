<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="/galerie/galerie/assets/css/artiste-style.css">

<style>
/* Стилі для вкладок та таблиці продажів */
.studio-tabs {
    display: flex;
    gap: 20px;
    margin-top: 40px;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 10px;
}
.studio-tab-btn {
    background: none;
    border: none;
    color: #888;
    font-size: 1.2rem;
    font-family: inherit;
    cursor: pointer;
    padding: 10px 0;
    position: relative;
    transition: color 0.3s;
}
.studio-tab-btn:hover { color: white; }
.studio-tab-btn.active { color: var(--color-gold); font-weight: bold; }
.studio-tab-btn.active::after {
    content: ''; position: absolute; bottom: -11px; left: 0; width: 100%; height: 2px; background: var(--color-gold);
}
.sales-card {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; padding: 20px; margin-bottom: 20px;
    display: flex; gap: 20px; align-items: center;
}
.sales-img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; }
.sales-info { flex: 1; }
.status-badge {
    padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;
}
.status-attente { background: rgba(255,165,0,0.2); color: orange; }
.status-expedie { background: rgba(39,174,96,0.2); color: #27ae60; }
</style>

<div id="studio-app" v-cloak>

    <main>
        <section class="page-hero" style="height: 60vh; justify-content: center; animation: smoothFadeIn 1s ease-out forwards;">
            <div class="page-hero-content" style="transform: translateY(0); text-align: center;">
                <h1 style="font-size: 5rem; letter-spacing: 0.05em; margin-bottom: 15px; animation: smoothFadeIn 0.8s ease-out forwards;">Mon Studio<span class="gold">.</span></h1>
                <p style="color: #888; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 3px; animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0;">
                    Espace créatif de {{ artistProfile ? artistProfile.nom : 'l\'artiste' }}
                </p>
            </div>
        </section>

        <section class="container" style="margin-bottom: 120px; padding-top: 50px;">
            
            <div v-if="isLoading" class="empty-state">
                <div class="loader"></div>
                <p>Ouverture du studio...</p>
            </div>

            <div v-else-if="errorMessage" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
                <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
                <h2>Accès Refusé</h2>
                <p>{{ errorMessage }}</p>
                <button @click="logout" class="btn-primary" style="margin-top: 20px;">Se déconnecter</button>
            </div>

            <div v-else-if="artistProfile" style="animation: fadeInUp 0.8s ease-out forwards;">
                
                <?php include 'includes/artiste/profile.php'; ?>

                <!-- ВКЛАДКИ НАВІГАЦІЇ -->
                <div class="studio-tabs">
                    <button class="studio-tab-btn" :class="{ active: currentTab === 'oeuvres' }" @click="currentTab = 'oeuvres'">
                        <i class="fas fa-palette"></i> Mes Œuvres ({{ myArtworks.length }})
                    </button>
                    <button class="studio-tab-btn" :class="{ active: currentTab === 'ventes' }" @click="currentTab = 'ventes'">
                        <i class="fas fa-shopping-bag"></i> Mes Ventes ({{ mySales.length }})
                    </button>
                </div>

                <!-- ВКЛАДКА 1: КАРТИНИ -->
                <div v-show="currentTab === 'oeuvres'">
                    <?php include 'includes/artiste/artworks.php'; ?>
                </div>

                <!-- ВКЛАДКА 2: ПРОДАЖІ ТА ВІДПРАВКА -->
                <div v-show="currentTab === 'ventes'">
                    <div v-if="mySales.length === 0" class="empty-state" style="border: 1px dashed rgba(255,255,255,0.2);">
                        <i class="fas fa-box-open" style="font-size: 3rem; color: #555; margin-bottom: 15px;"></i>
                        <h3>Aucune vente pour le moment</h3>
                        <p style="color: #888;">Vos futures ventes apparaîtront ici.</p>
                    </div>

                    <div v-else>
                        <h3 style="color: white; margin-bottom: 20px;">Gérer vos expéditions</h3>
                        
                        <div v-for="sale in mySales" :key="sale.id" class="sales-card">
                            <img :src="sale.oeuvres?.image_url || 'https://via.placeholder.com/80'" class="sales-img" alt="Artwork">
                            <div class="sales-info">
                                <h4 style="color: white; margin: 0 0 5px 0;">{{ sale.oeuvres?.titre || 'Œuvre supprimée' }}</h4>
                                <p style="color: #aaa; font-size: 0.9rem; margin: 0 0 5px 0;">
                                    <strong>Client :</strong> {{ sale.nom_client }} ({{ sale.user_email }})
                                </p>
                                <p style="color: var(--color-gold); font-weight: bold; margin: 0;">{{ sale.montant_total }} €</p>
                                
                                <p v-if="sale.adresse_livraison" style="color: #aaa; font-size: 0.9rem; margin-top: 10px; padding: 10px; background: rgba(0,0,0,0.2); border-radius: 4px;">
                                    <strong><i class="fas fa-map-marker-alt"></i> Adresse d'expédition :</strong><br>
                                    {{ sale.adresse_livraison }}
                                </p>
                                <p v-else style="color: #ff9f43; font-size: 0.9rem; margin-top: 10px;">
                                    <i class="fas fa-exclamation-circle"></i> Adresse non spécifiée. Veuillez contacter la galerie.
                                </p>
                            </div>
                            
                            <div style="text-align: right; min-width: 150px;">
                                <div style="margin-bottom: 15px;">
                                    <span v-if="sale.status === 'en_attente'" class="status-badge status-attente">À expédier</span>
                                    <span v-else-if="sale.status === 'expedie'" class="status-badge status-expedie">Expédié</span>
                                </div>

                                <button v-if="sale.status === 'en_attente'" @click="markAsShipped(sale)" class="btn-primary" style="padding: 8px 15px; font-size: 0.9rem; border-radius: 4px;">
                                    <i class="fas fa-truck"></i> Expédier
                                </button>
                                
                                <div v-else-if="sale.tracking_number" style="background: rgba(255,255,255,0.1); padding: 8px; border-radius: 4px; display: inline-block;">
                                    <p style="color: #888; font-size: 0.8rem; margin: 0;">N° de suivi :</p>
                                    <strong style="color: white;">{{ sale.tracking_number }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <?php include 'includes/artiste/modals.php'; ?>

</div>

<script src="/galerie/assets/js/animations.js"></script>
<script src="/galerie/assets/js/espace-artiste.js"></script>

<?php include 'includes/footer.php'; ?>