const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const currentUser = ref(null);
        const artistProfile = ref(null);
        const myArtworks = ref([]);
        const isLoading = ref(true);
        const errorMessage = ref('');

        const currentTab = ref('oeuvres');
        const mySales = ref([]);
        const adminMessages = ref([]);

        // Змінні для пошти
        const showMessagesModal = ref(false);

        const showAddModal = ref(false);
        const isSaving = ref(false);
        const modalError = ref('');
        const newArtwork = ref({ titre: '', description: '', prix: '', categorie: '', sous_categories: [] });
        const selectedFile = ref(null);
        const selectedFileName = ref('');

        const showEditProfile = ref(false);
        const isSavingProfile = ref(false);
        const profileError = ref('');
        const editProfileForm = ref({ nom: '', biographie: '', titre_pro: '', citation: '' });
        const profileFile = ref(null);
        const profileFileName = ref('');

        const showEditModal = ref(false);
        const isEditing = ref(false);
        const editImageFile = ref(null);
        const editForm = ref({ id: null, titre: '', description: '', prix: 0, categorie: '', image_url: '' });

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const mySalesRevenue = computed(() => mySales.value.reduce((sum, s) => sum + (Number(s.montant_total) || 0), 0));
        const soldCount = computed(() => myArtworks.value.filter(o => o.statut === 'vendu').length);
        const availableCount = computed(() => myArtworks.value.filter(o => !o.statut || o.statut === 'disponible').length);

        const formatDate = (dateString) => new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });

        // Рахуємо нові повідомлення
        const unreadMessagesCount = computed(() => {
            if (!currentUser.value) return 0;
            const lastRead = parseInt(localStorage.getItem(`last_read_msg_${currentUser.value.id}`)) || 0;
            return Math.max(0, adminMessages.value.length - lastRead);
        });

        // Відкриття пошти скидає лічильник
        const openMessagesModal = () => {
            showMessagesModal.value = true;
            localStorage.setItem(`last_read_msg_${currentUser.value.id}`, adminMessages.value.length);
        };

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
                    const { data: sales } = await supabase
                        .from('commandes')
                        .select('*, oeuvres(titre, image_url)')
                        .in('oeuvre_id', ids)
                        .order('created_at', { ascending: false });
                    if (sales) mySales.value = sales;
                }
            }

            const { data: msgs } = await supabase.from('admin_messages').select('*').eq('artiste_id', artistData.id).order('created_at', { ascending: false });
            if (msgs) adminMessages.value = msgs;

            isLoading.value = false;
        };

        const markAsShipped = async (sale) => {
            const trackingNum = prompt("Veuillez entrer le numéro de suivi (tracking number) pour cette expédition :");

            if (trackingNum === null) return;

            if (trackingNum.trim() === '') {
                alert("Le numéro de suivi est obligatoire pour expédier l'œuvre.");
                return;
            }

            try {
                const { error } = await supabase
                    .from('commandes')
                    .update({
                        status: 'expedie',
                        statut_livraison: 'expedie',
                        tracking_number: trackingNum
                    })
                    .eq('id', sale.id);

                if (error) throw error;

                sale.status = 'expedie';
                sale.statut_livraison = 'expedie';
                sale.tracking_number = trackingNum;

                await fetch('/api/notify-shipping', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        client_email: sale.user_email,
                        client_name: sale.nom_client,
                        oeuvre_titre: sale.oeuvres?.titre || 'Œuvre',
                        tracking_number: trackingNum,
                        artist_name: artistProfile.value.nom
                    })
                });

                alert("L'œuvre a été marquée comme expédiée et le client a été notifié !");

            } catch (err) {
                console.error("Erreur lors de l'expédition:", err);
                alert("Une erreur s'est produite lors de la mise à jour.");
            }
        };

        const handleFileUpload = (event) => {
            const file = event.target.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) { alert("Le fichier est trop volumineux. La taille maximale est de 5 Mo."); event.target.value = ''; return; }
                selectedFile.value = file; selectedFileName.value = file.name;
            }
        };

        const closeModal = () => { showAddModal.value = false; modalError.value = ''; newArtwork.value = { titre: '', description: '', prix: '', categorie: '', sous_categories: [] }; selectedFile.value = null; selectedFileName.value = ''; };

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

                const sousCatString = newArtwork.value.sous_categories ? newArtwork.value.sous_categories.join(', ') : '';

                const { data: insertData, error: insertError } = await supabase.from('oeuvres').insert([{
                    artiste_id: artistProfile.value.id,
                    titre: newArtwork.value.titre,
                    description: newArtwork.value.description,
                    image_url: publicUrlData.publicUrl,
                    statut: 'disponible',
                    categorie: newArtwork.value.categorie,
                    sous_categorie: sousCatString,
                    prix: parseFloat(newArtwork.value.prix) || 0
                }]).select();

                if (insertError) throw insertError;
                myArtworks.value.unshift(insertData[0]);
                closeModal();
            } catch (error) { modalError.value = error.message; } finally { isSaving.value = false; }
        };

        const deleteArtwork = async (id) => { if (confirm("Supprimer cette œuvre ?")) { myArtworks.value = myArtworks.value.filter(o => o.id !== id); await supabase.from('oeuvres').delete().eq('id', id); } };

        const openEditModal = (oeuvre) => {
            editForm.value = { ...oeuvre };
            editImageFile.value = null;
            showEditModal.value = true;
        };

        const handleEditFileUpload = (event) => {
            editImageFile.value = event.target.files[0];
        };

        const submitEditModal = async () => {
            isEditing.value = true;
            try {
                let finalImageUrl = editForm.value.image_url;

                if (editImageFile.value) {
                    const fileExt = editImageFile.value.name.split('.').pop();
                    const fileName = `${Date.now()}_${Math.random().toString(36).substring(7)}.${fileExt}`;

                    const { error: uploadError } = await supabase.storage.from('artworks').upload(`uploads/${fileName}`, editImageFile.value);
                    if (uploadError) throw new Error("Erreur lors de l'upload de la nouvelle image.");

                    const { data } = supabase.storage.from('artworks').getPublicUrl(`uploads/${fileName}`);
                    finalImageUrl = data.publicUrl;
                }

                const { error: updateError } = await supabase.from('oeuvres')
                    .update({
                        titre: editForm.value.titre,
                        description: editForm.value.description,
                        prix: parseFloat(editForm.value.prix) || 0,
                        categorie: editForm.value.categorie,
                        image_url: finalImageUrl
                    })
                    .eq('id', editForm.value.id);

                if (updateError) throw updateError;

                showEditModal.value = false;
                await fetchStudioData();

            } catch (error) {
                alert("Erreur: " + error.message);
            } finally {
                isEditing.value = false;
            }
        };

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

        const logout = async () => { await supabase.auth.signOut(); window.location.href = '/'; };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) { currentUser.value = session.user; await fetchStudioData(); }
            else { window.location.href = '/portail-artiste'; }
        });

        return {
            isLoading, errorMessage, artistProfile, myArtworks, logout,
            mySalesRevenue, soldCount, availableCount, adminMessages, formatDate,
            showAddModal, newArtwork, isSaving, modalError, submitArtwork, deleteArtwork,
            handleFileUpload, selectedFileName, closeModal, showEditProfile, isSavingProfile,
            profileError, editProfileForm, openEditProfile, closeEditProfile, handleProfileFileUpload,
            profileFileName, submitProfileUpdate,
            currentTab, mySales, markAsShipped,
            showEditModal, isEditing, editForm, openEditModal, handleEditFileUpload, submitEditModal,
            showMessagesModal, unreadMessagesCount, openMessagesModal
        };
    }
}).mount('#studio-app');