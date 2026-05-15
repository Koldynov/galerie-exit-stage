const { createApp, ref, onMounted } = Vue;
createApp({
    setup() {
        const oeuvre = ref(null); 
        const isLoading = ref(true); 
        const errorMessage = ref(""); 
        const currentUser = ref(null); 
        const currentUserName = ref(''); 
        const isFavori = ref(false);
        const showAcquireModal = ref(false); 
        const inquiryForm = ref({ nom: '', email: '', message: '' }); 
        const isSubmitting = ref(false); 
        const isSuccess = ref(false); 
        const formError = ref("");
        const showLightbox = ref(false);
        const recommendations = ref([]);

        const zoomStyle = ref({ 
            transform: 'scale(1)', 
            transformOrigin: 'center', 
            transition: 'transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1)' // Дуже плавний старт і кінець
        });

        const handleZoom = (e) => {
            const { left, top, width, height } = e.currentTarget.getBoundingClientRect();
            const x = ((e.clientX - left) / width) * 100;
            const y = ((e.clientY - top) / height) * 100;
            zoomStyle.value = {
                transformOrigin: `${x}% ${y}%`,
                transform: 'scale(2.2)', 
                transition: 'none' 
            };
        };

        const resetZoom = () => {
            zoomStyle.value = {
                transformOrigin: 'center',
                transform: 'scale(1)',
                transition: 'transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1)' 
            };
        };

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        
        const fetchOeuvre = async () => { 
            const id = window.CURRENT_ITEM_ID; 
            if (!id) { errorMessage.value = "Erreur."; isLoading.value = false; return; } 
            const { data } = await supabase.from('oeuvres').select('*, artistes(id, nom)').eq('id', id).single(); 
            if (!data) errorMessage.value = "Introuvable."; 
            else { 
                oeuvre.value = data; 
                await fetchRecommendations(data.categorie, data.id, data.prix);
                if (currentUser.value) { 
                    const { data: f } = await supabase.from('favoris').select('id').eq('user_id', currentUser.value.id).eq('oeuvre_id', id).single(); 
                    if (f) isFavori.value = true; 
                } 
            } 
            isLoading.value = false; 
        };
        
        const toggleFavori = async () => { 
            if (!currentUser.value) { 
                window.location.href = '/galerie/portail-artiste'; 
                return; 
            } 
            if (isFavori.value) { 
                isFavori.value = false; 
                await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvre.value.id }); 
            } else { 
                isFavori.value = true; 
                await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvre.value.id }]); 
            } 
        };
        
        const handleAcquisition = () => { 
            if (oeuvre.value && oeuvre.value.prix > 0) {
                window.location.href = '/galerie/paiement/' + oeuvre.value.id; 
            } else {
                showAcquireModal.value = true; 
            }
        };

        const submitInquiry = async () => { 
            formError.value = ""; 
            // Визначаємо ім'я та email (або з профілю, або з форми)
            let em = currentUser.value ? currentUser.value.email : inquiryForm.value.email; 
            let n = currentUser.value ? currentUserName.value : inquiryForm.value.nom; 
            
            if (!em) { formError.value = "Email requis."; return; } 
            isSubmitting.value = true; 
            
            // 1. Записуємо в Supabase
            const { error } = await supabase.from('demandes').insert([{ oeuvre_id: oeuvre.value.id, nom: n, email: em, message: inquiryForm.value.message }]); 
            
            if (error) {
                formError.value = "Erreur."; 
            } else {
                // 2. ВІДПРАВЛЯЄМО EMAIL ЧЕРЕЗ НАШ НОВИЙ API
                try {
                    await fetch('/galerie/api/notify-demande', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            nom: n,
                            email: em,
                            oeuvre_titre: oeuvre.value.titre
                        })
                    });
                } catch (e) {
                    console.error("Erreur lors de l'envoi de l'email API :", e);
                }
                
                // 3. Показуємо повідомлення про успіх
                isSuccess.value = true; 
            }
            isSubmitting.value = false; 
        };

        const fetchRecommendations = async (categorie, currentId, currentPrix) => {
            try {
                // Беремо більше картин з цієї ж категорії
                const { data, error } = await supabase
                    .from('oeuvres')
                    .select('id, titre, prix, image_url')
                    .eq('categorie', categorie)
                    .neq('id', currentId)
                    .limit(10);
                
                if (data && !error) {
                    // Якщо в картини є ціна, шукаємо схожі за бюджетом (від -50% до +50% ціни)
                    if (currentPrix > 0) {
                        const minPrice = currentPrix * 0.5;
                        const maxPrice = currentPrix * 1.5;
                        const filtered = data.filter(item => item.prix >= minPrice && item.prix <= maxPrice);
                        
                        // Якщо знайшли хоча б 3 схожі за ціною, показуємо їх. Якщо ні - показуємо просто з категорії
                        recommendations.value = filtered.length >= 3 ? filtered.slice(0, 4) : data.slice(0, 4);
                    } else {
                        recommendations.value = data.slice(0, 4);
                    }
                }
            } catch (err) {
                console.error("Error:", err);
            }
        };
        
        onMounted(async () => {
    const { data: { session } } = await supabase.auth.getSession();
    if (session) {
        currentUser.value = session.user;
        
        // 1. Спершу перевіряємо, чи це художник (Artiste)
        const { data: artistData } = await supabase
            .from('artistes')
            .select('nom')
            .eq('auth_user_id', currentUser.value.id)
            .maybeSingle();
        
        if (artistData && artistData.nom) {
            currentUserName.value = artistData.nom;
        } 
        else {
            // 2. Якщо не художник, шукаємо в таблиці profiles (Клієнт)
            const { data: profileData } = await supabase
                .from('profiles')
                .select('first_name, last_name')
                .eq('id', currentUser.value.id)
                .maybeSingle();

            if (profileData && (profileData.first_name || profileData.last_name)) {
                // Склеюємо Ім'я та Прізвище
                const firstName = profileData.first_name || '';
                const lastName = profileData.last_name || '';
                currentUserName.value = `${firstName} ${lastName}`.trim();
            }
            // 3. Якщо в профілі пусто, спробуємо метадані
            else if (session.user.user_metadata && session.user.user_metadata.full_name) {
                currentUserName.value = session.user.user_metadata.full_name;
            }
            // 4. Крайній випадок — пошта
            else {
                currentUserName.value = session.user.email.split('@')[0];
            }
        }
    }
    await fetchOeuvre();
});
        
        return { oeuvre, isLoading, errorMessage, handleAcquisition, toggleFavori, isFavori, showAcquireModal, inquiryForm, isSubmitting, isSuccess, formError, submitInquiry, showLightbox, recommendations, zoomStyle, handleZoom, resetZoom, closeModal: () => showAcquireModal.value = false, currentUser, currentUserName };
    }
}).mount('#oeuvre-app');