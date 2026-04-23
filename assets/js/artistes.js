const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const artistesList = ref([]);
        const isLoading = ref(true);
        const errorMessage = ref("");

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const fetchArtistes = async () => {
            isLoading.value = true;
            
            // Вибираємо тільки ТИХ, кого адмін вже схвалив
            const { data, error } = await supabase
                .from('artistes')
                .select('*')
                .eq('is_approved', true) 
                .order('nom', { ascending: true });

            if (error) {
                errorMessage.value = "Impossible de charger les artistes : " + error.message;
            } else if (data) {
                artistesList.value = data;
            }
            isLoading.value = false;
        };

        onMounted(() => {
            fetchArtistes();
        });

        return { artistesList, isLoading, errorMessage };
    }
}).mount('#artistes-page-app');