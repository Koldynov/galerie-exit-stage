document.addEventListener('DOMContentLoaded', async () => {
    // 1. Анімація хедеру
    const header = document.querySelector('.main-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 150) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
    });

    // Використовуємо ключі з конфіга (без хардкоду!)
    const supabaseClient = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
    const { data: { session } } = await supabaseClient.auth.getSession();

    if (session) {
        const userLink = document.getElementById('header-user-link');
        const userEmail = session.user.email;

        if (userLink) {
            // ПЕРЕВІРКА НА АДМІНА
            if (userEmail === 'admin@gmail.com') {
                userLink.href = '/galerie/dashboard'; // Новий роут
                userLink.innerHTML = '<i class="fas fa-chart-line" title="Tableau de bord"></i>'; 
            } else {
                userLink.href = '/galerie/mon-compte'; // Новий роут
            }
        }
    }
});

// 2. VUE APP ДЛЯ ЖИВОГО ПОШУКУ
const { createApp: createSearchApp, ref: searchRef } = Vue;

createSearchApp({
    setup() {
        const query = searchRef('');
        const results = searchRef([]);
        const isLoading = searchRef(false);
        const isFocused = searchRef(false);
        let searchTimeout = null;

        // Використовуємо безпечні ключі
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const handleInput = () => {
            if (query.value.length < 2) {
                results.value = [];
                return;
            }
            isLoading.value = true;
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 300);
        };

        const performSearch = async () => {
            let combinedResults = [];

            // 1. Шукаємо картини та скульптури
            const { data: oeuvresData } = await supabase
                .from('oeuvres')
                .select('id, titre, categorie, image_url')
                .ilike('titre', `%${query.value}%`)
                .limit(3);

            if (oeuvresData) {
                const oeuvresFormatted = oeuvresData.map(o => ({
                    id: o.id,
                    title: o.titre,
                    type: o.categorie,
                    image: o.image_url || 'https://via.placeholder.com/50',
                    url: `oeuvre/${o.id}` // ОНОВЛЕНО: Красивий URL
                }));
                combinedResults = [...combinedResults, ...oeuvresFormatted];
            }

            // 2. Шукаємо артистів
            const { data: artistesData } = await supabase
                .from('artistes')
                .select('id, nom, image_url')
                .ilike('nom', `%${query.value}%`)
                .limit(2);

            if (artistesData) {
                const artistesFormatted = artistesData.map(a => ({
                    id: a.id,
                    title: a.nom,
                    type: 'Artiste',
                    image: a.image_url || 'https://via.placeholder.com/50',
                    url: `artiste/${a.id}` // ОНОВЛЕНО: Красивий URL
                }));
                combinedResults = [...combinedResults, ...artistesFormatted];
            }

            results.value = combinedResults;
            isLoading.value = false;
        };

        const handleBlur = () => {
            setTimeout(() => {
                isFocused.value = false;
            }, 200);
        };

        return { query, results, isLoading, isFocused, handleInput, handleBlur };
    }
}).mount('#search-app');