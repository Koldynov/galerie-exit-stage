const { createApp, ref, onMounted } = Vue;
createApp({
    setup() {
        const oeuvre = ref(null); 
        const isLoading = ref(true); 
        const errorMessage = ref(""); 
        const currentUser = ref(null); 
        const currentUserName = ref('Membre Connecté'); 
        const isFavori = ref(false);
        const showAcquireModal = ref(false); 
        const inquiryForm = ref({ nom: '', email: '', message: '' }); 
        const isSubmitting = ref(false); 
        const isSuccess = ref(false); 
        const formError = ref("");

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        
        const fetchOeuvre = async () => { 
            const id = window.CURRENT_ITEM_ID; 
            if (!id) { errorMessage.value = "Erreur."; isLoading.value = false; return; } 
            const { data } = await supabase.from('oeuvres').select('*, artistes(id, nom)').eq('id', id).single(); 
            if (!data) errorMessage.value = "Introuvable."; 
            else { 
                oeuvre.value = data; 
                if (currentUser.value) { 
                    const { data: f } = await supabase.from('favoris').select('id').eq('user_id', currentUser.value.id).eq('oeuvre_id', id).single(); 
                    if (f) isFavori.value = true; 
                } 
            } 
            isLoading.value = false; 
        };
        
        const toggleFavori = async () => { 
            if (!currentUser.value) { 
                // ОНОВЛЕНО: Чіткий шлях до порталу
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
            let em = currentUser.value ? currentUser.value.email : inquiryForm.value.email; 
            let n = currentUser.value ? currentUserName.value : inquiryForm.value.nom; 
            if (!em) { formError.value = "Email requis."; return; } 
            isSubmitting.value = true; 
            const { error } = await supabase.from('demandes').insert([{ oeuvre_id: oeuvre.value.id, nom: n, email: em, message: inquiryForm.value.message }]); 
            isSubmitting.value = false; 
            if (error) formError.value = "Erreur."; 
            else isSuccess.value = true; 
        };
        
        onMounted(async () => { 
            const { data: { session } } = await supabase.auth.getSession(); 
            if (session) { 
                currentUser.value = session.user; 
                const { data: a } = await supabase.from('artistes').select('nom').eq('auth_user_id', currentUser.value.id).single(); 
                if (a && a.nom) currentUserName.value = a.nom; 
                else if (session.user.user_metadata?.full_name) currentUserName.value = session.user.user_metadata.full_name; 
            } 
            await fetchOeuvre(); 
        });
        
        return { oeuvre, isLoading, errorMessage, handleAcquisition, toggleFavori, isFavori, showAcquireModal, inquiryForm, isSubmitting, isSuccess, formError, submitInquiry, closeModal: () => showAcquireModal.value = false, currentUser, currentUserName };
    }
}).mount('#oeuvre-app');