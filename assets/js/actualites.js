const { createApp, ref, computed, onMounted } = Vue;
createApp({
    setup() {
        const allNews = ref([]); const isLoading = ref(true); const searchQuery = ref(""); const selectedCategory = ref("Tous"); const categories = ['Actualité', 'Nouvelles', 'Exposition', 'Événement'];
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        const fetchNews = async () => { const { data } = await supabase.from('journal').select('*').order('is_featured', { ascending: false }).order('created_at', { ascending: false }); if (data) allNews.value = data; isLoading.value = false; };
        const isDefaultView = computed(() => selectedCategory.value === 'Tous' && searchQuery.value.trim() === '');
        const featuredArticle = computed(() => { if (allNews.value.length === 0) return null; const f = allNews.value.find(a => a.is_featured); return f ? f : allNews.value[0]; });
        const otherArticles = computed(() => { if (!featuredArticle.value) return []; return allNews.value.filter(a => a.id !== featuredArticle.value.id); });
        const formatEventDateRange = (s, e) => { const st = new Date(s).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }); return e ? `Du ${st} au ${new Date(e).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })}` : st; };
        const filteredNews = computed(() => allNews.value.filter(n => (selectedCategory.value === "Tous" || (n.categorie && n.categorie.includes(selectedCategory.value))) && (n.titre.toLowerCase().includes(searchQuery.value.toLowerCase()) || (n.description && n.description.toLowerCase().includes(searchQuery.value.toLowerCase())))));
        const formatDate = (date) => new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
        const truncateText = (t, l) => t && t.length > l ? t.substring(0, l) + "..." : t;
        onMounted(fetchNews);
        return { isLoading, searchQuery, selectedCategory, categories, isDefaultView, featuredArticle, otherArticles, filteredNews, formatDate, formatEventDateRange, truncateText };
    }
}).mount('#actualites-app');