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

    <teleport to="body">
    <div v-if="showAcquireModal" @click.self="closeModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.85); z-index: 999999; display: flex; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
        
        <div style="background: #0a0a0a; border: 1px solid var(--color-gold, #d4af37); padding: 40px; border-radius: 4px; width: 90%; max-width: 500px; position: relative; box-shadow: 0 15px 40px rgba(0,0,0,0.8); animation: slideUp 0.3s ease-out; max-height: 90vh; overflow-y: auto;">
            
            <button @click="closeModal" style="position: absolute; top: 15px; right: 20px; background: none; border: none; color: #888; font-size: 1.5rem; cursor: pointer; transition: 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#888'">
                <i class="fas fa-times"></i>
            </button>

            <div v-if="isSuccess" style="text-align: center; padding: 20px 0;">
                <i class="fas fa-check-circle" style="font-size: 3.5rem; color: #27ae60; margin-bottom: 20px;"></i>
                <h2 style="color: white; margin-bottom: 10px; font-weight: 300;">Demande envoyée !</h2>
                <p style="color: #aaa; margin-bottom: 25px;">Sabrina vous contactera très prochainement avec toutes les informations.</p>
                <button @click="closeModal" class="btn-primary" style="width: 100%; padding: 12px;">Fermer</button>
            </div>

            <div v-else>
                <h2 style="color: var(--color-gold, #d4af37); margin-bottom: 5px; font-family: 'Helvetica Neue', sans-serif; font-weight: 400; font-size: 1.8rem;">Demande d'acquisition</h2>
                
                <p v-if="currentUser" style="color: #888; font-size: 0.9rem; margin-bottom: 25px;">
                    Vous êtes connecté en tant que <strong style="color: white;">{{ currentUserName }}</strong>. Votre demande sera transmise à la galerie.
                </p>
                <p v-else style="color: #888; font-size: 0.9rem; margin-bottom: 25px;">
                    Veuillez remplir vos coordonnées pour être recontacté par notre galerie.
                </p>

                <div v-if="!currentUser">
                    <div style="margin-bottom: 15px;">
                        <input type="text" v-model="inquiryForm.nom" placeholder="Votre nom complet" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 14px 15px; color: white; border-radius: 4px; outline: none; font-size: 1rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#d4af37'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <input type="email" v-model="inquiryForm.email" placeholder="Votre adresse email" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 14px 15px; color: white; border-radius: 4px; outline: none; font-size: 1rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#d4af37'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <textarea v-model="inquiryForm.message" rows="4" placeholder="Votre message ou question (optionnel)..." style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 14px 15px; color: white; border-radius: 4px; outline: none; resize: vertical; font-family: inherit; font-size: 1rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#d4af37'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"></textarea>
                </div>

                <div v-if="formError" style="color: #ff4d4d; font-size: 0.9rem; margin-bottom: 15px; text-align: center; background: rgba(255,77,77,0.1); padding: 10px; border-radius: 4px;">
                    <i class="fas fa-exclamation-circle"></i> {{ formError }}
                </div>

                <button @click="submitInquiry" class="btn-primary" style="width: 100%; padding: 14px; font-weight: bold; letter-spacing: 1px; font-size: 1rem;" :disabled="isSubmitting">
                    <span v-if="isSubmitting"><i class="fas fa-spinner fa-spin"></i> Envoi en cours...</span>
                    <span v-else>ENVOYER LA DEMANDE</span>
                </button>
            </div>

        </div>
    </div>
</teleport>
</main>

<script src="/galerie/assets/js/oeuvre.js"></script>

<?php include 'includes/footer.php'; ?>