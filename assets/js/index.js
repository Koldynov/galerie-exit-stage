const { createApp, ref, onMounted, nextTick } = Vue;

createApp({
    setup() {
        const oeuvresList = ref([]);
        const artistesList = ref([]);
        const favorisSet = ref(new Set());
        const currentUser = ref(null);

        // НОВІ ЗМІННІ ДЛЯ ГОЛОВНОЇ СТОРІНКИ
        const latestPost = ref(null);
        const featuredAtelier = ref(null);
        const featuredEvenement = ref(null);

        const activeExposition = ref(null);
        const homepageSettings = ref(null);

        // Безпечне підключення
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const fetchOeuvres = async () => {
            const { data } = await supabase
                .from('oeuvres')
                .select('id, titre, description, image_url, prix, categorie, artistes!inner(nom)')
                .neq('statut', 'vendu')
                .eq('artistes.is_approved', true)
                .eq('is_featured', true)
                .order('created_at', { ascending: false })
                .limit(8);
            if (data) oeuvresList.value = data;
        };

        const fetchArtistes = async () => {
            const { data } = await supabase
                .from('artistes')
                .select('id, nom, titre_pro, image_url')
                .eq('is_approved', true)
                .eq('is_featured', true)
                .order('created_at', { ascending: false })
                .limit(6);
            if (data) artistesList.value = data;
        };

        const fetchFavoris = async () => {
            if (!currentUser.value) return;
            const { data } = await supabase.from('favoris').select('oeuvre_id').eq('user_id', currentUser.value.id);
            if (data) favorisSet.value = new Set(data.map(f => f.oeuvre_id));
        };

        const toggleFavori = async (oeuvreId) => {
            if (!currentUser.value) { window.location.href = 'portail-artiste'; return; }
            if (favorisSet.value.has(oeuvreId)) {
                favorisSet.value.delete(oeuvreId);
                await supabase.from('favoris').delete().match({ user_id: currentUser.value.id, oeuvre_id: oeuvreId });
            }
            else {
                favorisSet.value.add(oeuvreId);
                await supabase.from('favoris').insert([{ user_id: currentUser.value.id, oeuvre_id: oeuvreId }]);
            }
        };

        const isFavori = (id) => favorisSet.value.has(id);

        // 1. Новина для Головної
        const fetchHomepagePost = async () => {
            const { data } = await supabase
                .from('journal')
                .select('*')
                .eq('is_on_homepage', true)
                .limit(1)
                .maybeSingle();
            if (data) latestPost.value = data;
        };

        // 2. Ательє для Головної
        const fetchHomepageAtelier = async () => {
            const { data } = await supabase
                .from('ateliers')
                .select('*')
                .eq('type_experience', 'Atelier')
                .eq('is_on_homepage', true)
                .limit(1)
                .maybeSingle();
            if (data) featuredAtelier.value = data;
        };

        // 3. Івент для Головної
        const fetchHomepageEvenement = async () => {
            const { data } = await supabase
                .from('ateliers')
                .select('*')
                .eq('type_experience', 'Événement')
                .eq('is_on_homepage', true)
                .limit(1)
                .maybeSingle();
            if (data) featuredEvenement.value = data;
        };

        const fetchActiveExposition = async () => {
            const { data } = await supabase
                .from('expositions')
                .select('*')
                .eq('is_active', true)
                .limit(1)
                .maybeSingle();
            if (data) activeExposition.value = data;
        };

        const fetchHomepageSettings = async () => {
            const { data } = await supabase.from('homepage_settings').select('*').eq('id', 1).single();
            if (data) homepageSettings.value = data;
        };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchFavoris(); }

            // Запускаємо всі функції паралельно для швидкості
            await Promise.all([
                fetchOeuvres(),
                fetchArtistes(),
                fetchHomepagePost(),
                fetchHomepageAtelier(),
                fetchHomepageEvenement(),
                fetchActiveExposition(),
                fetchHomepageSettings()
            ]);

            // ЛОГІКА КАРУСЕЛІ (ПРОКРУТКА + ЗНИКНЕННЯ СТРІЛОК)
            nextTick(() => {
                const carousels = [
                    { container: document.getElementById('oeuvres-carousel'), prev: document.getElementById('prev-oeuvres'), next: document.getElementById('next-oeuvres') },
                    { container: document.getElementById('artistes-carousel'), prev: document.getElementById('prev-artistes'), next: document.getElementById('next-artistes') },
                    // АНІМАЦІЯ ДЛЯ ДИНАМІЧНИХ БЛОКІВ (EDITORIAL)
                    // АНІМАЦІЯ ДЛЯ ДИНАМІЧНИХ БЛОКІВ (EDITORIAL)
                    setTimeout(() => {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    // Показуємо плавно, коли доскролили
                                    entry.target.classList.add('is-visible');
                                } else {
                                    // Плавно ховаємо, коли блок зникає з екрану
                                    entry.target.classList.remove('is-visible');
                                }
                            });
                        }, { threshold: 0.15 });

                        document.querySelectorAll('.vue-reveal').forEach(el => observer.observe(el));
                    }, 600) // Чекаємо 0.6 сек, щоб Vue точно встиг відмалювати всі новини
                ];

                carousels.forEach(c => {
                    if (!c.container || !c.prev || !c.next) return;

                    c.prev.style.display = 'flex';
                    c.next.style.display = 'flex';

                    const updateButtons = () => {
                        if (c.container.scrollLeft <= 5) {
                            c.prev.style.opacity = '0';
                            c.prev.style.visibility = 'hidden';
                            c.prev.style.pointerEvents = 'none';
                        } else {
                            c.prev.style.opacity = '1';
                            c.prev.style.visibility = 'visible';
                            c.prev.style.pointerEvents = 'auto';
                        }

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

                    c.prev.addEventListener('click', () => {
                        c.container.scrollBy({ left: -350, behavior: 'smooth' });
                    });

                    c.next.addEventListener('click', () => {
                        c.container.scrollBy({ left: 350, behavior: 'smooth' });
                        c.prev.style.opacity = '1';
                        c.prev.style.visibility = 'visible';
                        c.prev.style.pointerEvents = 'auto';
                    });

                    c.container.addEventListener('scroll', updateButtons);
                    window.addEventListener('resize', updateButtons);

                    setTimeout(updateButtons, 300);

                    c.prev.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';
                    c.next.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';
                });
            });
        });

        // Повертаємо нові змінні, щоб HTML міг їх використовувати
        return {
            oeuvresList, artistesList, toggleFavori, isFavori,
            latestPost, featuredAtelier, featuredEvenement, activeExposition, homepageSettings
        };
    }
}).mount('#home-app');