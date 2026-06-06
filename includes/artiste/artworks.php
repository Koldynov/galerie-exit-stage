<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <h2 style="font-size: 2rem;">Mes Œuvres</h2>
    <button @click="showAddModal = true" class="btn-primary"><i class="fas fa-plus"></i> Ajouter une œuvre</button>
</div>

<div v-if="myArtworks.length === 0" class="empty-state">
    <i class="fas fa-palette" style="font-size: 4rem; color: #555; margin-bottom: 20px;"></i>
    <h2>Votre galerie est vide</h2>
    <p>Commencez à partager votre art avec le monde.</p>
</div>

<div v-else class="favoris-grid">
    <div v-for="oeuvre in myArtworks" :key="oeuvre.id" class="carousel-card interactive-card" style="width: 100%; margin-bottom: 40px;">
        <div class="image-wrapper" style="position: relative;">
            <img :src="oeuvre.image_url || 'https://via.placeholder.com/600x600?text=Indisponible'" :alt="oeuvre.titre" class="carousel-img" :style="{ opacity: oeuvre.statut === 'vendu' ? '0.6' : '1', filter: oeuvre.statut === 'vendu' ? 'grayscale(50%)' : 'none' }">
            
            <div :class="['status-chip', oeuvre.statut || 'disponible']" style="position: absolute; top: 15px; right: 15px; padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; z-index: 10;">
                <span v-if="oeuvre.statut === 'vendu'">Vendu</span>
                <span v-else>Disponible</span>
            </div>
        </div>
        <div class="card-info">
            <p style="color: #888; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">{{ oeuvre.categorie }}</p>
            
            <h3>{{ oeuvre.titre }}</h3>
            <p :style="{ color: oeuvre.statut === 'vendu' ? '#888' : 'var(--color-gold)', textDecoration: oeuvre.statut === 'vendu' ? 'line-through' : 'none', fontSize: '1.2rem', marginBottom: '10px', fontWeight: 'bold' }">
                {{ oeuvre.prix > 0 ? oeuvre.prix + ' €' : 'Sur demande' }}
            </p>
            <p class="description" style="word-wrap: break-word;">{{ oeuvre.description }}</p>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                <span style="color: var(--color-gold); font-size: 0.85rem; text-transform: uppercase;">Publié</span>
                
                <div style="display: flex; gap: 15px;">
                    <button @click="openEditModal(oeuvre)" class="btn-icon" style="color: var(--color-gold); margin: 0;" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button @click="deleteArtwork(oeuvre.id)" class="btn-icon" style="color: var(--color-red-heart); margin: 0;" title="Supprimer">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>