const { createApp, ref, computed, onMounted, onUnmounted, watch } = Vue;

createApp({
    setup() {
        const peinturesList = ref([]);
        const favorisSet = ref(new Set());
        const currentUser = ref(null);
        const isLoading = ref(true);
        const errorMessage = ref("");

        const isArtisteOpen = ref(false);
        const isStatutOpen = ref(false);

        const filters = ref({
            recherche: "",
            minPrix: null,
            maxPrix: null,
            artisteId: "",
            statut: "disponible",
            sousCategorie: ""
        });
        const artistesList = ref([]);

        const filterLabels = {
            'disponible': 'En vente (Disponibles)',
            'tous': 'Tout le catalogue',
            'vendu': 'Archives (Vendus)'
        };

        const page = ref(0);
        const pageSize = 12;
        const isLoadingMore = ref(false);
        const hasMoreData = ref(true);
        const loadMoreTrigger = ref(null);

        let observer = null;
        let debounceTimeout = null;

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const closeDropdowns = () => {
            isArtisteOpen.value = false;
            isStatutOpen.value = false;
        };

        const getArtisteLabel = (id) => {
            if (!id) return "Tous les artistes";
            const artiste = artistesList.value.find(a => a.id === id);
            return artiste ? artiste.nom : "Tous les artistes";
        };

        const selectArtiste = (id) => {
            filters.value.artisteId = id;
            isArtisteOpen.value = false;
        };

        const selectStatut = (statut) => {
            filters.value.statut = statut;
            isStatutOpen.value = false;
        };

        const fetchArtistes = async () => {
            const { data } = await supabase.from('artistes').select('id, nom').order('nom');
            if (data) artistesList.value = data;
        };

        const fetchPeintures = async (isLoadMore = false) => {
            if (!isLoadMore) {
                page.value = 0;
                peinturesList.value = [];
                hasMoreData.value = true;
            } else {
                isLoadingMore.value = true;
            }

            try {
                // Використовуємо потужний вбудований фільтр Supabase
                let query = supabase
                    .from('oeuvres')
                    .select('*, artistes!inner(id, nom)') 
                    .eq('categorie', 'Peinture')
                    .eq('artistes.is_approved', true);

                if (filters.value.statut && filters.value.statut !== 'tous') {
                    query = query.eq('statut', filters.value.statut);
                }
                if (filters.value.minPrix) {
                    query = query.gte('prix', Number(filters.value.minPrix));
                }
                if (filters.value.maxPrix) {
                    query = query.lte('prix', Number(filters.value.maxPrix));
                }
                if (filters.value.artisteId) {
                    query = query.eq('artiste_id', filters.value.artisteId);
                }
                if (filters.value.recherche) {
                    query = query.ilike('titre', `%${filters.value.recherche}%`);
                }
                if (filters.value.sousCategorie) {
                    query = query.ilike('sous_categorie', `%${filters.value.sousCategorie}%`);
                }

                const from = page.value * pageSize;
                const to = from + pageSize - 1;
                query = query.order('created_at', { ascending: false }).range(from, to);

                const { data, error } = await query;

                if (error) throw error;

                if (data) {
                    if (data.length < pageSize) {
                        hasMoreData.value = false;
                    }

                    if (isLoadMore) {
                        peinturesList.value.push(...data);
                    } else {
                        peinturesList.value = data;
                    }
                }
            } catch (err) {
                console.warn("⚠️ Supabase indisponible ou erreur SQL:", err.message);
                console.info("🔄 Chargement du backup local...");

                try {
                    const response = await fetch('/assets/data/fallback-peintures.json');
                    if (!response.ok) throw new Error("Backup introuvable");

                    let backupData = await response.json();

                    if (filters.value.recherche) {
                        const searchLower = filters.value.recherche.toLowerCase();
                        backupData = backupData.filter(o => o.titre.toLowerCase().includes(searchLower));
                    }
                    if (filters.value.statut === 'disponible') {
                        backupData = backupData.filter(o => !o.statut || o.statut === 'disponible');
                    } else if (filters.value.statut === 'vendu') {
                        backupData = backupData.filter(o => o.statut === 'vendu');
                    }
                    if (filters.value.minPrix) {
                        backupData = backupData.filter(o => o.prix >= filters.value.minPrix);
                    }
                    if (filters.value.maxPrix) {
                        backupData = backupData.filter(o => o.prix <= filters.value.maxPrix);
                    }

                    const from = page.value * pageSize;
                    const to = from + pageSize;
                    const paginatedBackup = backupData.slice(from, to);

                    if (paginatedBackup.length < pageSize) hasMoreData.value = false;

                    if (isLoadMore) {
                        peinturesList.value.push(...paginatedBackup);
                    } else {
                        peinturesList.value = paginatedBackup;
                    }
                    errorMessage.value = "";
                } catch (fallbackErr) {
                    errorMessage.value = "La galerie est en cours de mise à jour. Veuillez patienter.";
                }
            }

            if (isLoadMore) {
                isLoadingMore.value = false;
            }
        };

        watch(filters, () => {
            isLoading.value = true;
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(async () => {
                await fetchPeintures(false);
                isLoading.value = false;
            }, 500);
        }, { deep: true });

        const loadMore = async () => {
            if (isLoadingMore.value || !hasMoreData.value) return;
            page.value++;
            await fetchPeintures(true);
        };

        const setupObserver = () => {
            const options = { root: null, rootMargin: '100px', threshold: 0.1 };
            observer = new IntersectionObserver((entries) => {
                const target = entries[0];
                if (target.isIntersecting && hasMoreData.value) loadMore();
            }, options);

            setTimeout(() => {
                if (loadMoreTrigger.value) observer.observe(loadMoreTrigger.value);
            }, 500);
        };

        const filteredPeintures = computed(() => peinturesList.value);

        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            const { data } = await supabase.from('favoris').select('oeuvre_id').eq('user_id', currentUser.value.id);
            if (data) favorisSet.value = new Set(data.map(f => f.oeuvre_id));
        };

        const toggleFavori = async (oeuvreId) => {
            if (!currentUser.value) { alert("Veuillez vous connecter."); window.location.href = '/galerie/portail-artiste'; return; }
            if (favorisSet.value.has(oeuvreId)) {
                favorisSet.value.delete(oeuvreId);
                await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
            } else {
                favorisSet.value.add(oeuvreId);
                await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]);
            }
        };

        const isFavori = (id) => favorisSet.value.has(id);

        onUnmounted(() => {
            if (observer && loadMoreTrigger.value) observer.unobserve(loadMoreTrigger.value);
        });

        onMounted(async () => {
            const urlParams = new URLSearchParams(window.location.search);
            const sousCat = urlParams.get('sous_categorie');
            if (sousCat) {
                filters.value.sousCategorie = sousCat;
            }

            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchFavoris(); }

            await fetchArtistes();
            await fetchPeintures();

            isLoading.value = false;
            setupObserver();
        });

        return {
            peinturesList, filteredPeintures, filters, artistesList, filterLabels,
            isArtisteOpen, isStatutOpen, closeDropdowns, getArtisteLabel, selectArtiste, selectStatut,
            toggleFavori, isFavori, isLoading, errorMessage,
            loadMoreTrigger, isLoadingMore, hasMoreData
        };
    }
}).mount('#peintures-app');