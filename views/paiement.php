<?php include 'includes/header.php'; ?>
<script>window.CURRENT_ITEM_ID = "<?= $item_id ?>";</script>

<link rel="stylesheet" href="assets/css/paiement-style.css">

<main id="paiement-app" v-cloak style="padding-top: 150px; padding-bottom: 100px; min-height: 100vh;">
    <div class="container" style="max-width: 900px;">
        
        <div v-if="isLoading" class="empty-state" style="border: none;"><div class="loader"></div><p>Chargement...</p></div>
        
        <div v-else-if="oeuvre" style="display: flex; gap: 50px; animation: fadeInUp 0.5s forwards; align-items: flex-start;">
            <div style="flex: 1;">
                <div style="position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <img :src="oeuvre.image_url" style="width: 100%; display: block; object-fit: cover;">
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px 20px 20px; background: linear-gradient(transparent, rgba(0,0,0,0.9));">
                        <span style="color: var(--color-gold); font-size: 0.8rem; text-transform: uppercase;">{{ oeuvre.categorie }}</span>
                        <h2 style="color: white; margin: 5px 0;">{{ oeuvre.titre }}</h2>
                    </div>
                </div>
            </div>

            <div style="flex: 1.2; background: #111; padding: 40px; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px;">
                <div v-if="isSuccess" style="text-align: center;">
                    <i class="far fa-check-circle" style="font-size: 5rem; color: var(--color-gold); margin-bottom: 20px;"></i>
                    <h2 style="color: white; margin-bottom: 15px;">Paiement Réussi !</h2>
                    <a :href="'oeuvre/' + oeuvre.id" class="btn-primary" style="display: inline-block; width: 100%;">Retour à l'œuvre</a>
                </div>

                <div v-else>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid rgba(212,175,55,0.3); padding-bottom: 15px;">
                        <h3 style="margin: 0; font-size: 1.5rem;">Finaliser l'acquisition</h3>
                        <span style="font-size: 1.8rem; font-weight: bold; color: var(--color-gold);">{{ oeuvre.prix }} €</span>
                    </div>
                    
                    <div class="form-group" :class="{ 'has-error': errors.nom }">
                        <label>Nom sur la carte</label>
                        <input v-model="form.nom" @input="clearError('nom')" type="text" class="payment-input">
                        <span v-if="errors.nom" class="error-text">{{ errors.nom }}</span>
                    </div>

                    <div class="form-group" :class="{ 'has-error': errors.email }">
                        <label>Adresse Email</label>
                        <input v-model="form.email" @input="clearError('email')" type="email" class="payment-input">
                    </div>

                    <div class="form-group" :class="{ 'has-error': errors.numero }">
                        <label>Numéro de carte</label>
                        <div class="input-with-icon">
                            <i class="far fa-credit-card"></i>
                            <input v-model="form.numero" @input="formatCardNumber" type="text" maxlength="19" class="payment-input" style="padding-left: 45px;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" :class="{ 'has-error': errors.expiration }" style="flex: 1;">
                            <label>Expiration</label><input v-model="form.expiration" @input="formatExpiry" type="text" placeholder="MM/YY" maxlength="5" class="payment-input">
                        </div>
                        <div class="form-group" :class="{ 'has-error': errors.cvc }" style="flex: 1;">
                            <label>CVC</label><input v-model="form.cvc" @input="formatCvc" type="password" maxlength="3" class="payment-input">
                        </div>
                    </div>

                    <button @click="processPayment" class="btn-primary" style="width: 100%; padding: 18px;" :disabled="isProcessing">
                        <span v-if="isProcessing">Traitement en cours...</span><span v-else>Payer {{ oeuvre.prix }} €</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
<script src="assets/js/paiement.js"></script>

<?php include 'includes/footer.php'; ?>