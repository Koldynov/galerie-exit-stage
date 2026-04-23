<?php include 'includes/header.php'; ?>
<script>window.CURRENT_ITEM_ID = "<?= $item_id ?>";</script>

<link rel="stylesheet" href="/galerie/galerie/assets/css/oeuvre-style.css">


<main id="oeuvre-app" v-cloak style="padding-top: 120px; padding-bottom: 100px; min-height: 100vh;">
    
    <div v-if="isLoading" class="empty-state" style="height: 60vh; border: none;">
        <div class="loader"></div>
        <p>Préparation de l'œuvre...</p>
    </div>

    <div v-else-if="errorMessage" class="empty-state" style="height: 60vh; border: none; animation: fadeInUp 0.5s forwards;">
        <i class="fas fa-unlink" style="font-size: 4rem; color: var(--color-gold); margin-bottom: 20px;"></i>
        <h2>Œuvre Introuvable</h2>
        <p>{{ errorMessage }}</p>
        <a href="/galerie/peintures" class="btn-primary" style="margin-top: 20px;">Retour à la galerie</a>
    </div>

    <div v-else-if="oeuvre" class="oeuvre-editorial" style="animation: fadeInUp 0.8s ease-out forwards;">
        
        <a href="javascript:history.back()" class="back-link" style="margin-bottom: 30px; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Retour
        </a>

        <div class="oeuvre-header-center">
            <span class="oeuvre-cat-label">{{ oeuvre.categorie }}</span>
            <h1 class="oeuvre-title-huge">{{ oeuvre.titre }}</h1>
            
            <a :href="'/galerie/artiste/' + oeuvre.artiste_id" class="oeuvre-artist-link" v-if="oeuvre.artistes">
                Une œuvre de <span class="gold">{{ oeuvre.artistes.nom }}</span>
            </a>
        </div>

        <div class="oeuvre-hero-img-container">
            <img :src="oeuvre.image_url" :alt="oeuvre.titre">
        </div>

        <div class="oeuvre-story-center">
            <p>{{ oeuvre.description || 'L\'artiste n\'a pas fourni de description pour cette œuvre.' }}</p>
        </div>

        <div class="oeuvre-purchase-footer">
            <span class="oeuvre-final-price">
                {{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Prix sur demande' }}
            </span>
            
            <div class="oeuvre-small-actions">
                <button v-if="!oeuvre.statut || oeuvre.statut !== 'vendu'" @click="handleAcquisition" class="btn-acquire-small">
                    {{ oeuvre.prix > 0 ? 'Acheter maintenant' : 'Demander le prix' }}
                </button>
                
                <div v-else class="sold-out-badge" style="flex: 1;">
                    <i class="fas fa-check-circle"></i> VENDU
                </div>
                
                <button @click="toggleFavori" class="btn-fav-small" :style="{ color: isFavori ? 'var(--color-red-heart)' : '' }" title="Ajouter aux favoris">
                    <i :class="isFavori ? 'fas fa-heart' : 'far fa-heart'"></i>
                </button>
            </div>
            
            <p style="color: #555; font-size: 0.7rem; margin-top: 20px; text-transform: uppercase; letter-spacing: 1px;">
                Certificat d'authenticité inclus
            </p>
        </div>
    </div>

    <div v-if="showAcquireModal" class="inquiry-modal-overlay" @click.self="closeModal">
        <div class="inquiry-modal-content">
            <div v-if="!isSuccess">
                <button @click="closeModal" class="close-modal-btn"><i class="fas fa-times"></i></button>
                <h2 style="font-size: 1.8rem; margin-bottom: 10px; color: white;">Demande d'acquisition</h2>
                
                <div v-if="currentUser" style="margin-bottom: 25px;">
                    <p style="color: #888; font-size: 0.95rem; line-height: 1.5;">
                        Vous êtes connecté en tant que <strong style="color: var(--color-gold);">{{ currentUserName }}</strong>.
                        Votre demande sera transmise à la galerie.
                    </p>
                </div>
                <div v-else>
                    <div class="form-group"><label>Nom *</label><input v-model="inquiryForm.nom" type="text" class="inquiry-input"></div>
                    <div class="form-group"><label>Email *</label><input v-model="inquiryForm.email" type="email" class="inquiry-input"></div>
                </div>
                <div class="form-group" style="margin-bottom: 30px;">
                    <label>Message</label>
                    <textarea v-model="inquiryForm.message" rows="4" class="inquiry-input"></textarea>
                </div>

                <button @click="submitInquiry" class="btn-primary" style="width: 100%; padding: 15px;" :disabled="isSubmitting">
                    <span v-if="isSubmitting">Envoi...</span><span v-else>Envoyer la demande</span>
                </button>
            </div>
            <div v-else style="text-align: center; padding: 30px 0;">
                <i class="far fa-check-circle" style="font-size: 5rem; color: var(--color-gold); margin-bottom: 20px;"></i>
                <h2 style="color: white; margin-bottom: 15px;">Demande envoyée !</h2>
                <button @click="closeModal" class="btn-primary">Fermer</button>
            </div>
        </div>
    </div>
</main>

<script src="/galerie/assets/js/oeuvre.js"></script>

<?php include 'includes/footer.php'; ?>