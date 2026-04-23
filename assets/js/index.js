const { createApp, ref, onMounted, nextTick } = Vue;
createApp({
    setup() {
        const oeuvresList = ref([]); const artistesList = ref([]); const favorisSet = ref(new Set()); const currentUser = ref(null);
        
        // Безпечне підключення
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        
        const fetchOeuvres = async () => { const { data } = await supabase.from('oeuvres').select('id, titre, description, image_url, prix, categorie, artistes(nom)').eq('is_featured', true).order('created_at', { ascending: false }).limit(8); if (data) oeuvresList.value = data; };
        const fetchArtistes = async () => { const { data } = await supabase.from('artistes').select('id, nom, titre_pro, image_url').eq('is_featured', true).order('created_at', { ascending: false }).limit(6); if (data) artistesList.value = data; };
        const fetchFavoris = async () => { if (!currentUser.value) return; const { data } = await supabase.from('favoris').select('oeuvre_id').eq('user_id', currentUser.value.id); if (data) favorisSet.value = new Set(data.map(f => f.oeuvre_id)); };
        
        const toggleFavori = async (oeuvreId) => { 
            if (!currentUser.value) { window.location.href = 'portail-artiste'; return; } // Оновлено роут
            if (favorisSet.value.has(oeuvreId)) { favorisSet.value.delete(oeuvreId); await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId }); } 
            else { favorisSet.value.add(oeuvreId); await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]); } 
        };
        
        const isFavori = (id) => favorisSet.value.has(id);
        
        onMounted(async () => { 
            const { data: { session } } = await supabase.auth.getSession(); 
            if (session) { currentUser.value = session.user; await fetchFavoris(); } 
            await Promise.all([fetchOeuvres(), fetchArtistes()]); 
            nextTick(() => { 
                const sc = (id, p, n) => { const c = document.getElementById(id), pb = document.getElementById(p), nb = document.getElementById(n); if(c && pb && nb) { pb.addEventListener('click', ()=>c.scrollBy({left:-300, behavior:'smooth'})); nb.addEventListener('click', ()=>c.scrollBy({left:300, behavior:'smooth'})); } }; 
                sc('oeuvres-carousel', 'prev-oeuvres', 'next-oeuvres'); sc('artistes-carousel', 'prev-artistes', 'next-artistes'); 
            }); 
        });
        
        return { oeuvresList, artistesList, toggleFavori, isFavori };
    }
}).mount('#home-app');