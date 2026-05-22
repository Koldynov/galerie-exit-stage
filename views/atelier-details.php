<?php include 'includes/header.php'; ?>

<main id="atelier-details-app" v-cloak>
    <section class="atelier-hero" :style="{ backgroundImage: 'linear-gradient(to bottom, rgba(0,0,0,0.2), #0a0a0a), url(' + (atelier?.image_url || 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1920') + ')' }">
        <div class="container hero-content">
            <div class="atelier-meta-top">
                <span class="category-tag">Masterclass</span>
                <span class="date-tag"><i class="far fa-calendar-alt"></i> {{ formatDateFull(atelier?.date_atelier) }}</span>
            </div>
            <h1 class="atelier-title">{{ atelier?.titre }}</h1>
            <p class="atelier-subtitle">Une immersion artistique animée par <strong>{{ atelier?.professeur }}</strong></p>
        </div>
    </section>

    <section class="container atelier-main-grid">
        <div class="atelier-info">
            <div class="info-block">
                <h2>À propos de cet atelier</h2>
                <div class="description-text" v-html="atelier?.description"></div>
            </div>

            <div class="info-block stats-grid">
                <div class="stat-item" v-if="atelier?.duree">
                    <i class="fas fa-clock"></i>
                    <div>
                        <span>Durée</span>
                        <strong>{{ atelier.duree }}</strong>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <div>
                        <span>Capacité</span>
                        <strong>{{ atelier?.places_totales }} personnes max</strong>
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
                <div class="price-tag">{{ atelier?.prix }} € <small>/ personne</small></div>
                
                <div class="availability" :class="{ 'low-stock': atelier?.places_restantes <= 3 }">
                    <i class="fas fa-info-circle"></i>
                    {{ atelier?.places_restantes > 0 ? 'Il reste ' + atelier?.places_restantes + ' places disponibles' : 'Complet' }}
                </div>

                <button class="btn-primary full-width" @click="showModal = true" :disabled="atelier?.places_restantes <= 0">
                    {{ atelier?.places_restantes > 0 ? 'Réserver maintenant' : 'Session complète' }}
                </button>

                <ul class="booking-perks" v-if="atelier?.avantage_1 || atelier?.avantage_2 || atelier?.avantage_3">
                    <li v-if="atelier?.avantage_1"><i class="fas fa-check"></i> {{ atelier.avantage_1 }}</li>
                    <li v-if="atelier?.avantage_2"><i class="fas fa-check"></i> {{ atelier.avantage_2 }}</li>
                    <li v-if="atelier?.avantage_3"><i class="fas fa-check"></i> {{ atelier.avantage_3 }}</li>
                </ul>
            </div>
        </aside>
    </section>

    <teleport to="body">
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3>Réserver : {{ atelier?.titre }}</h3>
                    <button class="btn-close" @click="showModal = false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 20px; color: #aaa; font-size: 0.9rem;">
                        <p style="margin-bottom: 5px;">Date : <strong style="color: #fff;">{{ formatDateFull(atelier?.date_atelier) }}</strong></p>
                        <p>Prix : <strong style="color: var(--color-gold);">{{ atelier?.prix }} €</strong> / personne</p>
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
                            <label>Nombre de places</label>
                            <select v-model="form.places" required>
                                <option v-for="n in Math.min(4, atelier?.places_restantes)" :key="n" :value="n">{{ n }}</option>
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

<link rel="stylesheet" href="/galerie/assets/css/event-atelier-style.css">

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
                // Відправляємо дані на наш PHP бекенд для створення Stripe Session
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
                    // Якщо Stripe успішно створив сесію, перенаправляємо туди клієнта
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
}).mount('#atelier-details-app');
</script>

<?php include 'includes/footer.php'; ?>
