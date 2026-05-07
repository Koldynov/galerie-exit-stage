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
        
        <div v-if="isLoading" style="text-align: center; padding: 50px;">
            <i class="fas fa-spinner fa-spin" style="color: var(--color-gold); font-size: 2rem;"></i>
        </div>

        <div v-else-if="ateliers.length === 0" style="text-align: center; padding: 50px; background: #111; border-radius: 8px;">
            <i class="fas fa-calendar-times" style="font-size: 3rem; color: #444; margin-bottom: 20px;"></i>
            <h3 style="color: #fff;">Aucun atelier programmé</h3>
            <p style="color: #888;">Revenez bientôt pour découvrir nos prochaines sessions !</p>
        </div>

        <div v-else class="ateliers-grid">
            <div v-for="atelier in ateliers" :key="atelier.id" class="atelier-card">
                <div class="atelier-image">
                    <img :src="atelier.image_url || 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=800&auto=format&fit=crop'" :alt="atelier.titre">
                    <div class="atelier-date-badge">
                        <span class="day">{{ getDay(atelier.date_atelier) }}</span>
                        <span class="month">{{ getMonth(atelier.date_atelier) }}</span>
                    </div>
                </div>
                <div class="atelier-content">
                    <h3>{{ atelier.titre }}</h3>
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

    <div v-if="showModal" class="modal-overlay" style="display:flex;">
        <div class="modal-box" style="max-width: 500px;">
            <div class="modal-header">
                <h3>Réserver : {{ selectedAtelier.titre }}</h3>
                <button class="btn-close" @click="showModal = false"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p style="color: #ccc; margin-bottom: 20px;">Date : <strong>{{ formatDateFull(selectedAtelier.date_atelier) }}</strong><br>Prix : <strong>{{ selectedAtelier.prix }} €</strong></p>
                
                <form @submit.prevent="submitReservation" class="exit-form">
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" v-model="form.nom" required placeholder="Votre nom">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" v-model="form.email" required placeholder="votre@email.com">
                    </div>
                    <div class="form-group">
                        <label>Nombre de places</label>
                        <select v-model="form.places" required>
                            <option v-for="n in Math.min(4, selectedAtelier.places_restantes)" :key="n" :value="n">{{ n }}</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;" :disabled="isSubmitting">
                        <span v-if="isSubmitting"><i class="fas fa-spinner fa-spin"></i> Traitement...</span>
                        <span v-else>Confirmer et Payer ({{ selectedAtelier.prix * form.places }} €)</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
/* Специфічні стилі для Ательє */
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
.atelier-footer { display: flex; justify-content: space-between; align-items: center; }
.atelier-footer .price { font-size: 1.5rem; font-weight: bold; color: #fff; }
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
            // Беремо тільки майбутні уроки
            const { data, error } = await supabase
                .from('ateliers')
                .select('*')
                .gte('date_atelier', new Date().toISOString())
                .order('date_atelier', { ascending: true });

            if (data) {
                // Пізніше додамо логіку підрахунку вже зайнятих місць
                ateliers.value = data.map(a => ({ ...a, places_restantes: a.places_totales })); 
            }
            isLoading.value = false;
            
            // Автозаповнення даних клієнта, якщо він залогінений
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
            // ТУТ БУДЕ ЛОГІКА СТРАЙПУ
            // Поки що просто імітуємо створення замовлення в базі
            const { error } = await supabase.from('atelier_reservations').insert([{
                atelier_id: selectedAtelier.value.id,
                user_email: form.value.email,
                nom_client: form.value.nom,
                places_reservees: form.value.places,
                statut: 'en attente'
            }]);

            isSubmitting.value = false;
            
            if (error) {
                alert("Erreur lors de la réservation.");
            } else {
                alert("Réservation pré-enregistrée ! Redirection vers le paiement...");
                showModal.value = false;
                // window.location.href = '/galerie/checkout-atelier?id=' + ...
            }
        };

        // Helpers для форматування дати
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