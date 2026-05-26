const { createApp, ref, onMounted, nextTick } = Vue;
createApp({
    setup() {
        const oeuvresList = ref([]); const artistesList = ref([]); const favorisSet = ref(new Set()); const currentUser = ref(null);
        
        // Безпечне підключення
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        
        const fetchOeuvres = async () => { 
            const { data } = await supabase
                .from('oeuvres')
                .select('id, titre, description, image_url, prix, categorie, artistes!inner(nom)') // Додали !inner
                .neq('statut', 'vendu')
                .eq('artistes.is_approved', true) // Тільки від підтверджених артистів
                .eq('is_featured', true) // ТІЛЬКИ ТІ, ЩО З ЗІРОЧКОЮ
                .order('created_at', { ascending: false })
                .limit(8); 
            if (data) oeuvresList.value = data; 
        };

        const fetchArtistes = async () => { 
            const { data } = await supabase
                .from('artistes')
                .select('id, nom, titre_pro, image_url')
                .eq('is_approved', true) // Тільки підтверджені артисти
                .eq('is_featured', true) // ТІЛЬКИ ТІ, ЩО З ЗІРОЧКОЮ
                .order('created_at', { ascending: false })
                .limit(6); 
            if (data) artistesList.value = data; 
        };

        const fetchFavoris = async () => { if (!currentUser.value) return; const { data } = await supabase.from('favoris').select('oeuvre_id').eq('user_id', currentUser.value.id); if (data) favorisSet.value = new Set(data.map(f => f.oeuvre_id)); };
        
        const toggleFavori = async (oeuvreId) => { 
            if (!currentUser.value) { window.location.href = 'portail-artiste'; return; } 
            if (favorisSet.value.has(oeuvreId)) { favorisSet.value.delete(oeuvreId); await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId }); } 
            else { favorisSet.value.add(oeuvreId); await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]); } 
        };
        
        const isFavori = (id) => favorisSet.value.has(id);
        
        onMounted(async () => { 
            const { data: { session } } = await supabase.auth.getSession(); 
            if (session) { currentUser.value = session.user; await fetchFavoris(); } 
            await Promise.all([fetchOeuvres(), fetchArtistes()]); 
            
            // ЛОГІКА КАРУСЕЛІ (ПРОКРУТКА + ЗНИКНЕННЯ СТРІЛОК)
            // ЛОГІКА КАРУСЕЛІ (ПРОКРУТКА + ЗНИКНЕННЯ СТРІЛОК)
            nextTick(() => { 
                const carousels = [
                    { container: document.getElementById('oeuvres-carousel'), prev: document.getElementById('prev-oeuvres'), next: document.getElementById('next-oeuvres') },
                    { container: document.getElementById('artistes-carousel'), prev: document.getElementById('prev-artistes'), next: document.getElementById('next-artistes') }
                ];

                carousels.forEach(c => {
                    if (!c.container || !c.prev || !c.next) return;

                    // Змушуємо стрілки бути видимими для JS (на випадок display: none в CSS)
                    c.prev.style.display = 'flex';
                    c.next.style.display = 'flex';

                    // 1. Функція, яка перевіряє, коли ховати стрілки
                    const updateButtons = () => {
                        // Логіка лівої стрілки (ховаємо, якщо ми на початку, < 5px)
                        if (c.container.scrollLeft <= 5) {
                            c.prev.style.opacity = '0';
                            c.prev.style.visibility = 'hidden'; // Додали жорстке приховування
                            c.prev.style.pointerEvents = 'none';
                        } else {
                            c.prev.style.opacity = '1';
                            c.prev.style.visibility = 'visible'; // Жорстко показуємо
                            c.prev.style.pointerEvents = 'auto';
                        }
                        
                        // Логіка правої стрілки (ховаємо, якщо ми в самому кінці)
                        const maxScrollLeft = c.container.scrollWidth - c.container.clientWidth;
                        if (c.container.scrollLeft >= maxScrollLeft - 5) {
                            c.next.style.opacity = '0';
                            c.next.style.visibility = 'hidden';
                            c.next.style.pointerEvents = 'none';
                        } else {
                            c.next.style.opacity = '1';
                            c.next.style.visibility = 'visible';
                            c.next.style.pointerEvents = 'auto';
                        }
                    };

                    // 2. Обробка кліків по стрілках
                    c.prev.addEventListener('click', () => {
                        c.container.scrollBy({ left: -350, behavior: 'smooth' });
                    });
                    
                    c.next.addEventListener('click', () => {
                        c.container.scrollBy({ left: 350, behavior: 'smooth' });
                        // МИТТЄВО показуємо ліву стрілку, не чекаючи завершення скролу!
                        c.prev.style.opacity = '1';
                        c.prev.style.visibility = 'visible';
                        c.prev.style.pointerEvents = 'auto';
                    });

                    // Слухаємо скрол мишкою або пальцями (свайп на телефоні)
                    c.container.addEventListener('scroll', updateButtons);
                    window.addEventListener('resize', updateButtons);
                    
                    // Даємо Vue 300мс на відмальовку картинок, і тільки тоді перевіряємо кнопки
                    setTimeout(updateButtons, 300); 
                    
                    // Додаємо красиву анімацію зникнення
                    c.prev.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';
                    c.next.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';
                });
            }); 
        });
        
        return { oeuvresList, artistesList, toggleFavori, isFavori };
    }
}).mount('#home-app');