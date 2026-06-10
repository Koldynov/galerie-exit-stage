<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="/assets/css/artiste-style.css?v=<?php echo time(); ?>">

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

                <div class="studio-tabs">
                    <button class="studio-tab-btn" :class="{ active: currentTab === 'oeuvres' }" @click="currentTab = 'oeuvres'">
                        <i class="fas fa-palette"></i> Mes Œuvres ({{ myArtworks.length }})
                    </button>
                    <button class="studio-tab-btn" :class="{ active: currentTab === 'ventes' }" @click="currentTab = 'ventes'">
                        <i class="fas fa-shopping-bag"></i> Mes Ventes ({{ mySales.length }})
                    </button>
                    <button class="studio-tab-btn" @click="openMessagesModal">
                        <i class="fas fa-envelope"></i> Messages
                        <span v-if="unreadMessagesCount > 0" style="background: var(--color-red-heart); color: white; border-radius: 50%; padding: 2px 7px; font-size: 0.75rem; margin-left: 8px; font-weight: bold;">{{ unreadMessagesCount }}</span>
                    </button>
                </div>

                <div v-show="currentTab === 'oeuvres'">
                    <?php include 'includes/artiste/artworks.php'; ?>
                </div>

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
                                    <span v-if="sale.status !== 'expedie'" class="status-badge status-attente">À expédier</span>
                                    <span v-else-if="sale.status === 'expedie'" class="status-badge status-expedie">Expédié</span>
                                </div>

                                <button v-if="sale.status !== 'expedie'" @click="markAsShipped(sale)" class="btn-primary" style="padding: 8px 15px; font-size: 0.9rem; border-radius: 4px;">
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

<script src="/assets/js/animations.js"></script>
<script src="/assets/js/espace-artiste.js"></script>

<?php include 'includes/footer.php'; ?>