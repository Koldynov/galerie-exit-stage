<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="/galerie/assets/css/admin-style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Кастомний скролбар для всієї адмінки */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2); 
    }
    ::-webkit-scrollbar-thumb {
        background: #333; 
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: var(--color-gold, #d4af37); 
    }
</style>

<main id="admin-dashboard" v-cloak>
    
    <div v-if="isAuthenticating" style="height: 100vh; display: flex; justify-content: center; align-items: center; background: #0a0a0a;">
        <div class="loader"></div>
    </div>

    <div v-else class="admin-wrapper">
        
        <?php include 'includes/admin/sidebar.php'; ?>

        <section class="admin-content">
            
            <div v-show="currentTab === 'overview'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-overview.php'; ?>
            </div>

            <div v-if="currentTab === 'candidatures'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-candidatures.php'; ?>
            </div>

            <div v-if="currentTab === 'commandes'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-commandes.php'; ?>
            </div>

            <div v-if="currentTab === 'demandes'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-kanban.php'; ?>
            </div>

            <div v-if="currentTab === 'oeuvres'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-oeuvres.php'; ?>
            </div>

            <div v-if="currentTab === 'expositions'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-expositions.php'; ?>
            </div>

            <div v-if="currentTab === 'artistes'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-artistes.php'; ?>
            </div>

            <div v-if="currentTab === 'journal'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-journal.php'; ?>
            </div>

            <div v-if="currentTab === 'messages'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-messages.php'; ?>
            </div>

            <div v-if="currentTab === 'ateliers'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-ateliers.php'; ?>
            </div>

            <div v-if="currentTab === 'evenements'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-evenements.php'; ?>
            </div>

            <div v-if="currentTab === 'newsletter'" style="animation: fadeInUp 0.4s ease forwards;">
                <?php include 'includes/admin/tab-newsletter.php'; ?>
            </div>

        </section> 

        <?php include 'includes/admin/modals.php'; ?>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script src="/galerie/assets/js/admin-dashboard.js"></script>

<?php include 'includes/footer.php'; ?>