const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const photographiesList = ref([]);
        const favorisSet = ref(new Set());
        const currentUser = ref(null);
        const isLoading = ref(true);
        const errorMessage = ref("");

        const filterStatut = ref('disponible');
        const isFilterOpen = ref(false);
        const filterLabels = {
            'disponible': 'En vente (Disponibles)',
            'tous': 'Tout le catalogue',
            'vendu': 'Archives (Vendus)'
        };

        const selectFilter = (val) => {
            filterStatut.value = val;
            isFilterOpen.value = false;
        };

        // Безпечне підключення
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const fetchPhotographies = async () => {
            const { data, error } = await supabase
                .from('oeuvres')
                .select(`id, titre, description, image_url, categorie, prix, statut, artistes ( nom )`)
                .eq('categorie', 'Photographie') 
                .order('created_at', { ascending: false });

            if (error) {
                errorMessage.value = "Erreur de chargement: " + error.message;
            } else if (data) {
                photographiesList.value = data;
            }
        };

        const filteredPhotographies = computed(() => {
            if (filterStatut.value === 'tous') return photographiesList.value;
            if (filterStatut.value === 'disponible') return photographiesList.value.filter(o => !o.statut || o.statut === 'disponible');
            if (filterStatut.value === 'vendu') return photographiesList.value.filter(o => o.statut === 'vendu');
            return photographiesList.value;
        });

        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            const { data } = await supabase.from('favoris').select('oeuvre_id').eq('user_id', currentUser.value.id);
            if (data) favorisSet.value = new Set(data.map(f => f.oeuvre_id));
        };

        const toggleFavori = async (oeuvreId) => {
            if (!currentUser.value) { alert("Veuillez vous connecter."); window.location.href = '/galerie/portail-artiste'; return; } // Оновлено роут
            if (favorisSet.value.has(oeuvreId)) {
                favorisSet.value.delete(oeuvreId);
                const { error } = await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
                if (error) { alert("Erreur: " + error.message); favorisSet.value.add(oeuvreId); }
            } else {
                favorisSet.value.add(oeuvreId);
                const { error } = await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]);
                if (error) { alert("Erreur: " + error.message); favorisSet.value.delete(oeuvreId); }
            }
        };

        const isFavori = (id) => favorisSet.value.has(id);

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchFavoris(); }
            await fetchPhotographies();
            isLoading.value = false;
        });

        return { photographiesList, filteredPhotographies, filterStatut, toggleFavori, isFavori, isLoading, errorMessage, isFilterOpen, filterLabels, selectFilter };
    }
}).mount('#photographies-app');