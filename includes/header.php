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
                <g class="power-e" stroke="currentColor" stroke-width="5" stroke-linecap="round" fill="none">
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
                <a href="#peintures">Peintures</a>
                <div class="dropdown-content">
                    <div class="dropdown-grid">
                        <div class="column">
                            <a href="#">Art Contemporain</a>
                            <a href="#">Abstrait</a>
                        </div>
                        <div class="column">
                            <a href="#">Street Art</a>
                            <a href="#">Tout voir</a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="dropdown">
                <a href="#sculptures">Sculptures</a>
                <div class="dropdown-content">
                    <div class="dropdown-grid">
                        <div class="column">
                            <a href="#">Bronze</a>
                            <a href="#">Moderne</a>
                        </div>
                        <div class="column">
                            <a href="#">Statues</a>
                            <a href="#">Tout voir</a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="dropdown">
                <a href="#photographies">Photographies</a>
                <div class="dropdown-content">
                    <div class="dropdown-grid">
                        <div class="column">
                            <a href="#">Noir et Blanc</a>
                        </div>
                        <div class="column">
                            <a href="#">Paysages</a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="dropdown">
                <a href="#artistes">Artistes</a>
                <div class="dropdown-content">
                    <div class="dropdown-grid">
                        <div class="column">
                            <a href="#">Portraits</a> [cite: 40]
                            <a href="#">Biographies</a> [cite: 41]
                        </div>
                        <div class="column">
                            <a href="#">Expositions</a>
                        </div>
                    </div>
                </div>
            </li>

            <li><a href="#heart-concept">Heart Concept</a></li> 
        </ul>
    </nav>

    <div class="user-actions">
        <div class="search-container">
            <input type="text" placeholder="Rechercher...">
            <button><i class="fas fa-search"></i></button>
        </div>
        <a href="login.php"><i class="fas fa-user"></i></a>
        <a href="#favoris"><i class="fas fa-heart"></i></a>
        <a href="#panier"><i class="fas fa-shopping-cart"></i></a> 
    </div>

    <script>
document.addEventListener('DOMContentLoaded', async () => {
    const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
    const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';

    const supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

    const { data: { session } } = await supabaseClient.auth.getSession();

    if (session) {
        const loginLinks = document.querySelectorAll('a[href="login.php"]');
        loginLinks.forEach(link => {
            link.href = 'mon-compte.php';
            link.innerHTML = '<i class="fas fa-user-circle"></i> Mon Compte';
        });
    }
});
</script>
    
</header>