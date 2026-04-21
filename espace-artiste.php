<?php include 'includes/header.php'; ?>

<div id="studio-app" v-cloak>

    <main>
        <section class="page-hero" style="height: 60vh; justify-content: center; animation: smoothFadeIn 1s ease-out forwards;">
            <div class="page-hero-content" style="transform: translateY(0); text-align: center;">
                <h1 style="font-size: 5rem; letter-spacing: 0.05em; margin-bottom: 15px; animation: smoothFadeIn 0.8s ease-out forwards;">Mon Studio<span class="gold">.</span></h1>
                <p style="color: #888; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 3px; animation: fadeInUp 0.8s ease-out 0.2s forwards; opacity: 0;">
                    Espace créatif de {{ artistProfile ? artistProfile.nom : 'l\'artiste' }}
                </p>
            </div>
        </section>

        <section class="container" style="margin-bottom: 120px; padding-top: 50px;">
            
            <div v-if="isLoading" class="empty-state">
                <div class="loader"></div>
                <p>Ouverture du studio...</p>
            </div>

            <div v-else-if="errorMessage" class="empty-state" style="animation: fadeInUp 0.6s ease-out forwards;">
                <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: var(--color-red-heart); margin-bottom: 20px;"></i>
                <h2>Accès Refusé</h2>
                <p>{{ errorMessage }}</p>
                <button @click="logout" class="btn-primary" style="margin-top: 20px;">Se déconnecter</button>
            </div>

            <div v-else-if="artistProfile" style="animation: fadeInUp 0.8s ease-out forwards;">
                
                <div v-if="!artistProfile.is_approved" style="background: rgba(212,175,55,0.1); border: 1px solid var(--color-gold); padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-hourglass-half" style="color: var(--color-gold); font-size: 1.5rem; margin-bottom: 10px;"></i>
                    <h3 style="color: var(--color-gold); margin-bottom: 5px;">Profil en attente de validation</h3>
                    <p style="color: #bbb; font-size: 0.9rem;">Votre profil est en cours d'examen par la galerie. Vos œuvres ne sont pas encore visibles publiquement sur le site.</p>
                </div>

                <div class="profile-info" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 40px; margin-bottom: 60px; background: rgba(255,255,255,0.02); padding: 40px; border: 1px solid rgba(212,175,55,0.2); border-radius: 8px;">
                    
                    <div style="flex: 0 0 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 2px solid var(--color-gold);">
                        <img :src="artistProfile.image_url || 'assets/img/default-artist.jpg' || 'https://via.placeholder.com/400x400?text=Photo'" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>

                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 5px;">
                            <h2 style="color: var(--color-gold); font-size: 2.2rem; margin: 0;">{{ artistProfile.nom }}</h2>
                            <button @click="openEditProfile" class="btn-icon" style="color: var(--color-white); font-size: 1rem; margin: 0;" title="Modifier le profil">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                        </div>
                        
                        <p v-if="artistProfile.titre_pro" style="color: #fff; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-bottom: 20px;">{{ artistProfile.titre_pro }}</p>
                        
                        <blockquote v-if="artistProfile.citation" style="font-style: italic; color: #aaa; border-left: 2px solid var(--color-gold); padding-left: 15px; margin-bottom: 20px; font-size: 1.1rem;">
                            "{{ artistProfile.citation }}"
                        </blockquote>

                        <p style="color: #bbb; line-height: 1.8; white-space: pre-wrap; word-wrap: break-word;">{{ artistProfile.biographie || 'Aucune biographie renseignée. Cliquez sur "Modifier" pour en ajouter une.' }}</p>
                    </div>
                    <div>
                        <button @click="logout" class="btn-primary cancel-btn">Quitter</button>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 50px;">
                    <div style="background: #111; padding: 30px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center; transition: all 0.3s;" class="stat-card">
                        <p style="color: #888; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 10px;">Ventes Totales</p>
                        <p style="font-size: 2.5rem; color: #2ecc71; font-weight: bold;">{{ mySalesRevenue }} €</p>
                    </div>
                    <div style="background: #111; padding: 30px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center; transition: all 0.3s;" class="stat-card">
                        <p style="color: #888; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 10px;">Œuvres Vendues</p>
                        <p style="font-size: 2.5rem; color: white; font-weight: bold;">{{ soldCount }}</p>
                    </div>
                    <div style="background: #111; padding: 30px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); text-align: center; transition: all 0.3s;" class="stat-card">
                        <p style="color: #888; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 10px;">Œuvres en Vente</p>
                        <p style="font-size: 2.5rem; color: var(--color-gold); font-weight: bold;">{{ availableCount }}</p>
                    </div>
                </div>

                <div v-if="adminMessages.length > 0" style="margin-bottom: 60px;">
                    <h3 style="margin-bottom: 20px; color: var(--color-gold); font-size: 1.5rem;"><i class="fas fa-bell"></i> Messages de la Galerie</h3>
                    <div v-for="msg in adminMessages" :key="msg.id" style="background: rgba(255,255,255,0.02); padding: 20px; border-left: 3px solid var(--color-gold); border-radius: 0 8px 8px 0; margin-bottom: 15px;">
                        <p style="color: #ddd; font-size: 1rem; line-height: 1.5; margin-bottom: 10px; white-space: pre-wrap;">{{ msg.message }}</p>
                        <span style="font-size: 0.75rem; color: #666;"><i class="far fa-clock"></i> {{ formatDate(msg.created_at) }}</span>
                    </div>
                </div>

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
                    <div v-for="oeuvre in myArtworks" :key="oeuvre.id" class="carousel-card interactive-card" style="width: 100%;">
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
                                <button @click="deleteArtwork(oeuvre.id)" class="btn-icon" style="color: var(--color-red-heart); margin: 0;" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <div v-if="showAddModal" class="modal-overlay" @click.self="closeModal" style="z-index: 500;">
        <div class="modal-content" style="animation: slideUp 0.4s ease-out; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="color: var(--color-gold);">Nouvelle Œuvre</h2>
                <button @click="closeModal" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
            </div>

            <div class="input-group" style="position: relative;">
                <label>Catégorie de l'œuvre</label>
                <select v-model="newArtwork.categorie" class="custom-select">
                    <option value="" disabled>Sélectionnez une catégorie...</option>
                    <option value="Peinture">Peinture (Toile, Aquarelle...)</option>
                    <option value="Sculpture">Sculpture</option>
                    <option value="Photographie">Photographie</option>
                    <option value="Autre">Autre format</option>
                </select>
                <i class="fas fa-chevron-down" style="position: absolute; right: 15px; bottom: 15px; pointer-events: none; color: #888;"></i>
            </div>

            <div class="input-group">
                <label>Titre de l'œuvre</label>
                <input type="text" v-model="newArtwork.titre" placeholder="Ex: Nuit Cendrée">
            </div>

            <div class="input-group">
                <label>Prix (€) <span style="color: #666; font-size: 0.8rem;">(Laissez 0 pour "Sur demande")</span></label>
                <input type="number" v-model="newArtwork.prix" placeholder="Ex: 1500" min="0">
            </div>
            
            <div class="input-group">
                <label>Description / Histoire</label>
                <textarea v-model="newArtwork.description" rows="3" placeholder="Racontez l'histoire..." style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div class="input-group">
                <label>Photo de l'œuvre</label>
                <input type="file" accept="image/*" @change="handleFileUpload" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; cursor: pointer;">
                <p v-if="selectedFileName" style="font-size: 0.85rem; color: var(--color-gold); margin-top: 8px;">
                    <i class="fas fa-file-image"></i> {{ selectedFileName }}
                </p>
            </div>

            <div v-if="modalError" class="error-message" style="margin-bottom: 15px;"><i class="fas fa-exclamation-circle"></i> {{ modalError }}</div>

            <button @click="submitArtwork" class="btn-primary" style="width: 100%; margin-top: 10px;" :disabled="isSaving">
                <span v-if="isSaving"><i class="fas fa-spinner fa-spin"></i> Publication et upload...</span>
                <span v-else>Publier l'œuvre</span>
            </button>
        </div>
    </div>

    <div v-if="showEditProfile" class="modal-overlay" @click.self="closeEditProfile" style="z-index: 500;">
        <div class="modal-content" style="animation: slideUp 0.4s ease-out; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="color: var(--color-gold);">Modifier le profil</h2>
                <button @click="closeEditProfile" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;"><i class="fas fa-times"></i></button>
            </div>

            <div class="input-group">
                <label>Nom d'artiste</label>
                <input type="text" v-model="editProfileForm.nom">
            </div>

            <div class="input-group">
                <label>Titre professionnel <span style="color: #666; font-size: 0.8rem;">(Optionnel)</span></label>
                <input type="text" v-model="editProfileForm.titre_pro" placeholder="Ex: Sculpteur Contemporain">
            </div>

            <div class="input-group">
                <label>Votre citation ou devise <span style="color: #666; font-size: 0.8rem;">(Optionnel)</span></label>
                <input type="text" v-model="editProfileForm.citation" placeholder="Ex: L'art est le reflet de l'âme...">
            </div>
            
            <div class="input-group">
                <label>Biographie complète</label>
                <textarea v-model="editProfileForm.biographie" rows="5" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <div class="input-group">
                <label>Photo de profil</label>
                <input type="file" accept="image/*" @change="handleProfileFileUpload" style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px dashed rgba(212, 175, 55, 0.5); padding: 12px 15px; border-radius: 4px; color: var(--color-white); outline: none; cursor: pointer;">
                <p v-if="profileFileName" style="font-size: 0.85rem; color: var(--color-gold); margin-top: 8px;">
                    <i class="fas fa-file-image"></i> {{ profileFileName }}
                </p>
            </div>

            <div v-if="profileError" class="error-message" style="margin-bottom: 15px;"><i class="fas fa-exclamation-circle"></i> {{ profileError }}</div>

            <button @click="submitProfileUpdate" class="btn-primary" style="width: 100%; margin-top: 10px;" :disabled="isSavingProfile">
                <span v-if="isSavingProfile"><i class="fas fa-spinner fa-spin"></i> Mise à jour...</span>
                <span v-else>Enregistrer les modifications</span>
            </button>
        </div>
    </div>

