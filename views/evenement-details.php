<?php include 'includes/header.php'; ?>

<main id="evenement-details-app" v-cloak>
    <section class="atelier-hero" :style="{ backgroundImage: 'linear-gradient(to bottom, rgba(0,0,0,0.2), #0a0a0a), url(' + (atelier?.image_url || 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1920') + ')' }">
        <div class="container hero-content">
            <div class="atelier-meta-top">
                <span class="category-tag" style="background: #fff;">{{ atelier?.categorie || 'Événement' }}</span>
                <span class="date-tag"><i class="far fa-calendar-alt"></i> {{ formatDateFull(atelier?.date_atelier) }}</span>
            </div>
            <h1 class="atelier-title">{{ atelier?.titre }}</h1>
            <p class="atelier-subtitle">Une expérience exclusive présentée par <strong>{{ atelier?.professeur }}</strong></p>
        </div>
    </section>

    <section class="container atelier-main-grid">
        <div class="atelier-info">
            <div class="info-block">
                <h2>À propos de cet événement</h2>
                <div class="description-text" v-html="atelier?.description"></div>
            </div>

            <div class="info-block stats-grid">
                <div class="stat-item" v-if="atelier?.duree">
                    <i class="fas fa-clock"></i>
                    <div>
                        <span>Durée prévue</span>
                        <strong>{{ atelier.duree }}</strong>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-ticket-alt"></i>
                    <div>
                        <span>Disponibilité</span>
                        <strong>{{ atelier?.places_totales }} billets max</strong>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <span>Lieu</span>
                        <strong>{{ atelier?.lieu || 'Galerie EXIT' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <aside class="atelier-sidebar">
            <div class="booking-card">
                <div class="price-tag">{{ atelier?.prix }} € <small>/ billet</small></div>
                
                <div class="availability" :class="{ 'low-stock': atelier?.places_restantes <= 5 }">
                    <i class="fas fa-info-circle"></i>
                    {{ atelier?.places_restantes > 0 ? 'Il reste ' + atelier?.places_restantes + ' billets disponibles' : 'Événement complet' }}
                </div>

                <button class="btn-primary full-width" @click="showModal = true" :disabled="atelier?.places_restantes <= 0">
                    {{ atelier?.places_restantes > 0 ? 'Acheter mon billet' : 'Billetterie fermée' }}
                </button>

                <ul class="booking-perks" v-if="atelier?.avantage_1 || atelier?.avantage_2 || atelier?.avantage_3">
                    <li v-if="atelier?.avantage_1"><i class="fas fa-check" style="color: var(--color-gold);"></i> {{ atelier.avantage_1 }}</li>
                    <li v-if="atelier?.avantage_2"><i class="fas fa-check" style="color: var(--color-gold);"></i> {{ atelier.avantage_2 }}</li>
                    <li v-if="atelier?.avantage_3"><i class="fas fa-check" style="color: var(--color-gold);"></i> {{ atelier.avantage_3 }}</li>
                </ul>
            </div>
        </aside>
    </section>

    <teleport to="body">
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3>Billet : {{ atelier?.titre }}</h3>
                    <button class="btn-close" @click="showModal = false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 20px; color: #aaa; font-size: 0.9rem;">
                        <p style="margin-bottom: 5px;">Date : <strong style="color: #fff;">{{ formatDateFull(atelier?.date_atelier) }}</strong></p>
                        <p>Prix : <strong style="color: var(--color-gold);">{{ atelier?.prix }} €</strong> / billet</p>
                    </div>
                    
                    <form @submit.prevent="submitReservation" class="exit-form">
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input type="text" v-model="form.nom" required placeholder="Votre nom et prénom">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" v-model="form.email" required placeholder="votre@email.com">
                        </div>
                        <div class="form-group">
                            <label>Nombre de billets</label>
                            <select v-model="form.places" required>
                                <option v-for="n in Math.min(6, atelier?.places_restantes)" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary full-width" :disabled="isSubmitting" style="margin-top: 15px;">
                            <span v-if="isSubmitting"><i class="fas fa-spinner fa-spin"></i> Connexion sécurisée...</span>
                            <span v-else>CONFIRMER ET PAYER ({{ atelier?.prix * form.places }} €)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </teleport>
</main>

<style>
/* Ті самі стилі, що й у atelier-details.php */
.atelier-hero { height: 60vh; background-size: cover; background-position: center; display: flex; align-items: flex-end; padding-bottom: 80px; }
.hero-content { width: 100%; }
.atelier-meta-top { display: flex; gap: 20px; margin-bottom: 20px; }
.category-tag { background: var(--color-gold); color: #000; padding: 5px 15px; font-weight: bold; text-transform: uppercase; font-size: 0.8rem; border-radius: 2px;}
.date-tag { color: #fff; font-size: 0.9rem; letter-spacing: 1px; }
.atelier-title { font-size: 5rem; color: #fff; margin-bottom: 10px; font-weight: 300; }
.atelier-subtitle { color: #888; font-size: 1.2rem; }
.atelier-main-grid { display: grid; grid-template-columns: 1fr 350px; gap: 60px; margin-top: 60px; margin-bottom: 100px; }
.info-block { margin-bottom: 50px; }
.info-block h2 { color: var(--color-gold); font-size: 1.8rem; margin-bottom: 25px; font-weight: 300; }
.description-text { color: #ccc; line-height: 1.8; font-size: 1.1rem; }
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; border-top: 1px solid #222; padding-top: 40px; }
.stat-item { display: flex; align-items: center; gap: 15px; }
.stat-item i { color: var(--color-gold); font-size: 1.5rem; }
.stat-item span { display: block; color: #666; font-size: 0.8rem; text-transform: uppercase; }
.stat-item strong { color: #fff; font-size: 1rem; }
.booking-card { background: #111; padding: 30px; border: 1px solid #222; position: sticky; top: 120px; border-radius: 8px;}
.price-tag { font-size: 2.5rem; color: #fff; margin-bottom: 20px; }
.price-tag small { font-size: 1rem; color: #666; }
.availability { background: rgba(255,255,255,0.05); padding: 12px; color: #888; font-size: 0.9rem; margin-bottom: 25px; text-align: center; border-radius: 4px;}
.availability.low-stock { color: #ffab00; background: rgba(255,171,0,0.1); }
.booking-perks { list-style: none; padding: 0; margin-top: 30px; }
.booking-perks li { color: #666; font-size: 0.85rem; margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
.booking-perks li i { color: var(--color-gold); font-size: 0.7rem; }
.full-width { width: 100%; }

.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.85); z-index: 999999 !important; display: flex; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box; }
.modal-box { background: #151515; border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; padding: 30px; width: 100%; max-width: 500px; box-shadow: 0 15px 40px rgba(0,0,0,0.9); position: relative; max-height: 90vh; overflow-y: auto; box-sizing: border-box; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 15px; }
.modal-header h3 { margin: 0; color: #fff; font-size: 1.3rem; font-weight: 500; }
.btn-close { background: transparent; border: none; color: #888; font-size: 1.2rem; cursor: pointer; transition: color 0.3s; }
.btn-close:hover { color: #ff4d4d; }
.exit-form { width: 100%; box-sizing: border-box; }
.exit-form .form-group { display: flex; flex-direction: column; margin-bottom: 20px; width: 100%; box-sizing: border-box; }
.exit-form label { color: var(--color-gold, #d4af37); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
.exit-form input, .exit-form select { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); padding: 12px 15px; color: #fff; font-family: inherit; border-radius: 4px; transition: all 0.3s ease; width: 100%; font-size: 0.95rem; box-sizing: border-box !important; }
.exit-form select { appearance: none; -webkit-appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill="%23d4af37" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>'); background-repeat: no-repeat; background-position-x: 98%; background-position-y: center; }
.exit-form input:focus, .exit-form select:focus { outline: none; border-color: rgba(212, 175, 55, 0.5); background: rgba(255, 255, 255, 0.06); }
.exit-form select option { background-color: #151515; color: #fff; padding: 10px; }
</style>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        const atelier = ref(null);
        const showModal = ref(false);
        const isSubmitting = ref(false);
        const form = ref({ nom: '', email: '', places: 1 });

        const fetchAtelier = async () => {
            const pathParts = window.location.pathname.split('/');
            const id = pathParts[pathParts.length - 1];

            const { data } = await supabase.from('ateliers').select('*').eq('id', id).single();
            if (data) {
                atelier.value = { ...data, places_restantes: data.places_totales - (data.places_reservees || 0) };
            }
        };

        const submitReservation = async () => {
            isSubmitting.value = true;
            try {
                const response = await fetch('/galerie/api/create-atelier-checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        atelier_id: atelier.value.id,
                        nom: form.value.nom,
                        email: form.value.email,
                        places: form.value.places
                    })
                });

                const data = await response.json();
                
                if (data.url) {
                    window.location.href = data.url; 
                } else {
                    alert("Erreur lors de la création du paiement: " + (data.error || "Inconnue"));
                }
            } catch (error) {
                console.error("Erreur Stripe:", error);
                alert("Une erreur est survenue lors de la connexion au paiement.");
            }
            isSubmitting.value = false;
        };

        const formatDateFull = (date) => date ? new Date(date).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';

        onMounted(fetchAtelier);

        return { atelier, showModal, form, isSubmitting, submitReservation, formatDateFull };
    }
}).mount('#evenement-details-app');
</script>

<?php include 'includes/footer.php'; ?>