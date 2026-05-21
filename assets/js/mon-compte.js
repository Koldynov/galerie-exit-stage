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

        // Змінна для підписок (назва точно збігається з HTML)
        const artistesSuivis = ref([]);
        const loadingArtistes = ref(false);

        const isArtist = ref(false);
        const isAdmin = ref(false);

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

                const { data: artistData } = await supabase
                    .from('artistes')
                    .select('id')
                    .eq('auth_user_id', currentUser.value.id)
                    .maybeSingle();

                if (artistData) {
                    isArtist.value = true;
                }

                // --- НОВИЙ КОД: Перевірка чи є юзер адміном ---
                // Заміни email на той, який ти використовуєш для входу в адмінку
                if (currentUser.value.email === 'admin@gmail.com') {
                    isAdmin.value = true;
                }

                const { data: favData } = await supabase
                    .from('favoris')
                    .select('*, oeuvres(id, titre, image_url, prix, statut)')
                    .eq('user_id', currentUser.value.id);
                if (favData) favorisList.value = favData.filter(f => f.oeuvres);

                const { data: demData } = await supabase
                    .from('demandes')
                    .select('*, oeuvres(titre, image_url)')
                    .eq('email', currentUser.value.email)
                    .order('created_at', { ascending: false });
                if (demData) demandesList.value = demData;

                const { data: cmdData, error: cmdError } = await supabase
                    .from('commandes')
                    .select('*, oeuvres(id, titre, image_url, artistes(nom))')
                    .eq('user_email', currentUser.value.email)
                    .order('created_at', { ascending: false });

                if (cmdError) throw cmdError;
                commandesList.value = cmdData || [];

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

        // Нова функція для завантаження художників (Один запит замість двох)
        const fetchArtistesSuivis = async () => {
            if (!currentUser.value) return;
            loadingArtistes.value = true;

            try {
                const { data, error } = await supabase
                    .from('user_artist_follows')
                    .select('id, artist_id, artistes(id, nom, image_url, titre_pro)')
                    .eq('user_id', currentUser.value.id);

                if (error) throw error;
                artistesSuivis.value = data || [];
            } catch (error) {
                console.error("Помилка завантаження художників:", error);
            } finally {
                loadingArtistes.value = false;
            }
        };

        const removeFavori = async (oeuvreId) => {
            favorisList.value = favorisList.value.filter(f => f.oeuvres.id !== oeuvreId);
            await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
        };

        const unfollowArtist = async (artistId) => {
            if (!currentUser.value) return;
            try {
                await supabase
                    .from('user_artist_follows')
                    .delete()
                    .match({ user_id: currentUser.value.id, artist_id: artistId });

                artistesSuivis.value = artistesSuivis.value.filter(a => a.artist_id !== artistId);
            } catch (error) {
                console.error("Помилка відписки:", error);
            }
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

        const handleLogout = async () => { await supabase.auth.signOut(); window.location.href = '/galerie/'; };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                currentUser.value = session.user;
                await fetchClientData();
                await fetchArtistesSuivis(); // Виклик нашої нової функції
            }
            else { window.location.href = '/galerie/portail-artiste'; }
        });

        if (window.location.hash === '#favoris') {
            currentTab.value = 'favoris';
        }

        window.addEventListener('hashchange', () => {
            if (window.location.hash === '#favoris') {
                currentTab.value = 'favoris';
            }
        });

        return {
            isLoading, currentUser, userProfile, currentTab,
            favorisList, demandesList, commandesList, recommendedList,
            artistesSuivis, loadingArtistes, unfollowArtist, // Додано до return
            userInitials, displayFullName, formatDate, getDemandeStatusClass, removeFavori, isArtist, isAdmin,
            isEditing, isSaving, editForm, successMsg, errorMsg, startEdit, cancelEdit, saveProfile, handleLogout
        };
    }
}).mount('#client-dashboard');