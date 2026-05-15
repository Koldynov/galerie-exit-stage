<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="assets/css/style.css">

<main id="client-dashboard" class="account-page" v-cloak>
    
    <div v-if="isLoading" class="client-loading">
        <div class="loader"></div>
    </div>

    <div v-else-if="!currentUser" class="error-state client-centered">
        <i class="far fa-user-circle" style="font-size: 4rem; color: var(--color-gold); margin-bottom: 20px;"></i>
        <p style="margin-bottom: 20px; color: white; font-size: 1.2rem;">Vous devez être connecté pour voir cette page.</p>
        <a href="portail-artiste" class="btn-primary">Aller à la connexion</a>
    </div>

    <div v-else class="client-wrapper">
        
        <?php include 'includes/client/sidebar.php'; ?>

        <section class="client-content">
            
            <div v-if="currentTab === 'profil'" class="fade-in">
                <?php include 'includes/client/tab-profil.php'; ?>
            </div>

            <div v-if="currentTab === 'favoris'" class="fade-in">
                <?php include 'includes/client/tab-favoris.php'; ?>
            </div>

            <div v-if="currentTab === 'artistes-suivis'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/client/tab-artistes-suivis.php'; ?>
            </div>

            <div v-if="currentTab === 'demandes'" class="fade-in">
                <?php include 'includes/client/tab-demandes.php'; ?>
            </div>

            <div v-if="currentTab === 'achats'" class="fade-in">
                <?php include 'includes/client/tab-achats.php'; ?>
            </div>

        </section>
    </div>
</main>

<script src="assets/js/mon-compte.js"></script>

<?php include 'includes/footer.php'; ?>