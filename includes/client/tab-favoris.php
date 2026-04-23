<h2 class="client-title" style="margin-bottom: 30px;">Mes Coups de Cœur</h2>

<div v-if="favorisList.length === 0" class="client-empty-box">
    <i class="far fa-heart"></i>
    <p>Vous n'avez pas encore d'œuvres favorites.</p>
</div>

<div v-else class="client-grid">
    <div v-for="fav in favorisList" :key="fav.oeuvres.id" class="fav-card">
        <a :href="'oeuvre/' + fav.oeuvres.id" class="card-img-link">
            <img :src="fav.oeuvres.image_url" :alt="fav.oeuvres.titre">
            <div v-if="fav.oeuvres.statut === 'vendu'" class="vendu-badge">VENDU</div>
        </a>
        <div class="card-details">
            <div>
                <h4>{{ fav.oeuvres.titre }}</h4>
                <p class="price">{{ fav.oeuvres.prix }} €</p>
            </div>
            <button @click="removeFavori(fav.oeuvres.id)" class="remove-fav-btn" title="Retirer des favoris">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>
</div>


