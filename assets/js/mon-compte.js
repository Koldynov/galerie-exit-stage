const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const currentUser = ref(null);
        const userProfile = ref({});
        const isLoading = ref(true);
        const currentTab = ref('profil'); 

        const favorisList = ref([]);
        const demandesList = ref([]);
        const commandesList = ref([]);
        const recommendedList = ref([]); 

        const isEditing = ref(false);
        const isSaving = ref(false);
        const editForm = ref({ first_name: '', last_name: '', phone: '' });
        const successMsg = ref('');
        const errorMsg = ref('');

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const displayFullName = computed(() => {
            if (userProfile.value.first_name || userProfile.value.last_name) return `${userProfile.value.first_name || ''} ${userProfile.value.last_name || ''}`.trim();
            return 'Utilisateur';
        });

        const userInitials = computed(() => {
            let initials = '';
            if (userProfile.value.first_name) initials += userProfile.value.first_name.charAt(0).toUpperCase();
            if (userProfile.value.last_name) initials += userProfile.value.last_name.charAt(0).toUpperCase();
            if (!initials && currentUser.value) initials = currentUser.value.email.charAt(0).toUpperCase();
            return initials || 'U';
        });

        const formatDate = (dateString) => new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });

        const getDemandeStatusClass = (statut) => {
            if (!statut || statut === 'Nouveau') return 'new';
            if (statut === 'En cours') return 'processing';
            if (statut === 'Conclu') return 'success';
            return 'canceled';
        };

        const fetchClientData = async () => {
    try {
        if (currentUser.value.user_metadata) {
            userProfile.value = {
                first_name: currentUser.value.user_metadata.first_name || '',
                last_name: currentUser.value.user_metadata.last_name || '',
                phone: currentUser.value.user_metadata.phone || ''
            };
        }

        // 1. Завантажуємо Обране
        const { data: favData } = await supabase
            .from('favoris')
            .select('*, oeuvres(id, titre, image_url, prix, statut)')
            .eq('user_id', currentUser.value.id);
        if (favData) favorisList.value = favData.filter(f => f.oeuvres);

        // 2. Завантажуємо Запити
        const { data: demData } = await supabase
            .from('demandes')
            .select('*, oeuvres(titre, image_url)')
            .eq('email', currentUser.value.email)
            .order('created_at', { ascending: false });
        if (demData) demandesList.value = demData;

        // 3. ЗАВАНТАЖУЄМО ПОКУПКИ (Зверни увагу на фільтрацію)
        // Ми шукаємо замовлення, де email збігається з поточним користувачем
        const { data: cmdData, error: cmdError } = await supabase
            .from('commandes')
            .select('*, oeuvres(id, titre, image_url)') // Обов'язково додаємо image_url
            .eq('user_email', currentUser.value.email)
            .order('created_at', { ascending: false });

        if (cmdError) throw cmdError;
        commandesList.value = cmdData || [];

        // 4. Рекомендації
        const { data: recData } = await supabase
            .from('oeuvres')
            .select('id, titre, image_url, prix')
            .eq('statut', 'disponible')
            .limit(4);
        if (recData) recommendedList.value = recData.sort(() => 0.5 - Math.random());

    } catch (err) {
        console.error("Помилка завантаження даних клієнта:", err);
    } finally {
        isLoading.value = false;
    }
};
        const removeFavori = async (oeuvreId) => {
            favorisList.value = favorisList.value.filter(f => f.oeuvres.id !== oeuvreId);
            await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
        };

        const startEdit = () => { successMsg.value = ''; errorMsg.value = ''; editForm.value = { ...userProfile.value }; isEditing.value = true; };
        const cancelEdit = () => { isEditing.value = false; };

        const saveProfile = async () => {
            isSaving.value = true; successMsg.value = ''; errorMsg.value = '';
            try {
                const { error } = await supabase.auth.updateUser({ data: { first_name: editForm.value.first_name, last_name: editForm.value.last_name, phone: editForm.value.phone } });
                if (error) throw error;
                userProfile.value = { ...editForm.value };
                successMsg.value = "Profil mis à jour !";
                setTimeout(() => { isEditing.value = false; successMsg.value = ''; }, 1500);
            } catch (err) { errorMsg.value = err.message; } finally { isSaving.value = false; }
        };

        const handleLogout = async () => { await supabase.auth.signOut(); window.location.href = '/galerie/'; }; // ОНОВЛЕНО

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchClientData(); } 
            else { window.location.href = '/galerie/portail-artiste'; } // Якщо не залогінений
        });

        // Перевірка URL при завантаженні сторінки
if (window.location.hash === '#favoris') {
    currentTab.value = 'favoris';
}

// Перевірка, якщо користувач клікне на сердечко, вже знаходячись на сторінці
window.addEventListener('hashchange', () => {
    if (window.location.hash === '#favoris') {
        currentTab.value = 'favoris';
    }
});

        return { isLoading, currentUser, userProfile, currentTab, favorisList, demandesList, commandesList, recommendedList, userInitials, displayFullName, formatDate, getDemandeStatusClass, removeFavori, isEditing, isSaving, editForm, successMsg, errorMsg, startEdit, cancelEdit, saveProfile, handleLogout };
    }
}).mount('#client-dashboard');