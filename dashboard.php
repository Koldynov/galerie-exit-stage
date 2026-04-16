<?php include 'includes/header.php'; ?>

<main id="admin-dashboard" v-cloak>
    
    <div v-if="isAuthenticating" style="height: 100vh; display: flex; justify-content: center; align-items: center; background: #0a0a0a;">
        <div class="loader"></div>
    </div>

    <div v-else class="admin-wrapper">
        
        <aside class="admin-sidebar">
            <div style="padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <p style="color: var(--color-gold); font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase;">Espace</p>
                <h3 style="color: var(--color-white); font-size: 1.5rem;">Administrateur</h3>
            </div>

            <nav class="admin-nav">
                <button @click="currentTab = 'overview'" :class="['admin-nav-btn', { active: currentTab === 'overview' }]">
                    <i class="fas fa-home"></i> <span>Vue d'ensemble</span>
                </button>
                <button @click="currentTab = 'oeuvres'" :class="['admin-nav-btn', { active: currentTab === 'oeuvres' }]">
                    <i class="fas fa-palette"></i> <span>Inventaire Œuvres</span>
                </button>
                <button @click="currentTab = 'artistes'" :class="['admin-nav-btn', { active: currentTab === 'artistes' }]">
                    <i class="fas fa-users"></i> <span>Gestion Artistes</span>
                </button>
                
                <div style="margin-top: auto; display: flex; flex-direction: column; gap: 10px;">
                    <a href="index.php" class="admin-nav-btn" style="text-decoration: none; color: #888;">
                        <i class="fas fa-globe"></i> <span>Retour au site</span>
                    </a>
                    <button @click="logout" class="admin-nav-btn" style="color: #ff4d4d;">
                        <i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span>
                    </button>
                </div>
            </nav>
        </aside>

        <section class="admin-content">
            
            <div v-if="currentTab === 'overview'" style="animation: fadeInUp 0.4s ease forwards;">
                <h1 class="admin-header-title">Vue d'ensemble</h1>
                <p class="admin-subtitle">Statistiques globales de la plateforme</p>

                <div class="admin-grid grid-4" style="margin-bottom: 40px;">
                    <div class="admin-card">
                        <i class="fas fa-euro-sign stat-icon"></i>
                        <p class="stat-label">Valeur du Catalogue</p>
                        <p class="stat-number">{{ totalRevenue }} €</p>
                    </div>
                    <div class="admin-card">
                        <i class="fas fa-image stat-icon"></i>
                        <p class="stat-label">Œuvres Publiées</p>
                        <p class="stat-number">{{ stats.oeuvres }}</p>
                    </div>
                    <div class="admin-card">
                        <i class="fas fa-user-tie stat-icon"></i>
                        <p class="stat-label">Artistes Actifs</p>
                        <p class="stat-number">{{ stats.artistes }}</p>
                    </div>
                    <div class="admin-card">
                        <i class="fas fa-heart stat-icon"></i>
                        <p class="stat-label">Interactions</p>
                        <p class="stat-number">{{ stats.favoris }}</p>
                    </div>
                </div>

                <div class="admin-grid grid-2">
                    <div class="admin-card" style="padding: 0;">
                        <h3 style="padding: 25px; border-bottom: 1px solid rgba(255,255,255,0.05);">Dernières Entrées</h3>
                        <table class="admin-table">
                            <tbody>
                                <tr v-for="item in allOeuvres.slice(0, 5)" :key="item.id">
                                    <td style="width: 60px;">
                                        <img :src="item.image_url" class="admin-thumbnail">
                                    </td>
                                    <td>
                                        <strong style="color: var(--color-white); display: block;">{{ item.titre }}</strong>
                                        <span style="font-size: 0.8rem; color: #888;">{{ item.artistes?.nom }}</span>
                                    </td>
                                    <td>{{ item.prix }} €</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="admin-card">
                        <h3 style="margin-bottom: 20px;">Répartition</h3>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span>Peintures</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Peinture') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span>Sculptures</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Sculpture') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span>Photographies</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Photographie') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span>Autres</span> <strong style="color: var(--color-gold);">{{ getCountByCat('Autre') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="currentTab === 'oeuvres'" style="animation: fadeInUp 0.4s ease forwards;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <div>
                        <h1 class="admin-header-title">Inventaire Œuvres</h1>
                        <p class="admin-subtitle">Gestion de tout le catalogue</p>
                    </div>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Aperçu</th>
                                <th>Titre</th>
                                <th>Artiste</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th style="text-align: center;">À la une (Accueil)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="oeuvre in allOeuvres" :key="oeuvre.id">
                                <td><img :src="oeuvre.image_url" class="admin-thumbnail"></td>
                                <td><strong style="color: white;">{{ oeuvre.titre }}</strong></td>
                                <td>{{ oeuvre.artistes?.nom || 'Inconnu' }}</td>
                                <td>{{ oeuvre.categorie }}</td>
                                <td>{{ oeuvre.prix }} €</td>
                                <td style="text-align: center;">
                                    <button @click="toggleFeatured('oeuvre', oeuvre.id, oeuvre.is_featured)" class="action-btn" title="Afficher sur la page d'accueil">
                                        <i :class="oeuvre.is_featured ? 'fas fa-star' : 'far fa-star'" :style="{ color: oeuvre.is_featured ? 'var(--color-gold)' : '#666', fontSize: '1.2rem' }"></i>
                                    </button>
                                </td>
                                <td>
                                    <a :href="'oeuvre.php?id=' + oeuvre.id" target="_blank" class="action-btn" title="Voir sur le site"><i class="fas fa-eye"></i></a>
                                    <button @click="askDelete('oeuvre', oeuvre.id)" class="action-btn delete" title="Supprimer l'œuvre"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="currentTab === 'artistes'" style="animation: fadeInUp 0.4s ease forwards;">
                <h1 class="admin-header-title">Gestion Artistes</h1>
                <p class="admin-subtitle">Comptes créateurs enregistrés</p>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Nom de l'artiste</th>
                                <th>Titre Pro</th>
                                <th style="text-align: center;">À la une (Accueil)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="artiste in allArtistes" :key="artiste.id">
                                <td><img :src="artiste.image_url || 'assets/img/default-artist.jpg'" class="admin-thumbnail" style="border-radius: 50%;"></td>
                                <td><strong style="color: white;">{{ artiste.nom }}</strong></td>
                                <td>{{ artiste.titre_pro || '-' }}</td>
                                <td style="text-align: center;">
                                    <button @click="toggleFeatured('artiste', artiste.id, artiste.is_featured)" class="action-btn" title="Afficher sur la page d'accueil">
                                        <i :class="artiste.is_featured ? 'fas fa-star' : 'far fa-star'" :style="{ color: artiste.is_featured ? 'var(--color-gold)' : '#666', fontSize: '1.2rem' }"></i>
                                    </button>
                                </td>
                                <td>
                                    <a :href="'profil-artiste.php?id=' + artiste.id" target="_blank" class="action-btn"><i class="fas fa-eye"></i></a>
                                    <button @click="askDelete('artiste', artiste.id)" class="action-btn delete" title="Supprimer l'artiste"><i class="fas fa-user-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>
        
        <div v-if="deleteModal.show" class="admin-modal-overlay" @click.self="closeDeleteModal">
            <div class="admin-modal-content">
                <i class="fas fa-exclamation-triangle" style="font-size: 3.5rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
                <h2 style="color: white; margin-bottom: 15px; font-size: 1.8rem;">Êtes-vous sûr ?</h2>
                <p style="color: #bbb; margin-bottom: 30px; font-size: 1rem; line-height: 1.5;">{{ deleteModal.message }}</p>
                
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button @click="closeDeleteModal" class="admin-modal-btn cancel">Annuler</button>
                    <button @click="confirmDelete" class="admin-modal-btn confirm" :disabled="isDeleting">
                        <span v-if="isDeleting"><i class="fas fa-spinner fa-spin"></i> Suppression...</span>
                        <span v-else>Oui, supprimer</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</main>

