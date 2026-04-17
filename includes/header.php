<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Galerie EXIT | Accueil</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <canvas id="particle-canvas"></canvas>
    
    <header class="main-header">
        <div class="logo">
            <a href="index.php" class="logo-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 40" class="logo-svg">
                    <g class="power-e" stroke="var(--color-gold)" stroke-width="5" stroke-linecap="round" fill="none">
                        <path d="M 28,10 A 14,14 0 1,0 28,30" />
                        <line x1="21" y1="20" x2="36" y2="20" />
                    </g>
                    <text x="40" y="32" font-family="'Helvetica Neue', Arial, sans-serif" font-weight="900" font-size="34" fill="currentColor">xit</text>
                </svg>
            </a>
        </div>
        
        <nav class="main-nav">
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                
                <li class="dropdown">
                    <a href="peintures.php">Peintures</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="peintures.php">Art Contemporain</a>
                                <a href="peintures.php">Abstrait</a>
                                <a href="peintures.php">Figuratif</a>
                            </div>
                            <div class="column">
                                <a href="peintures.php">Street Art</a>
                                <a href="peintures.php" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tout voir &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="sculptures.php">Sculptures</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="sculptures.php">Bronze</a>
                                <a href="sculptures.php">Moderne</a>
                                <a href="sculptures.php">Bois</a>
                            </div>
                            <div class="column">
                                <a href="sculptures.php">Statues</a>
                                <a href="sculptures.php" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tout voir &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="photographies.php">Photographies</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="#">Noir et Blanc</a>
                                <a href="#">Argentique</a>
                            </div>
                            <div class="column">
                                <a href="#">Paysages</a>
                                <a href="#" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tout voir &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="artistes.php">Artistes</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="artistes.php">Peintres</a>
                                <a href="artistes.php">Sculpteurs</a>
                                <a href="artistes.php">Photographes</a>
                            </div>
                            <div class="column">
                                <a href="artistes.php">Nouvelles Entrées</a>
                                <a href="artistes.php" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tous les artistes &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>
                
                <li><a href="actualites.php">Le Journal</a></li>
            </ul>
        </nav>

        <div class="user-actions">
            
            <div id="search-app" class="search-container" v-cloak>
                
                <div class="search-box">
                    <input type="text" 
                           v-model="query" 
                           @input="handleInput" 
                           @focus="isFocused = true" 
                           @blur="handleBlur" 
                           placeholder="Rechercher une œuvre, un artiste...">
                    <button><i class="fas fa-search"></i></button>
                </div>

                <transition name="search-fade">
                    <div v-show="isFocused && query.length >= 2" class="search-results-dropdown">
                        
                        <div v-if="isLoading" class="search-state">
                            <i class="fas fa-spinner fa-spin" style="color: var(--color-gold); margin-right: 10px;"></i> Recherche en cours...
                        </div>
                        
                        <div v-else-if="results.length === 0" class="search-state">
                            Aucun résultat pour "{{ query }}"
                        </div>
                        
                        <div v-else class="search-results-list">
                            <a v-for="item in results" :key="item.id + item.type" :href="item.url" class="search-result-item">
                                <div class="search-img-box">
                                    <img :src="item.image" :alt="item.title">
                                </div>
                                <div class="search-info">
                                    <h4>{{ item.title }}</h4>
                                    <span>{{ item.type }}</span>
                                </div>
                            </a>
                        </div>

                    </div>
                </transition>
            </div>

            <a href="login.php" id="header-user-link"><i class="fas fa-user"></i></a>
            <a href="favoris.php"><i class="fas fa-heart"></i></a>
        </div>
    </header>

    <script>
    // 1. Анімація хедеру та авторизація
    document.addEventListener('DOMContentLoaded', async () => {
    const header = document.querySelector('.main-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 150) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
    });

    const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
    const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
    
    const supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
    const { data: { session } } = await supabaseClient.auth.getSession();

    if (session) {
        const userLink = document.getElementById('header-user-link');
        const userEmail = session.user.email;

        if (userLink) {
            // ПЕРЕВІРКА НА АДМІНА
            if (userEmail === 'admin@gmail.com') {
                userLink.href = 'dashboard.php';
                userLink.innerHTML = '<i class="fas fa-chart-line" title="Tableau de bord"></i>'; // Іконка графіка для адміна
            } else {
                userLink.href = 'mon-compte.php';
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

            const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
            const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
            const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

            const handleInput = () => {
                if (query.value.length < 2) {
                    results.value = [];
                    return;
                }

                isLoading.value = true;
                
                // Debounce (чекаємо 300мс після останнього натискання клавіші, перш ніж йти в базу)
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch();
                }, 300);
            };

            const performSearch = async () => {
                let combinedResults = [];

                // 1. Шукаємо картини та скульптури (oeuvres)
                const { data: oeuvresData } = await supabase
                    .from('oeuvres')
                    .select('id, titre, categorie, image_url')
                    .ilike('titre', `%${query.value}%`) // Пошук по буквам, незалежно від регістру
                    .limit(3);

                if (oeuvresData) {
                    const oeuvresFormatted = oeuvresData.map(o => ({
                        id: o.id,
                        title: o.titre,
                        type: o.categorie, // 'Peinture', 'Sculpture' і тд
                        image: o.image_url || 'https://via.placeholder.com/50',
                        url: `oeuvre.php?id=${o.id}`
                    }));
                    combinedResults = [...combinedResults, ...oeuvresFormatted];
                }

                // 2. Шукаємо артистів (artistes)
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
                        url: `profil-artiste.php?id=${a.id}`
                    }));
                    combinedResults = [...combinedResults, ...artistesFormatted];
                }

                results.value = combinedResults;
                isLoading.value = false;
            };

            // Затримка приховування меню, щоб встигнути клікнути по посиланню
            const handleBlur = () => {
                setTimeout(() => {
                    isFocused.value = false;
                }, 200);
            };

            return { query, results, isLoading, isFocused, handleInput, handleBlur };
        }
    }).mount('#search-app');
    </script>