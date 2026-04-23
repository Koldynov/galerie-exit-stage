<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="/galerie/galerie/assets/css/artiste-style.css">

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

                <?php include 'includes/artiste/artworks.php'; ?>

            </div>
        </section>
    </main>

    <?php include 'includes/artiste/modals.php'; ?>

</div>

<script src="/galerie/assets/js/animations.js"></script>
<script src="/galerie/assets/js/espace-artiste.js"></script>

<?php include 'includes/footer.php'; ?>