<style>
.admin-modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(5px); z-index: 9999; display: flex; justify-content: center; align-items: center; }
.admin-modal-content { background: rgba(20, 20, 20, 0.98); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 8px; padding: 40px; width: 100%; max-width: 450px; text-align: center; box-shadow: 0 30px 60px rgba(0,0,0,0.8); animation: slideUp 0.3s ease-out; }
.admin-modal-btn { padding: 12px 30px; border-radius: 4px; border: none; cursor: pointer; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem; transition: all 0.3s ease; }
.admin-modal-btn.cancel { background: rgba(255, 255, 255, 0.1); color: white; }
.admin-modal-btn.cancel:hover { background: rgba(255, 255, 255, 0.2); }
.admin-modal-btn.confirm { background: var(--color-red-heart); color: white; }
.admin-modal-btn.confirm:hover { background: #ff1a1a; box-shadow: 0 5px 15px rgba(230, 57, 70, 0.4); }
[v-cloak] { display: none; }
</style>

<script>
const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const isAuthenticating = ref(true);
        const currentTab = ref('overview'); 
        
        const stats = ref({ oeuvres: 0, artistes: 0, favoris: 0 });
        const allOeuvres = ref([]);
        const allArtistes = ref([]);
        const totalRevenue = ref(0);

        const deleteModal = ref({ show: false, type: '', id: null, message: '' });
        const isDeleting = ref(false);

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const checkAdminAccess = async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (!session || session.user.email !== 'admin@gmail.com') {
                window.location.href = 'index.php'; 
            } else {
                isAuthenticating.value = false;
                fetchAllData();
            }
        };

        const fetchAllData = async () => {
            const { data: oeuvres } = await supabase.from('oeuvres').select('*, artistes(nom)').order('created_at', { ascending: false });
            if (oeuvres) {
                allOeuvres.value = oeuvres;
                stats.value.oeuvres = oeuvres.length;
                totalRevenue.value = oeuvres.reduce((acc, curr) => acc + (curr.prix || 0), 0);
            }

            const { data: artistes } = await supabase.from('artistes').select('*').order('nom', { ascending: true });
            if (artistes) {
                allArtistes.value = artistes;
                stats.value.artistes = artistes.length;
            }

            const { count: favCount } = await supabase.from('favoris').select('*', { count: 'exact', head: true });
            stats.value.favoris = favCount || 0;
        };

        const getCountByCat = (cat) => {
            return allOeuvres.value.filter(o => o.categorie === cat).length;
        };

        // ФУНКЦІЯ ПЕРЕМИКАННЯ СТАТУСУ "НА ГОЛОВНІЙ"
        const toggleFeatured = async (type, id, currentStatus) => {
            const tableName = type === 'oeuvre' ? 'oeuvres' : 'artistes';
            const newStatus = !currentStatus;
            
            const { error } = await supabase
                .from(tableName)
                .update({ is_featured: newStatus })
                .eq('id', id);
                
            if (!error) {
                // Оновлюємо список локально, щоб зірочка відразу зафарбувалась
                if (type === 'oeuvre') {
                    const item = allOeuvres.value.find(o => o.id === id);
                    if (item) item.is_featured = newStatus;
                } else {
                    const item = allArtistes.value.find(a => a.id === id);
                    if (item) item.is_featured = newStatus;
                }
            } else {
                alert("Erreur lors de la mise à jour du statut.");
            }
        };

        const askDelete = (type, id) => {
            deleteModal.value.type = type;
            deleteModal.value.id = id;
            deleteModal.value.show = true;
            if (type === 'oeuvre') deleteModal.value.message = "Cette action est irréversible. L'œuvre sera définitivement supprimée de la galerie.";
            else if (type === 'artiste') deleteModal.value.message = "ATTENTION : Supprimer cet artiste supprimera également TOUTES ses œuvres de la galerie.";
        };

        const closeDeleteModal = () => {
            if (!isDeleting.value) { deleteModal.value.show = false; deleteModal.value.id = null; }
        };

        const confirmDelete = async () => {
            isDeleting.value = true;
            try {
                if (deleteModal.value.type === 'oeuvre') {
                    await supabase.from('oeuvres').delete().eq('id', deleteModal.value.id);
                } else if (deleteModal.value.type === 'artiste') {
                    await supabase.from('oeuvres').delete().eq('artiste_id', deleteModal.value.id);
                    await supabase.from('artistes').delete().eq('id', deleteModal.value.id);
                }
                await fetchAllData(); 
            } catch (error) {
                console.error(error);
            } finally {
                isDeleting.value = false;
                closeDeleteModal();
            }
        };

        const logout = async () => {
            await supabase.auth.signOut();
            window.location.href = 'index.php';
        };

        onMounted(() => { checkAdminAccess(); });

        return { 
            isAuthenticating, currentTab, stats, allOeuvres, allArtistes, totalRevenue, 
            getCountByCat, toggleFeatured, askDelete, confirmDelete, closeDeleteModal, deleteModal, isDeleting, logout
        };
    }
}).mount('#admin-dashboard');
</script>

<?php include 'includes/footer.php'; ?>