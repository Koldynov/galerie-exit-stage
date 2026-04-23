const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const currentUser = ref(null);
        const artistProfile = ref(null);
        const myArtworks = ref([]);
        const isLoading = ref(true);
        const errorMessage = ref('');
        
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

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

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
                const ids = oeuvresData.map(o => o.id);
                if (ids.length > 0) {
                    const { data: sales } = await supabase.from('commandes').select('*').in('oeuvre_id', ids);
                    if (sales) mySales.value = sales;
                }
            }

            const { data: msgs } = await supabase.from('admin_messages').select('*').eq('artiste_id', artistData.id).order('created_at', {ascending: false});
            if (msgs) adminMessages.value = msgs;

            isLoading.value = false;
        };

        const handleFileUpload = (event) => {
            const file = event.target.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024; 
                if (file.size > maxSize) { alert("Le fichier est trop volumineux. La taille maximale est de 5 Mo."); event.target.value = ''; return; }
                selectedFile.value = file; selectedFileName.value = file.name; 
            }
        };

        const closeModal = () => { showAddModal.value = false; modalError.value = ''; newArtwork.value = { titre: '', description: '', prix: '', categorie: '' }; selectedFile.value = null; selectedFileName.value = ''; };

        const submitArtwork = async () => {
            modalError.value = '';
            if (!newArtwork.value.titre || !selectedFile.value || !newArtwork.value.categorie) { modalError.value = "Titre, catégorie et image sont obligatoires."; return; }
            isSaving.value = true;
            try {
                const fileExt = selectedFile.value.name.split('.').pop();
                const fileName = `${Date.now()}-${Math.random().toString(36).substring(2)}.${fileExt}`;
                const { error: uploadError } = await supabase.storage.from('artworks').upload(`uploads/${fileName}`, selectedFile.value);
                if (uploadError) throw uploadError;

                const { data: publicUrlData } = supabase.storage.from('artworks').getPublicUrl(`uploads/${fileName}`);
                
                const { data: insertData, error: insertError } = await supabase.from('oeuvres').insert([{ 
                    artiste_id: artistProfile.value.id, titre: newArtwork.value.titre, description: newArtwork.value.description, image_url: publicUrlData.publicUrl, statut: 'disponible', categorie: newArtwork.value.categorie, prix: parseFloat(newArtwork.value.prix) || 0 
                }]).select();

                if (insertError) throw insertError;
                myArtworks.value.unshift(insertData[0]);
                closeModal();
            } catch (error) { modalError.value = error.message; } finally { isSaving.value = false; }
        };

        const deleteArtwork = async (id) => { if(confirm("Supprimer cette œuvre ?")) { myArtworks.value = myArtworks.value.filter(o => o.id !== id); await supabase.from('oeuvres').delete().eq('id', id); } };

        const openEditProfile = () => { editProfileForm.value = { nom: artistProfile.value.nom, biographie: artistProfile.value.biographie || '', titre_pro: artistProfile.value.titre_pro || '', citation: artistProfile.value.citation || '' }; profileFile.value = null; profileFileName.value = ''; profileError.value = ''; showEditProfile.value = true; };
        const closeEditProfile = () => showEditProfile.value = false;
        const handleProfileFileUpload = (event) => { const file = event.target.files[0]; if (file) { profileFile.value = file; profileFileName.value = file.name; } };

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

                const { error: updateError } = await supabase.from('artistes').update({ nom: editProfileForm.value.nom, biographie: editProfileForm.value.biographie, titre_pro: editProfileForm.value.titre_pro, citation: editProfileForm.value.citation, image_url: finalAvatarUrl }).eq('id', artistProfile.value.id);
                if (updateError) throw updateError;
                artistProfile.value = { ...artistProfile.value, ...editProfileForm.value, image_url: finalAvatarUrl };
                closeEditProfile();
            } catch (error) { profileError.value = error.message; } finally { isSavingProfile.value = false; }
        };

        const logout = async () => { await supabase.auth.signOut(); window.location.href = '/galerie/'; }; // ОНОВЛЕНО

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchStudioData(); } 
            else { window.location.href = '/galerie/portail-artiste'; } // ОНОВЛЕНО
        });

        return { isLoading, errorMessage, artistProfile, myArtworks, logout, mySalesRevenue, soldCount, availableCount, adminMessages, formatDate, showAddModal, newArtwork, isSaving, modalError, submitArtwork, deleteArtwork, handleFileUpload, selectedFileName, closeModal, showEditProfile, isSavingProfile, profileError, editProfileForm, openEditProfile, closeEditProfile, handleProfileFileUpload, profileFileName, submitProfileUpdate };
    }
}).mount('#studio-app');