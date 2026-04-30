<?php include 'includes/header.php'; ?>

<main id="sculptures-app" v-cloak>
    <section class="page-header container" style="padding-top: 60px; margin-bottom: 40px;">
        <h1 class="reveal" style="font-size: 4rem; margin-bottom: 10px;">Sculptures<span class="gold">.</span></h1>
        <p class="reveal delay-1" style="color: #bbb; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px;">
            Découvrez notre collection de sculptures contemporaines
        </p>
    </section>

    <section id="gallery-section" class="container" style="margin-bottom: 120px;">
        
        <div v-if="isArtisteOpen || isStatutOpen" @click="closeDropdowns" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999; cursor: default;"></div>

        <div v-if="!errorMessage" class="filters-panel" style="position: relative; z-index: 1000;">
            
            <div class="filter-group" style="grid-column: span 2;">
                <label class="filter-label">Rechercher</label>
                <div style="position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                    <input type="text" v-model="filters.recherche" placeholder="Nom de l'œuvre..." class="filter-input" style="padding-left: 40px;">
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">Artiste</label>
                <div class="custom-dropdown">
                    <div class="filter-input" @click="isArtisteOpen = !isArtisteOpen" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ getArtisteLabel(filters.artisteId) }}</span>
                        <i class="fas fa-chevron-down" :style="{ transform: isArtisteOpen ? 'rotate(180deg)' : 'rotate(0)' }" style="transition: transform 0.4s ease; color: #888;"></i>
                    </div>
                    <div class="dropdown-menu" :class="{ 'show': isArtisteOpen }">
                        <div class="dropdown-item" :class="{ 'active': filters.artisteId === '' }" @click="selectArtiste('')">Tous les artistes</div>
                        <div class="dropdown-item" v-for="artiste in artistesList" :key="artiste.id" :class="{ 'active': filters.artisteId === artiste.id }" @click="selectArtiste(artiste.id)">
                            {{ artiste.nom }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">Statut</label>
                <div class="custom-dropdown">
                    <div class="filter-input" @click="isStatutOpen = !isStatutOpen" style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                        <span>{{ filterLabels[filters.statut] }}</span>
                        <i class="fas fa-chevron-down" :style="{ transform: isStatutOpen ? 'rotate(180deg)' : 'rotate(0)' }" style="transition: transform 0.4s ease; color: #888;"></i>
                    </div>
                    <div class="dropdown-menu" :class="{ 'show': isStatutOpen }">
                        <div class="dropdown-item" :class="{ 'active': filters.statut === 'disponible' }" @click="selectStatut('disponible')">En vente (Disponibles)</div>
                        <div class="dropdown-item" :class="{ 'active': filters.statut === 'tous' }" @click="selectStatut('tous')">Tout le catalogue</div>
                        <div class="dropdown-item" :class="{ 'active': filters.statut === 'vendu' }" @click="selectStatut('vendu')">Archives (Vendus)</div>
                    </div>
                </div>
            </div>

            <div class="filter-group">
                <label class="filter-label">Prix Min (€)</label>
                <input type="number" v-model="filters.minPrix" placeholder="0" class="filter-input">
            </div>

            <div class="filter-group">
                <label class="filter-label">Prix Max (€)</label>
                <input type="number" v-model="filters.maxPrix" placeholder="Max" class="filter-input">
            </div>

        </div>

        <div v-if="isLoading" class="empty-state">
            <div class="loader"></div>
            <p>Chargement de la collection...</p>
        </div>

        <div v-else-if="errorMessage" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
            <h2>Erreur de Connexion</h2>
            <p>{{ errorMessage }}</p>
        </div>

        <div v-else-if="sculpturesList.length === 0" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-cube" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Aucune sculpture disponible</h2>
            <p>Notre collection est en cours de renouvellement.</p>
        </div>

        <div v-else-if="filteredSculptures.length === 0" class="empty-state" style="animation: fadeInUp 0.4s ease-out forwards;">
            <i class="fas fa-search" style="font-size: 3rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Aucun résultat</h2>
        </div>

        <div v-else class="masonry-grid" style="animation: fadeInUp 0.8s ease-out forwards;">
            <div v-for="oeuvre in filteredSculptures" :key="oeuvre.id" class="masonry-item">
                <div class="gallery-image-wrapper">
                    <a :href="'/galerie/oeuvre/' + oeuvre.id">
                        <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x800?text=Indisponible'" :alt="oeuvre.titre" class="gallery-img">
                    </a>
                </div>
                <div class="gallery-card-info">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3><a :href="'/galerie/oeuvre/' + oeuvre.id" style="color: inherit; text-decoration: none;">{{ oeuvre.titre }}</a></h3>
                            <p v-if="oeuvre.artistes" class="artist-name">Par {{ oeuvre.artistes.nom }}</p>
                        </div>
                        <button class="btn-icon" @click="toggleFavori(oeuvre.id)" :style="{ color: isFavori(oeuvre.id) ? 'var(--color-red-heart)' : '#666', marginTop: '-5px' }">
                            <i :class="isFavori(oeuvre.id) ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                    </div>
                    <p class="oeuvre-price">
                        <span v-if="oeuvre.statut === 'vendu'" style="color: var(--color-red-heart); font-weight: bold; margin-right: 10px;">VENDU</span>
                        <span :style="{ textDecoration: oeuvre.statut === 'vendu' ? 'line-through' : 'none', opacity: oeuvre.statut === 'vendu' ? '0.5' : '1' }">
                            {{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Sur demande' }}
                        </span>
                    </p>
                    <p class="gallery-description">{{ oeuvre.description }}</p>
                </div>
            </div>
        </div>

        <div ref="loadMoreTrigger" style="height: 50px; display: flex; justify-content: center; align-items: center; margin-top: 40px; margin-bottom: 40px; clear: both; width: 100%;">
            <div v-if="isLoadingMore" class="loader" style="width: 30px; height: 30px;"></div>
            <p v-if="!hasMoreData && filteredSculptures.length > 0" style="color: #666; font-size: 0.9rem;">Vous avez vu toutes nos œuvres.</p>
        </div>

    </section>
</main>

<style>
[v-cloak] { display: none; }

/* --- ІДЕАЛЬНА СІТКА ФІЛЬТРІВ (CSS GRID) --- */
.filters-panel {
    background: rgba(255, 255, 255, 0.02);
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    animation: fadeInUp 0.6s ease-out forwards;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    color: #888;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
    font-weight: 600;
}

.filter-input {
    width: 100%;
    height: 45px;
    background: #0a0a0a;
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0 15px;
    border-radius: 6px;
    font-family: inherit;
    font-size: 0.95rem;
    outline: none;
    transition: all 0.3s ease;
}

.filter-input:focus {
    border-color: var(--color-gold, #d4af37);
    box-shadow: 0 0 10px rgba(212, 175, 55, 0.1);
}

.filter-input[type="number"]::-webkit-inner-spin-button, 
.filter-input[type="number"]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
}

/* --- КАСОМНІ ВИПАДАЮЧІ СПИСКИ (Dropdowns) --- */
.custom-dropdown {
    position: relative;
    width: 100%;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: #111;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    margin-top: 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1010;
    max-height: 250px;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-item {
    padding: 12px 15px;
    cursor: pointer;
    color: #ddd;
    transition: background 0.2s, color 0.2s;
    font-size: 0.9rem;
}

.dropdown-item:hover, .dropdown-item.active {
    background: rgba(212, 175, 55, 0.1);
    color: var(--color-gold, #d4af37);
}

/* На мобільних екранах пошук займає 1 колонку замість 2 */
@media (max-width: 768px) {
    .filters-panel .filter-group[style*="grid-column: span 2"] {
        grid-column: span 1 !important;
    }
}

/* --- СТИЛІ MASONRY --- */
.masonry-grid {
    column-count: 3;
    column-gap: 25px;
    width: 100%;
    margin-top: 20px;
}

.masonry-item {
    -webkit-column-break-inside: avoid;
    page-break-inside: avoid;
    break-inside: avoid;
    margin-bottom: 25px;
    background: #0a0a0a;
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: inline-block;
    width: 100%;
    height: auto !important;
}

.masonry-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    border-color: rgba(212, 175, 55, 0.3);
}

.masonry-item .gallery-image-wrapper,
.masonry-item .gallery-image-wrapper a {
    display: block !important;
    width: 100% !important;
    aspect-ratio: 4 / 5 !important;
    overflow: hidden !important;
    height: auto !important;
    min-height: unset !important;
    max-height: none !important;
}

.masonry-item .gallery-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    display: block !important;
    border-radius: 8px 8px 0 0;
}

.masonry-item .gallery-card-info {
    padding: 20px;
    height: auto !important;
    min-height: unset !important;
}

@media (max-width: 1024px) {
    .masonry-grid { column-count: 2; }
}

@media (max-width: 600px) {
    .masonry-grid { column-count: 1; }
}
</style>

<script src="assets/js/animations.js"></script>
<script src="assets/js/pages/sculptures.js"></script>

<?php include 'includes/footer.php'; ?>