</div>

<style>
.stat-card:hover { border-color: var(--color-gold) !important; transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
.status-chip.disponible { background: #2ecc71; color: white; }
.status-chip.vendu { background: #e74c3c; color: white; }

.modal-overlay {
    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(5px); z-index: 500; 
    display: flex; justify-content: center; align-items: center;
}
.modal-content {
    background: #1a1a1a; border: 1px solid rgba(212, 175, 55, 0.5); border-radius: 8px;
    padding: 40px; width: 100%; max-width: 600px; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
}
.modal-content textarea:focus, .modal-content input:focus, .custom-select:focus {
    border-color: var(--color-gold) !important; background: rgba(212, 175, 55, 0.05) !important;
}

.custom-select {
    width: 100%; 
    background: rgba(255, 255, 255, 0.05); 
    border: 1px solid rgba(255, 255, 255, 0.2); 
    padding: 12px 15px; 
    border-radius: 4px; 
    color: var(--color-white); 
    outline: none; 
    font-family: inherit; 
    appearance: none; 
    cursor: pointer;
}
.custom-select option {
    background: #1a1a1a;
    color: var(--color-white);
    padding: 10px;
}

input[type="file"]::file-selector-button {
    background: var(--color-gold); color: #1a1a1a; border: none; padding: 8px 15px;
    border-radius: 4px; font-weight: bold; cursor: pointer; margin-right: 15px; transition: background 0.3s;
}
input[type="file"]::file-selector-button:hover { background: #fff; }
[v-cloak] { display: none; }
</style>

<script src="assets/js/animations.js"></script>

<script>
const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const currentUser = ref(null);
        const artistProfile = ref(null);
        const myArtworks = ref([]);
        const isLoading = ref(true);
        const errorMessage = ref('');
        
        // НОВІ ЗМІННІ ДЛЯ СТАТИСТИКИ І ПОВІДОМЛЕНЬ
        const mySales = ref([]);
        const adminMessages = ref([]);
        
        const showAddModal = ref(false);
        const isSaving = ref(false);
        const modalError = ref('');
        const newArtwork = ref({ titre: '', description: '', prix: '', categorie: '' });
        const selectedFile = ref(null);
        const selectedFileName = ref('');

        const showEditProfile = ref(false);
        const isSavingProfile = ref(false);
        const profileError = ref('');
        const editProfileForm = ref({ nom: '', biographie: '', titre_pro: '', citation: '' });
        const profileFile = ref(null);
        const profileFileName = ref('');

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        // ОБЧИСЛЕННЯ СТАТИСТИКИ
        const mySalesRevenue = computed(() => mySales.value.reduce((sum, s) => sum + (Number(s.montant_total) || 0), 0));
        const soldCount = computed(() => myArtworks.value.filter(o => o.statut === 'vendu').length);
        const availableCount = computed(() => myArtworks.value.filter(o => !o.statut || o.statut === 'disponible').length);

        const formatDate = (dateString) => new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });

        const fetchStudioData = async () => {
            const { data: artistData, error: artistError } = await supabase.from('artistes').select('*').eq('auth_user_id', currentUser.value.id).single();
            if (artistError || !artistData) {
                errorMessage.value = "Ce compte n'est pas enregistré comme artiste."; isLoading.value = false; return;
            }
            artistProfile.value = artistData;

            const { data: oeuvresData } = await supabase.from('oeuvres').select('*').eq('artiste_id', artistData.id).order('created_at', { ascending: false });
            if (oeuvresData) {
                myArtworks.value = oeuvresData;
                
                // Отримуємо продажі
                const ids = oeuvresData.map(o => o.id);
                if (ids.length > 0) {
                    const { data: sales } = await supabase.from('commandes').select('*').in('oeuvre_id', ids);
                    if (sales) mySales.value = sales;
                }
            }

            // Отримуємо повідомлення
            const { data: msgs } = await supabase.from('admin_messages').select('*').eq('artiste_id', artistData.id).order('created_at', {ascending: false});
            if (msgs) adminMessages.value = msgs;

            isLoading.value = false;
        };

        const handleFileUpload = (event) => {
            const file = event.target.files[0];
            if (file) { selectedFile.value = file; selectedFileName.value = file.name; }
        };

        const closeModal = () => {
            showAddModal.value = false; modalError.value = ''; 
            newArtwork.value = { titre: '', description: '', prix: '', categorie: '' }; 
            selectedFile.value = null; selectedFileName.value = '';
        };

        const submitArtwork = async () => {
            modalError.value = '';
            
            if (!newArtwork.value.titre || !selectedFile.value || !newArtwork.value.categorie) { 
                modalError.value = "Titre, catégorie et image sont obligatoires."; return; 
            }
            
            isSaving.value = true;
            try {
                const fileExt = selectedFile.value.name.split('.').pop();
                const fileName = `${Date.now()}-${Math.random().toString(36).substring(2)}.${fileExt}`;
                const { error: uploadError } = await supabase.storage.from('artworks').upload(`uploads/${fileName}`, selectedFile.value);
                if (uploadError) throw uploadError;

                const { data: publicUrlData } = supabase.storage.from('artworks').getPublicUrl(`uploads/${fileName}`);
                
                const { data: insertData, error: insertError } = await supabase.from('oeuvres')
                    .insert([{ 
                        artiste_id: artistProfile.value.id, 
                        titre: newArtwork.value.titre, 
                        description: newArtwork.value.description, 
                        image_url: publicUrlData.publicUrl, 
                        statut: 'disponible', 
                        categorie: newArtwork.value.categorie, 
                        prix: parseFloat(newArtwork.value.prix) || 0 
                    }]).select();

                if (insertError) throw insertError;
                myArtworks.value.unshift(insertData[0]);
                closeModal();
            } catch (error) { modalError.value = error.message; } finally { isSaving.value = false; }
        };

        const deleteArtwork = async (id) => {
            if(confirm("Supprimer cette œuvre ?")) {
                myArtworks.value = myArtworks.value.filter(o => o.id !== id);
                await supabase.from('oeuvres').delete().eq('id', id);
            }
        };

        const openEditProfile = () => {
            editProfileForm.value = { 
                nom: artistProfile.value.nom, 
                biographie: artistProfile.value.biographie || '',
                titre_pro: artistProfile.value.titre_pro || '',
                citation: artistProfile.value.citation || ''
            };
            profileFile.value = null; profileFileName.value = ''; profileError.value = ''; showEditProfile.value = true;
        };

        const closeEditProfile = () => showEditProfile.value = false;

        const handleProfileFileUpload = (event) => {
            const file = event.target.files[0];
            if (file) { profileFile.value = file; profileFileName.value = file.name; }
        };

        const submitProfileUpdate = async () => {
            profileError.value = '';
            if (!editProfileForm.value.nom) { profileError.value = "Le nom est obligatoire."; return; }
            isSavingProfile.value = true;

            try {
                let finalAvatarUrl = artistProfile.value.image_url;
                if (profileFile.value) {
                    const fileExt = profileFile.value.name.split('.').pop();
                    const fileName = `${Date.now()}-avatar.${fileExt}`;
                    const { error: uploadError } = await supabase.storage.from('avatars').upload(fileName, profileFile.value);
                    if (uploadError) throw uploadError;
                    const { data: publicUrlData } = supabase.storage.from('avatars').getPublicUrl(fileName);
                    finalAvatarUrl = publicUrlData.publicUrl;
                }

                const { error: updateError } = await supabase.from('artistes')
                    .update({ 
                        nom: editProfileForm.value.nom, 
                        biographie: editProfileForm.value.biographie,
                        titre_pro: editProfileForm.value.titre_pro,
                        citation: editProfileForm.value.citation,
                        image_url: finalAvatarUrl
                    }).eq('id', artistProfile.value.id);

                if (updateError) throw updateError;

                artistProfile.value = { ...artistProfile.value, ...editProfileForm.value, image_url: finalAvatarUrl };
                closeEditProfile();
            } catch (error) { profileError.value = error.message; } finally { isSavingProfile.value = false; }
        };

        const logout = async () => { await supabase.auth.signOut(); window.location.href = 'index.php'; };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchStudioData(); } 
            else { window.location.href = 'portail-artiste.php'; }
        });

        return { 
            isLoading, errorMessage, artistProfile, myArtworks, logout,
            mySalesRevenue, soldCount, availableCount, adminMessages, formatDate,
            showAddModal, newArtwork, isSaving, modalError, submitArtwork, deleteArtwork, handleFileUpload, selectedFileName, closeModal,
            showEditProfile, isSavingProfile, profileError, editProfileForm, openEditProfile, closeEditProfile, handleProfileFileUpload, profileFileName, submitProfileUpdate
        };
    }
}).mount('#studio-app');
</script>

<?php include 'includes/footer.php'; ?>