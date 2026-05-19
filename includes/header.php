<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Galerie EXIT | Accueil</title>
    
    <base href="/galerie/">

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    
    <script>
    window.APP_CONFIG = {
        SUPABASE_URL: "<?php echo SUPABASE_URL; ?>",
        SUPABASE_ANON_KEY: "<?php echo SUPABASE_ANON_KEY; ?>"
    };
    </script>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <canvas id="particle-canvas"></canvas>
    
    <header class="main-header">
        <div class="logo">
            <a href="./" class="logo-link">
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
            <button id="mobile-menu-close" class="mobile-menu-close-btn" style="display: none; background: none; border: none; color: white; font-size: 2rem; cursor: pointer;"><i class="fas fa-times"></i></button>
            
            <ul class="nav-links">
                <li><a href="./">Accueil</a></li>
                
                <li class="dropdown">
                    <a href="peintures">Peintures</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="peintures">Art Contemporain</a>
                                <a href="peintures">Abstrait</a>
                                <a href="peintures">Figuratif</a>
                            </div>
                            <div class="column">
                                <a href="peintures">Street Art</a>
                                <a href="peintures" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tout voir &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="sculptures">Sculptures</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="sculptures">Bronze</a>
                                <a href="sculptures">Moderne</a>
                                <a href="sculptures">Bois</a>
                            </div>
                            <div class="column">
                                <a href="sculptures">Statues</a>
                                <a href="sculptures" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tout voir &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="photographies">Photographies</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="photographies">Noir et Blanc</a>
                                <a href="photographies">Argentique</a>
                            </div>
                            <div class="column">
                                <a href="photographies">Paysages</a>
                                <a href="photographies" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tout voir &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="dropdown">
                    <a href="artistes">Artistes</a>
                    <div class="dropdown-content">
                        <div class="dropdown-grid">
                            <div class="column">
                                <a href="artistes">Peintres</a>
                                <a href="artistes">Sculpteurs</a>
                                <a href="artistes">Photographes</a>
                            </div>
                            <div class="column">
                                <a href="artistes">Nouvelles Entrées</a>
                                <a href="artistes" style="color: var(--color-gold); font-weight: bold; margin-top: auto;">Tous les artistes &rarr;</a>
                            </div>
                        </div>
                    </div>
                </li>
                
                <li><a href="ateliers">Ateliers</a></li>
                <li><a href="evenements">Événements</a></li>              
                <li><a href="journal">Le Journal</a></li>
            </ul>
        </nav>

        <div class="user-actions">
            <div id="search-app" class="search-container" v-cloak>
                <div class="search-box">
                    <input type="text" v-model="query" @input="handleInput" @focus="isFocused = true" @blur="handleBlur" placeholder="Rechercher...">
                    <button><i class="fas fa-search"></i></button>
                </div>

                <transition name="search-fade">
                    <div v-show="isFocused && query.length >= 2" class="search-results-dropdown">
                        <div v-if="isLoading" class="search-state">
                            <i class="fas fa-spinner fa-spin" style="color: var(--color-gold); margin-right: 10px;"></i> Recherche...
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

            <a href="portail-client" id="header-user-btn" title="Mon Compte">
                <i class="fas fa-user"></i>
            </a>
    
            <a href="portail-client" id="header-heart-btn" title="Mes Favoris">
                <i class="fas fa-heart"></i>
            </a>

            <button id="mobile-menu-btn" class="mobile-menu-btn" style="display: none; background: none; border: none; color: var(--color-gold); font-size: 1.5rem; cursor: pointer; margin-left: 10px;">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <script src="assets/js/header.js"></script>

    <script>
    (async () => {
        const supabaseHeader = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        const { data: { session } } = await supabaseHeader.auth.getSession();
        
        const userBtn = document.getElementById('header-user-btn');
        const heartBtn = document.getElementById('header-heart-btn');
        
        if (session) {
            if (userBtn) userBtn.href = "mon-compte";
            if (heartBtn) heartBtn.href = "mon-compte#favoris"; 
        } else {
            if (userBtn) userBtn.href = "portail-client";
            if (heartBtn) heartBtn.href = "portail-client";
        }
    })();
    </script>