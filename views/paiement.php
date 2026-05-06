<?php include 'includes/header.php'; ?>
<script>window.CURRENT_ITEM_ID = "<?= $item_id ?>";</script>
<!-- Підключаємо офіційний скрипт Stripe -->
<script src="https://js.stripe.com/v3/"></script>

<link rel="stylesheet" href="/galerie/assets/css/paiement-style.css">

<style>
.autocomplete-dropdown { position: absolute; width: 100%; background: #1a1a1a; border: 1px solid var(--color-gold); z-index: 1000; border-radius: 4px; top: 100%; left: 0; box-shadow: 0 5px 15px rgba(0,0,0,0.8); max-height: 200px; overflow-y: auto; }
.autocomplete-item { padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #333; color: #ccc; font-size: 0.9rem; transition: all 0.2s ease; }
.autocomplete-item:last-child { border-bottom: none; }
.autocomplete-item:hover { background: rgba(212, 175, 55, 0.2); color: white; }
select.payment-input option { background: #111; color: white; }

/* Стилі для контейнера Stripe */
/* Стилі для контейнера Stripe */
#payment-element { margin-top: 20px; min-height: 150px; }</style>

<main id="paiement-app" v-cloak style="padding-top: 150px; padding-bottom: 100px; min-height: 100vh;">
    <div class="container" style="max-width: 900px;">
        
        <div v-if="isLoading" class="empty-state" style="border: none;"><div class="loader"></div><p>Chargement...</p></div>
        
        <div v-else-if="oeuvre" style="display: flex; gap: 50px; animation: fadeInUp 0.5s forwards; align-items: flex-start;">
            <!-- Ліва колонка з картиною -->
            <div style="flex: 1;">
                <div style="position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <img :src="oeuvre.image_url" style="width: 100%; display: block; object-fit: cover;">
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px 20px 20px; background: linear-gradient(transparent, rgba(0,0,0,0.9));">
                        <span style="color: var(--color-gold); font-size: 0.8rem; text-transform: uppercase;">{{ oeuvre.categorie }}</span>
                        <h2 style="color: white; margin: 5px 0;">{{ oeuvre.titre }}</h2>
                    </div>
                </div>
            </div>

            <!-- Права колонка з формою -->
            <div style="flex: 1.2; background: #111; padding: 40px; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px;">
                <div v-if="isSuccess" style="text-align: center;">
                    <i class="far fa-check-circle" style="font-size: 5rem; color: var(--color-gold); margin-bottom: 20px;"></i>
                    <h2 style="color: white; margin-bottom: 15px;">Paiement Réussi !</h2>
                    <p style="color: #aaa; margin-bottom: 30px;">L'artiste va préparer votre œuvre pour l'expédition.</p>
                    <a :href="'/galerie/oeuvre/' + oeuvre.id" class="btn-primary" style="display: inline-block; width: 100%;">Retour à l'œuvre</a>
                </div>

                <div v-else>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid rgba(212,175,55,0.3); padding-bottom: 15px;">
                        <h3 style="margin: 0; font-size: 1.5rem;">Finaliser l'acquisition</h3>
                        <span style="font-size: 1.8rem; font-weight: bold; color: var(--color-gold);">{{ oeuvre.prix }} €</span>
                    </div>
                    
                    <div class="form-group" :class="{ 'has-error': errors.nom }">
                        <label>Nom et Prénom</label>
                        <input v-model="form.nom" @input="clearError('nom')" type="text" class="payment-input" placeholder="Ex: Jean Dupont">
                        <span v-if="errors.nom" class="error-text">{{ errors.nom }}</span>
                    </div>

                    <div class="form-group" :class="{ 'has-error': errors.email }">
                        <label>Adresse Email</label>
                        <input v-model="form.email" @input="clearError('email')" type="email" class="payment-input">
                        <span v-if="errors.email" class="error-text">{{ errors.email }}</span>
                    </div>

                    <div style="margin-top: 30px; margin-bottom: 15px;">
                        <h4 style="color: var(--color-gold); font-size: 1rem; border-bottom: 1px solid #333; padding-bottom: 5px; margin-bottom: 15px;">Adresse de livraison</h4>
                        <div class="form-group" :class="{ 'has-error': errors.adresse }" style="position: relative;">
                            <label>Numéro et nom de la rue</label>
                            <input v-model="form.adresse" @input="searchAddress" type="text" class="payment-input" placeholder="Ex: 12 rue de la Paix" autocomplete="address-line1">
                            <div v-if="addressSuggestions.length > 0" class="autocomplete-dropdown">
                                <div v-for="suggestion in addressSuggestions" :key="suggestion.properties.id" @click="selectAddress(suggestion)" class="autocomplete-item">
                                    {{ suggestion.properties.label }}
                                </div>
                            </div>
                            <span v-if="errors.adresse" class="error-text">{{ errors.adresse }}</span>
                        </div>
                        <div style="display: flex; gap: 15px;">
                            <div class="form-group" :class="{ 'has-error': errors.code_postal }" style="flex: 1;">
                                <label>Code Postal</label><input v-model="form.code_postal" @input="clearError('code_postal')" type="text" class="payment-input" autocomplete="postal-code">
                                <span v-if="errors.code_postal" class="error-text">{{ errors.code_postal }}</span>
                            </div>
                            <div class="form-group" :class="{ 'has-error': errors.ville }" style="flex: 2;">
                                <label>Ville</label><input v-model="form.ville" @input="clearError('ville')" type="text" class="payment-input" autocomplete="address-level2">
                                <span v-if="errors.ville" class="error-text">{{ errors.ville }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pays</label>
                            <select v-model="form.pays" class="payment-input" style="appearance: auto; padding: 12px 15px;">
                                <option value="France">France</option><option value="Belgique">Belgique</option><option value="Suisse">Suisse</option><option value="Canada">Canada</option>
                            </select>
                        </div>
                    </div>

                    <!-- ТУТ БУДЕ ФОРМА STRIPE -->
                    <div style="margin-top: 30px;">
                        <h4 style="color: var(--color-gold); font-size: 1rem; border-bottom: 1px solid #333; padding-bottom: 5px;">Paiement sécurisé</h4>
                        <div id="payment-element">
                            <!-- Stripe injected here -->
                            <div style="padding: 20px; text-align: center; color: #888;">Chargement du module de paiement...</div>
                        </div>
                        <div id="payment-message" style="color: #ff4d4d; margin-top: 10px; font-size: 0.9rem; display: none;"></div>
                    </div>

                    <button @click="processPayment" id="submit-button" class="btn-primary" style="width: 100%; padding: 18px; margin-top: 20px;" :disabled="isProcessing">
                        <span v-if="isProcessing">Traitement en cours... <i class="fas fa-spinner fa-spin"></i></span>
                        <span v-else>Payer {{ oeuvre.prix }} €</span>
                    </button>
                    <p style="text-align: center; color: #666; font-size: 0.8rem; margin-top: 15px;">
                        <i class="fas fa-lock"></i> Propulsé par Stripe
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/galerie/assets/js/paiement.js"></script>
<?php include 'includes/footer.php'; ?>