<?php include 'includes/header.php'; ?>

<main id="ateliers-app" v-cloak>
    <div class="container" style="margin-top: 150px; margin-bottom: 60px;">
        <h1 style="font-size: 4.5rem; font-weight: 300; margin-bottom: 15px; color: #fff;">
            Ateliers<span style="color: var(--color-gold);">.</span>
        </h1>
        <p style="text-transform: uppercase; letter-spacing: 2px; color: #888; font-size: 0.9rem;">
            Découvrez nos masterclasses et cours pratiques animés par des artistes professionnels
        </p>
    </div>

    <section class="container" style="margin-bottom: 100px;">
        
        <div v-if="isLoading" class="ateliers-grid">
            <div v-for="n in 6" :key="n" class="skeleton-card">
                <div class="skeleton skeleton-image"></div>
                
                <div class="atelier-content">
                    <div class="skeleton skeleton-text title"></div>
                    <div class="skeleton skeleton-text short"></div>
                    
                    <div style="margin-top: 25px;">
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-text" style="width: 80%;"></div>
                    </div>

                    <div class="skeleton-footer">
                        <div class="skeleton skeleton-text" style="height: 30px; width: 60px;"></div>
                        <div class="skeleton skeleton-text" style="height: 40px; width: 100%; border-radius: 4px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="ateliers.length === 0" style="text-align: center; padding: 50px; background: #111; border-radius: 8px;">
            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #444; margin-bottom: 20px;"></i>
            <h3 style="color: #fff;">Aucun atelier programmé</h3>
            <p style="color: #888;">Revenez bientôt pour découvrir nos prochaines sessions !</p>
        </div>

        <div v-else class="ateliers-grid">
            <div v-for="atelier in ateliers" :key="atelier.id" class="atelier-card">
                <a :href="'atelier/' + atelier.id" class="atelier-image" style="display: block;">
                    <img :src="atelier.image_url || 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=800&auto=format&fit=crop'" :alt="atelier.titre">
                    <div class="atelier-date-badge">
                        <span class="day">{{ getDay(atelier.date_atelier) }}</span>
                        <span class="month">{{ getMonth(atelier.date_atelier) }}</span>
                    </div>
                </a>
                
                <div class="atelier-content">
                    <a :href="'atelier/' + atelier.id" style="text-decoration: none;">
                        <h3 style="transition: color 0.3s;" onmouseover="this.style.color='var(--color-gold)'" onmouseout="this.style.color='#fff'">{{ atelier.titre }}</h3>
                    </a>
                    
                    <p class="prof">Animé par <strong>{{ atelier.professeur }}</strong></p>
                    <p class="desc">{{ atelier.description }}</p>
                    
                    <div class="atelier-details">
                        <span><i class="fas fa-clock"></i> {{ getTime(atelier.date_atelier) }} ({{ atelier.duree }})</span>
                        <span><i class="fas fa-user-friends"></i> {{ atelier.places_restantes }} places restantes</span>
                    </div>

                    <div class="atelier-footer">
                        <span class="price">{{ atelier.prix }} €</span>
                        <button class="btn-primary" @click="openReserveModal(atelier)" :disabled="atelier.places_restantes <= 0">
                            {{ atelier.places_restantes > 0 ? 'Réserver ma place' : 'Complet' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <teleport to="body">
        <div v-if="showModal" class="modal-overlay">
            <div class="modal-box">
                <div class="modal-header">
                    <h3>Réserver : {{ selectedAtelier?.titre }}</h3>
                    <button class="btn-close" @click="showModal = false"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 20px; color: #aaa; font-size: 0.9rem;">
                        <p style="margin-bottom: 5px;">Date : <strong style="color: #fff;">{{ getDay(selectedAtelier?.date_atelier) }} {{ getMonth(selectedAtelier?.date_atelier) }} à {{ getTime(selectedAtelier?.date_atelier) }}</strong></p>
                        <p>Prix : <strong style="color: var(--color-gold);">{{ selectedAtelier?.prix }} €</strong> / personne</p>
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
                                <option v-for="n in Math.min(4, selectedAtelier?.places_restantes)" :key="n" :value="n">{{ n }}</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary full-width" :disabled="isSubmitting" style="margin-top: 15px;">
                            <span v-if="isSubmitting"><i class="fas fa-spinner fa-spin"></i> Connexion sécurisée...</span>
                            <span v-else>CONFIRMER ET PAYER ({{ selectedAtelier?.prix * form.places }} €)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </teleport>
</main>

<style>
.ateliers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
.atelier-card { background: #111; border: 1px solid rgba(255,255,255,0.05); border-radius: 8px; overflow: hidden; transition: transform 0.3s ease; }
.atelier-card:hover { transform: translateY(-5px); border-color: rgba(212, 175, 55, 0.3); }
.atelier-image { position: relative; height: 200px; }
.atelier-image img { width: 100%; height: 100%; object-fit: cover; }
.atelier-date-badge { position: absolute; top: 15px; right: 15px; background: var(--color-gold); color: #000; padding: 10px; border-radius: 4px; text-align: center; font-weight: bold; line-height: 1.2; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
.atelier-date-badge .day { display: block; font-size: 1.5rem; }
.atelier-date-badge .month { display: block; font-size: 0.8rem; text-transform: uppercase; }
.atelier-content { padding: 25px; }
.atelier-content h3 { color: #fff; font-size: 1.5rem; margin-bottom: 5px; }
.atelier-content .prof { color: var(--color-gold); font-size: 0.9rem; margin-bottom: 15px; }
.atelier-content .desc { color: #aaa; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.atelier-details { display: flex; justify-content: space-between; font-size: 0.85rem; color: #888; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.atelier-footer { 
    display: flex; 
    flex-direction: column;
    align-items: center;
    gap: 15px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.05);
}
.atelier-footer .price { 
    font-size: 2rem;
    font-weight: 300; 
    color: var(--color-gold);
}
.atelier-footer .btn-primary {
    width: 100%;
    text-align: center;
}

.modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.85); z-index: 999999;
    display: flex; justify-content: center; align-items: center;
    padding: 20px; box-sizing: border-box;
}
.modal-box {
    background: #151515; border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 8px; padding: 30px; width: 100%; max-width: 500px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.9); box-sizing: border-box;
}
.modal-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 15px;
}
.modal-header h3 { margin: 0; color: #fff; font-size: 1.3rem; font-weight: 500; }
.btn-close { background: transparent; border: none; color: #888; font-size: 1.2rem; cursor: pointer; }
.btn-close:hover { color: #ff4d4d; }

.exit-form { width: 100%; box-sizing: border-box; }
.exit-form .form-group { display: flex; flex-direction: column; margin-bottom: 20px; width: 100%; box-sizing: border-box; }
.exit-form label { color: var(--color-gold); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
.exit-form input, .exit-form select {
    background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 12px 15px; color: #fff; border-radius: 4px; transition: all 0.3s;
    width: 100%; font-size: 0.95rem; box-sizing: border-box !important;
}
.exit-form select {
    appearance: none; -webkit-appearance: none;
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23d4af37" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
    background-repeat: no-repeat; background-position-x: 98%; background-position-y: center;
}
.exit-form input:focus, .exit-form select:focus { outline: none; border-color: rgba(212, 175, 55, 0.5); background: rgba(255, 255, 255, 0.06); }
.full-width { width: 100%; }

.exit-form select option {
    background-color: #151515;
    color: #fff;
    padding: 10px;
}

/* =========================================
   SKELETON LOADERS (Анімація завантаження)
========================================= */
@keyframes skeleton-loading {
    0% { background-color: #1a1a1a; }
    50% { background-color: #2a2a2a; }
    100% { background-color: #1a1a1a; }
}

.skeleton {
    animation: skeleton-loading 1.5s infinite ease-in-out;
    border-radius: 4px;
}

.skeleton-card {
    background: #111;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 8px;
    overflow: hidden;
}

.skeleton-image {
    height: 200px;
    width: 100%;
    border-radius: 0;
}

.skeleton-text {
    height: 12px;
    margin-bottom: 12px;
    width: 100%;
}

.skeleton-text.title {
    height: 24px;
    width: 70%;
    margin-bottom: 20px;
}

.skeleton-text.short {
    width: 40%;
}

.skeleton-footer {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.05);
}
</style>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        const ateliers = ref([]);
        const isLoading = ref(true);

        const showModal = ref(false);
        const selectedAtelier = ref(null);
        const isSubmitting = ref(false);
        const form = ref({ nom: '', email: '', places: 1 });

        const fetchAteliers = async () => {
            const { data, error } = await supabase
                .from('ateliers')
                .select('*')
                .gte('date_atelier', new Date().toISOString())
                .order('date_atelier', { ascending: true });

            if (data) {
                ateliers.value = data.map(a => ({ ...a, places_restantes: a.places_totales - (a.places_reservees || 0) }));
            }
            isLoading.value = false;
            
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                form.value.email = session.user.email;
                form.value.nom = session.user.user_metadata?.full_name || '';
            }
        };

        const openReserveModal = (atelier) => {
            selectedAtelier.value = atelier;
            form.value.places = 1;
            showModal.value = true;
        };

        const submitReservation = async () => {
            isSubmitting.value = true;
            try {
                const response = await fetch('/galerie/api/create-atelier-checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        atelier_id: selectedAtelier.value.id,
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

        const getDay = (dateStr) => new Date(dateStr).getDate();
        const getMonth = (dateStr) => new Date(dateStr).toLocaleDateString('fr-FR', { month: 'short' });
        const getTime = (dateStr) => new Date(dateStr).toLocaleTimeString('fr-FR', { hour: '2-digit', minute:'2-digit' });
        const formatDateFull = (dateStr) => new Date(dateStr).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute:'2-digit' });

        onMounted(() => {
            fetchAteliers();
        });

        return { ateliers, isLoading, showModal, selectedAtelier, form, openReserveModal, submitReservation, isSubmitting, getDay, getMonth, getTime, formatDateFull };
    }
}).mount('#ateliers-app');
</script>

<?php include 'includes/footer.php'; ?>