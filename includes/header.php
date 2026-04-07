<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Galerie EXIT | Accueil</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
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
                <li><a href="index.php" class="active">Accueil</a></li>
                <li><a href="#oeuvres">Œuvres</a></li>
                <li><a href="#artistes">Artistes</a></li>
                <li><a href="#heart-concept">Heart Concept</a></li>
            </ul>
        </nav>

        <div class="user-actions">
            <div class="search-container">
                <input type="text" placeholder="Rechercher...">
                <button><i class="fas fa-search"></i></button>
            </div>
            <a href="#login"><i class="fas fa-user"></i></a>
            <a href="#favoris"><i class="fas fa-heart"></i></a>
            <a href="#panier"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </header>