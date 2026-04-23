<?php include 'includes/header.php'; ?>

<main id="peintures-app" v-cloak>
    <section class="page-header container" style="padding-top: 60px; margin-bottom: 40px;">
        <h1 class="reveal" style="font-size: 4rem; margin-bottom: 10px;">Peintures<span class="gold">.</span></h1>
        <p class="reveal delay-1" style="color: #bbb; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 2px;">
            Découvrez notre collection de toiles contemporaines
        </p>
    </section>

    <section class="container" style="margin-bottom: 120px;">
        
        <div v-if="!isLoading && !errorMessage" style="position: relative; z-index: 1000; display: flex; justify-content: flex-end; margin-bottom: 20px; animation: fadeInUp 0.6s ease-out forwards;">
            <div v-if="isFilterOpen" @click="isFilterOpen = false" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 999; cursor: default;"></div>
            <div style="display: flex; align-items: center; gap: 15px; position: relative; z-index: 1000;">
                <span style="color: #888; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Afficher :</span>
                <div class="custom-dropdown">
                    <div class="dropdown-trigger" @click="isFilterOpen = !isFilterOpen">
                        <span>{{ filterLabels[filterStatut] }}</span>
                        <i class="fas fa-chevron-down" :style="{ transform: isFilterOpen ? 'rotate(180deg)' : 'rotate(0)' }" style="transition: transform 0.4s ease; color: var(--color-gold);"></i>
                    </div>
                    <div class="dropdown-menu" :class="{ 'show': isFilterOpen }">
                        <div class="dropdown-item" :class="{ 'active': filterStatut === 'disponible' }" @click="selectFilter('disponible')">En vente (Disponibles)</div>
                        <div class="dropdown-item" :class="{ 'active': filterStatut === 'tous' }" @click="selectFilter('tous')">Tout le catalogue</div>
                        <div class="dropdown-item" :class="{ 'active': filterStatut === 'vendu' }" @click="selectFilter('vendu')">Archives (Vendus)</div>
                    </div>
                </div>
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

        <div v-else-if="peinturesList.length === 0" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
            <i class="fas fa-palette" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Aucune peinture disponible</h2>
            <p>Notre collection est en cours de renouvellement.</p>
        </div>

        <div v-else-if="filteredPeintures.length === 0" class="empty-state" style="animation: fadeInUp 0.4s ease-out forwards;">
            <i class="fas fa-search" style="font-size: 3rem; color: #555; margin-bottom: 20px;"></i>
            <h2>Aucun résultat</h2>
        </div>

        <div v-else class="gallery-grid" style="animation: fadeInUp 0.8s ease-out forwards;">
            <div v-for="oeuvre in filteredPeintures" :key="oeuvre.id" class="gallery-card">
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

    </section>
</main>

<style>[v-cloak] { display: none; }</style>

<script src="assets/js/animations.js"></script>
<script src="assets/js/pages/peintures.js"></script>

<?php include 'includes/footer.php'; ?>