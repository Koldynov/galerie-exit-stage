<?php include 'includes/header.php'; ?>

<main id="paiement-app" v-cloak style="padding-top: 150px; padding-bottom: 100px; min-height: 100vh;">
    <div class="container" style="max-width: 900px;">
        
        <div v-if="isLoading" class="empty-state" style="border: none;">
            <div class="loader"></div>
            <p>Chargement de la transaction...</p>
        </div>
        
        <div v-else-if="oeuvre" style="display: flex; gap: 50px; animation: fadeInUp 0.5s forwards; align-items: flex-start;">
            
            <div style="flex: 1;">
                <div style="position: relative; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    <img :src="oeuvre.image_url" style="width: 100%; display: block; object-fit: cover;">
                    <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 30px 20px 20px; background: linear-gradient(transparent, rgba(0,0,0,0.9));">
                        <span style="color: var(--color-gold); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px;">{{ oeuvre.categorie }}</span>
                        <h2 style="color: white; margin: 5px 0;">{{ oeuvre.titre }}</h2>
                        <p style="color: #bbb; font-size: 0.9rem;">Par {{ oeuvre.artistes?.nom || 'Artiste inconnu' }}</p>
                    </div>
                </div>
            </div>

            <div style="flex: 1.2; background: #111; padding: 40px; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px;">
                
                <div v-if="isSuccess" style="text-align: center; animation: fadeIn 0.5s ease-out;">
                    <i class="far fa-check-circle" style="font-size: 5rem; color: var(--color-gold); margin-bottom: 20px;"></i>
                    <h2 style="color: white; margin-bottom: 15px;">Paiement Réussi !</h2>
                    <p style="color: #888; line-height: 1.6; margin-bottom: 30px;">
                        Merci pour votre acquisition. Le montant de <strong style="color: var(--color-gold);">{{ oeuvre.prix }} €</strong> a été traité avec succès.<br>
                        Notre équipe vous contactera pour organiser la livraison de l'œuvre.
                    </p>
                    <a :href="'oeuvre.php?id=' + oeuvre.id" class="btn-primary" style="display: inline-block; width: 100%;">Retour à l'œuvre</a>
                </div>

                <div v-else>
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid rgba(212,175,55,0.3); padding-bottom: 15px;">
                        <h3 style="margin: 0; font-size: 1.5rem;">Finaliser l'acquisition</h3>
                        <span style="font-size: 1.8rem; font-weight: bold; color: var(--color-gold);">{{ oeuvre.prix }} €</span>
                    </div>
                    
                    <div class="form-group" :class="{ 'has-error': errors.nom }">
                        <label>Nom sur la carte</label>
                        <input v-model="form.nom" @input="clearError('nom')" type="text" placeholder="Ex: Jean Dupont" class="payment-input">
                        <span v-if="errors.nom" class="error-text">{{ errors.nom }}</span>
                    </div>

                    <div class="form-group" :class="{ 'has-error': errors.numero }">
                        <label>Numéro de carte</label>
                        <div class="input-with-icon">
                            <i class="far fa-credit-card"></i>
                            <input v-model="form.numero" @input="formatCardNumber" type="text" placeholder="0000 0000 0000 0000" maxlength="19" class="payment-input" style="padding-left: 45px;">
                        </div>
                        <span v-if="errors.numero" class="error-text">{{ errors.numero }}</span>
                    </div>

                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" :class="{ 'has-error': errors.expiration }" style="flex: 1;">
                            <label>Expiration</label>
                            <input v-model="form.expiration" @input="formatExpiry" type="text" placeholder="MM/YY" maxlength="5" class="payment-input">
                            <span v-if="errors.expiration" class="error-text">{{ errors.expiration }}</span>
                        </div>

                        <div class="form-group" :class="{ 'has-error': errors.cvc }" style="flex: 1;">
                            <label>CVC</label>
                            <input v-model="form.cvc" @input="formatCvc" type="password" placeholder="123" maxlength="3" class="payment-input">
                            <span v-if="errors.cvc" class="error-text">{{ errors.cvc }}</span>
                        </div>
                    </div>

                    <button @click="processPayment" class="btn-primary" style="width: 100%; padding: 18px; font-size: 1.1rem; margin-top: 10px;" :disabled="isProcessing">
                        <span v-if="isProcessing"><i class="fas fa-spinner fa-spin"></i> Traitement en cours...</span>
                        <span v-else>Payer {{ oeuvre.prix }} €</span>
                    </button>
                    
                    <p style="text-align: center; color: #555; font-size: 0.75rem; margin-top: 25px;">
                        <i class="fas fa-lock" style="color: #888;"></i> Paiement crypté et sécurisé en 256-bit SSL
                    </p>
                </div>

            </div>
        </div>
    </div>
</main>

