const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const artist = ref(null);
        const artworks = ref([]);
        const isLoading = ref(true);
        const errorMessage = ref("");
        const currentUser = ref(null);
        const favorisSet = ref(new Set());

        const isFollowing = ref(false);
        const followLoading = ref(false);

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const fetchProfileData = async () => {
            const id = window.CURRENT_ITEM_ID;
            if (!id) { errorMessage.value = "Erreur."; isLoading.value = false; return; }

            const { data: a } = await supabase.from('artistes').select('*').eq('id', id).single();
            if (!a) { errorMessage.value = "Introuvable."; isLoading.value = false; return; }
            artist.value = a;

            const { data: w } = await supabase.from('oeuvres').select('*').eq('artiste_id', id).order('created_at', { ascending: false });
            if (w) artworks.value = w;
            isLoading.value = false;
        };

        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            const { data } = await supabase.from('favoris').select('oeuvre_id').eq('user_id', currentUser.value.id);
            if (data) favorisSet.value = new Set(data.map(f => f.oeuvre_id));
        };

        const toggleFavori = async (id) => {
            if (!currentUser.value) { window.location.href = '/portail-client'; return; }
            if (favorisSet.value.has(id)) {
                favorisSet.value.delete(id);
                await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: id });
            } else {
                favorisSet.value.add(id);
                await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: id }]);
            }
        };

        const isFavori = (id) => favorisSet.value.has(id);

        const checkFollowStatus = async () => {
            if (!currentUser.value || !artist.value) return;
            const { data } = await supabase
                .from('user_artist_follows')
                .select('id')
                .eq('user_id', currentUser.value.id)
                .eq('artist_id', artist.value.id)
                .maybeSingle();

            if (data) isFollowing.value = true;
        };

        const toggleFollowArtist = async () => {
            if (!currentUser.value) {
                window.location.href = '/portail-client';
                return;
            }
            if (followLoading.value) return;

            followLoading.value = true;
            try {
                if (isFollowing.value) {
                    await supabase.from('user_artist_follows')
                        .delete()
                        .match({ user_id: currentUser.value.id, artist_id: artist.value.id });
                    isFollowing.value = false;
                } else {
                    await supabase.from('user_artist_follows')
                        .insert([{ user_id: currentUser.value.id, artist_id: artist.value.id }]);
                    isFollowing.value = true;
                }
            } catch (error) {
                console.error("Erreur toggle follow:", error);
            } finally {
                followLoading.value = false;
            }
        };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                currentUser.value = session.user;
            }
            await fetchProfileData();

            if (session) {
                await fetchFavoris();
                await checkFollowStatus();
            }
        });

        return {
            artist, artworks, isLoading, errorMessage,
            toggleFavori, isFavori,
            isFollowing, toggleFollowArtist, followLoading
        };
    }
}).mount('#artist-profile-app');