<style>
.main-header {
    transform: translateY(0) !important;
    background-color: rgba(20, 20, 20, 0.98) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.form-group { margin-bottom: 25px; }
.form-group label { display: block; color: #aaa; margin-bottom: 8px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }

.payment-input {
    width: 100%; padding: 15px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    color: white; border-radius: 4px; font-family: 'Inter', sans-serif; font-size: 1rem; letter-spacing: 1px;
    transition: all 0.3s; box-sizing: border-box;
}
.payment-input:focus { border-color: var(--color-gold); outline: none; background: rgba(20,20,20,0.9); }
.payment-input::placeholder { color: #444; letter-spacing: 1px; }

.input-with-icon { position: relative; }
.input-with-icon i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #666; font-size: 1.2rem; }

/* Стилі для помилок */
.has-error .payment-input { border-color: var(--color-red-heart); background: rgba(230, 57, 70, 0.05); }
.has-error label { color: var(--color-red-heart); }
.error-text { color: var(--color-red-heart); font-size: 0.8rem; display: block; margin-top: 8px; animation: fadeIn 0.3s; }

[v-cloak] { display: none; }
</style>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const oeuvre = ref(null);
        const isLoading = ref(true);
        const isProcessing = ref(false);
        const isSuccess = ref(false);

        const form = ref({ nom: '', numero: '', expiration: '', cvc: '' });
        const errors = ref({ nom: '', numero: '', expiration: '', cvc: '' });

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        onMounted(async () => {
            const id = new URLSearchParams(window.location.search).get('id');
            if (id) {
                const { data } = await supabase.from('oeuvres').select('*, artistes(nom)').eq('id', id).single();
                if (data) oeuvre.value = data;
            }
            isLoading.value = false;
        });

        const clearError = (field) => {
            errors.value[field] = '';
        };

        // Жорстке форматування номера карти (тільки цифри, макс 16, пробіли кожні 4)
        const formatCardNumber = (e) => {
            clearError('numero');
            let val = e.target.value.replace(/\D/g, ''); // Видаляємо всі не-цифри
            val = val.substring(0, 16); // Обрізаємо до 16 цифр
            form.value.numero = val.match(/.{1,4}/g)?.join(' ') || val; // Додаємо пробіли
        };

        // Жорстке форматування дати (тільки цифри, логіка місяців, слеш)
        const formatExpiry = (e) => {
            clearError('expiration');
            let val = e.target.value.replace(/\D/g, ''); // Видаляємо не-цифри
            val = val.substring(0, 4); // Обрізаємо до 4 цифр
            
            if (val.length >= 2) {
                let month = parseInt(val.substring(0, 2));
                if (month > 12) val = '12' + val.substring(2); // Якщо ввели 15, робимо 12
                if (month === 0) val = '01' + val.substring(2); // Якщо ввели 00, робимо 01
                
                // Додаємо слеш
                if (val.length > 2) {
                    val = val.substring(0, 2) + '/' + val.substring(2);
                } else if (e.inputType !== 'deleteContentBackward') {
                    val = val + '/';
                }
            }
            form.value.expiration = val;
        };

        // Жорстке форматування CVC (тільки цифри, макс 3)
        const formatCvc = (e) => {
            clearError('cvc');
            let val = e.target.value.replace(/\D/g, '');
            form.value.cvc = val.substring(0, 3);
        };

        // Логіка перевірки перед оплатою
        const validateForm = () => {
            let isValid = true;
            errors.value = { nom: '', numero: '', expiration: '', cvc: '' };

            if (!form.value.nom.trim() || form.value.nom.length < 3) {
                errors.value.nom = "Veuillez entrer le nom complet.";
                isValid = false;
            }

            const pureNumber = form.value.numero.replace(/\s/g, '');
            if (pureNumber.length !== 16) {
                errors.value.numero = "16 chiffres requis.";
                isValid = false;
            }

            if (form.value.expiration.length !== 5) {
                errors.value.expiration = "Format MM/YY requis.";
                isValid = false;
            } else {
                // Перевірка чи не закінчився термін дії
                const [month, year] = form.value.expiration.split('/');
                const now = new Date();
                const currentYear = parseInt(now.getFullYear().toString().substr(-2)); // наприклад 24
                const currentMonth = now.getMonth() + 1;
                
                const expYear = parseInt(year);
                const expMonth = parseInt(month);

                if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
                    errors.value.expiration = "Cette carte a expiré.";
                    isValid = false;
                }
            }

            if (form.value.cvc.length !== 3) {
                errors.value.cvc = "3 chiffres requis.";
                isValid = false;
            }

            return isValid;
        };

        const processPayment = () => {
            if (!validateForm()) return;

            isProcessing.value = true;
            
            // Імітація обробки платежу (2.5 секунди)
            setTimeout(() => {
                isProcessing.value = false;
                isSuccess.value = true;
            }, 2500);
        };

        return { 
            oeuvre, isLoading, form, errors, 
            clearError, formatCardNumber, formatExpiry, formatCvc, 
            isProcessing, isSuccess, processPayment 
        };
    }
}).mount('#paiement-app');
</script>

<?php include 'includes/footer.php'; ?>