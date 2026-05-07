const { createApp, ref, computed, onMounted, nextTick, watch } = Vue;

createApp({
    setup() {
        const isAuthenticating = ref(true);
        const currentTab = ref('overview');

        const stats = ref({ oeuvres: 0, artistes: 0, favoris: 0 });
        const allOeuvres = ref([]);
        const allArtistes = ref([]);
        const allNews = ref([]);
        const totalRevenue = ref(0);

        const allCommandes = ref([]);
        const allDemandes = ref([]);

        const realRevenue = computed(() => allCommandes.value.reduce((sum, cmd) => sum + (Number(cmd.montant_total) || 0), 0));
        const potentialRevenue = computed(() => allOeuvres.value.filter(o => o.statut !== 'vendu').reduce((sum, o) => sum + (Number(o.prix) || 0), 0));

        const pendingArtists = computed(() => allArtistes.value.filter(a => !a.is_approved));
        const approvedArtists = computed(() => allArtistes.value.filter(a => a.is_approved));
        const soldOeuvresCount = computed(() => allOeuvres.value.filter(o => o.statut === 'vendu').length);

        const itemsPerPage = 10;

        const currentPageCommandes = ref(1);
        const totalPagesCommandes = computed(() => Math.ceil(allCommandes.value.length / itemsPerPage) || 1);
        const paginatedCommandes = computed(() => { const start = (currentPageCommandes.value - 1) * itemsPerPage; return allCommandes.value.slice(start, start + itemsPerPage); });
        const prevPageCommandes = () => { if (currentPageCommandes.value > 1) currentPageCommandes.value--; };
        const nextPageCommandes = () => { if (currentPageCommandes.value < totalPagesCommandes.value) currentPageCommandes.value++; };

        const currentPageDemandes = ref(1);
        const totalPagesDemandes = computed(() => Math.ceil(allDemandes.value.length / itemsPerPage) || 1);
        const paginatedDemandes = computed(() => { const start = (currentPageDemandes.value - 1) * itemsPerPage; return allDemandes.value.slice(start, start + itemsPerPage); });
        const prevPageDemandes = () => { if (currentPageDemandes.value > 1) currentPageDemandes.value--; };
        const nextPageDemandes = () => { if (currentPageDemandes.value < totalPagesDemandes.value) currentPageDemandes.value++; };

        const showDemandeModal = ref(false);
        const activeDemande = ref({});
        const isSavingDemande = ref(false);

        const showEmailComposer = ref(false);
        const emailSubject = ref('');
        const emailBody = ref('');
        const isSendingEmail = ref(false);
        const emailSentSuccess = ref(false);

        const allMessages = ref([]);
        const unreadMessagesCount = computed(() => allMessages.value.filter(m => m.statut === 'nouveau').length);
        const showReplyModal = ref(false);
        const activeMessage = ref({});
        const replyText = ref('');
        const isSendingReply = ref(false);

        const showAdminMsgModal = ref(false);
        const activeMsgArtist = ref(null);
        const adminMsgText = ref('');
        const isSendingMsg = ref(false);

        const openMessageModal = (artiste) => { activeMsgArtist.value = artiste; adminMsgText.value = ''; showAdminMsgModal.value = true; };

        const sendAdminMessage = async () => {
            if (!adminMsgText.value.trim()) return alert('Message vide.');
            isSendingMsg.value = true;
            try {
                const { error } = await supabase.from('admin_messages').insert([{ artiste_id: activeMsgArtist.value.id, message: adminMsgText.value }]);
                if (error) throw error;
                showAdminMsgModal.value = false;
                alert("Message envoyé avec succès !");
            } catch (e) { alert("Erreur: " + e.message); } finally { isSendingMsg.value = false; }
        };

        const approveArtist = async (id) => {
            const { error } = await supabase.from('artistes').update({ is_approved: true }).eq('id', id);
            if (!error) { const art = allArtistes.value.find(a => a.id === id); if (art) art.is_approved = true; } else alert("Erreur d'approbation.");
        };

        const showNewsModal = ref(false);
        const isSavingNews = ref(false);
        const newNews = ref({ titre: '', categorie: ['Actualité'], image_url: '', description: '', event_date: '', event_end_date: '' });
        const selectedFile = ref(null);
        const isEditingNews = ref(false);
        const editingNewsId = ref(null);
        const deleteModal = ref({ show: false, type: '', id: null, message: '' });
        const isDeleting = ref(false);

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const checkAdminAccess = async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (!session || session.user.email !== 'admin@gmail.com') {
                window.location.href = '/galerie/'; // ОНОВЛЕНО
            } else {
                isAuthenticating.value = false;
                await fetchAllData();
            }
        };

        const renderChart = () => {
            const ctx = document.getElementById('salesChart');
            if (!ctx) return;
            const labels = []; const dataMap = {};
            for (let i = 6; i >= 0; i--) {
                const d = new Date(); d.setDate(d.getDate() - i);
                const dateStr = d.toISOString().split('T')[0];
                labels.push(d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' }));
                dataMap[dateStr] = 0;
            }
            allCommandes.value.forEach(cmd => {
                if (cmd.status === 'Payé' || !cmd.status) {
                    const cmdDate = new Date(cmd.created_at).toISOString().split('T')[0];
                    if (dataMap[cmdDate] !== undefined) dataMap[cmdDate] += Number(cmd.montant_total || 0);
                }
            });
            if (window.mySalesChart) window.mySalesChart.destroy();
            window.mySalesChart = new Chart(ctx, {
                type: 'line', data: { labels: labels, datasets: [{ label: 'Revenus (€)', data: Object.values(dataMap), borderColor: '#d4af37', backgroundColor: 'rgba(212, 175, 55, 0.1)', borderWidth: 2, fill: true, tension: 0.4 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#888' } }, x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#888' } } } }
            });
        };

        watch(currentTab, (newTab) => { if (newTab === 'overview') nextTick(() => renderChart()); });

        const fetchAllData = async () => {
            const { data: oeuvres } = await supabase.from('oeuvres').select('*, artistes(nom)').order('created_at', { ascending: false });
            if (oeuvres) { oeuvres.forEach(o => { if (!o.statut) o.statut = 'disponible'; }); allOeuvres.value = oeuvres; stats.value.oeuvres = oeuvres.length; }
            const { data: artistes } = await supabase.from('artistes').select('*').order('nom', { ascending: true });
            if (artistes) { allArtistes.value = artistes; stats.value.artistes = artistes.length; }
            const { data: news } = await supabase.from('journal').select('*').order('created_at', { ascending: false });
            if (news) allNews.value = news;
            const { data: messages } = await supabase.from('contact_messages').select('*').order('created_at', { ascending: false });
            if (messages) allMessages.value = messages;
            const { data: commandes } = await supabase
                .from('commandes')
                .select('*, oeuvres(titre, artistes(nom))')
                .order('created_at', { ascending: false });
            if (commandes) allCommandes.value = commandes;
            const { data: demandes } = await supabase.from('demandes').select('*, oeuvres(titre, image_url)').order('created_at', { ascending: false });
            if (demandes) allDemandes.value = demandes;
            nextTick(() => { if (currentTab.value === 'overview') renderChart(); });
        };

        const updateStatut = async (id, newStatut) => { const { error } = await supabase.from('oeuvres').update({ statut: newStatut }).eq('id', id); if (error) alert("Erreur lors de la mise à jour du statut."); };
        const pendingDemandesCount = computed(() => allDemandes.value.filter(d => !d.statut || d.statut === 'Nouveau').length);
        const getStatusClass = (statut) => { if (!statut || statut === 'Nouveau') return 'new'; if (statut === 'En cours') return 'processing'; if (statut === 'Conclu') return 'success'; return 'canceled'; };
        const getDemandesByStatut = (statut) => allDemandes.value.filter(d => { if (statut === 'Nouveau') return !d.statut || d.statut === 'Nouveau'; return d.statut === statut; });

        const openReplyModal = (msg) => {
            activeMessage.value = msg;
            replyText.value = `Bonjour ${msg.nom},\n\nMerci de nous avoir contactés concernant : ${msg.sujet}.\n\n\n\nCordialement,\nSabrina, Galerie EXIT`;
            showReplyModal.value = true;
        };

        const sendContactReply = async () => {
            if (!replyText.value.trim()) return alert("Veuillez écrire une réponse.");
            isSendingReply.value = true;

            // Використовуємо той самий шаблон EmailJS, що і для Kanban!
            const templateParams = {
                to_email: activeMessage.value.email,
                to_name: activeMessage.value.nom,
                subject: `RE: ${activeMessage.value.sujet} - Galerie EXIT`,
                message: replyText.value
            };

            try {
                // Відправляємо лист через EmailJS
                await emailjs.send('service_5jqvnbe', 'template_qgto97i', templateParams, '4YhmmUwZrYVZg7zTc');

                // Оновлюємо статус в Supabase на "repondu"
                const { error } = await supabase.from('contact_messages').update({ statut: 'repondu' }).eq('id', activeMessage.value.id);
                if (error) throw error;

                // Оновлюємо локально
                const msgIndex = allMessages.value.findIndex(m => m.id === activeMessage.value.id);
                if (msgIndex !== -1) allMessages.value[msgIndex].statut = 'repondu';

                alert("Réponse envoyée avec succès !");
                showReplyModal.value = false;
            } catch (error) {
                alert("Erreur lors de l'envoi de la réponse.");
                console.error(error);
            } finally {
                isSendingReply.value = false;
            }
        };

        const dragStart = (event, demande) => { event.dataTransfer.setData('demandeId', demande.id); event.target.style.opacity = '0.4'; setTimeout(() => { event.target.style.opacity = '1'; }, 100); };
        const dropCard = async (event, newStatut) => {
            const demandeId = event.dataTransfer.getData('demandeId');
            const demande = allDemandes.value.find(d => d.id == demandeId);
            if (demande && demande.statut !== newStatut) {
                demande.statut = newStatut;
                const { error } = await supabase.from('demandes').update({ statut: newStatut }).eq('id', demandeId);
                if (error) { alert("Erreur lors de la mise à jour du statut"); fetchAllData(); }
            }
        };

        const displayClientName = (nom, email) => { if (email === 'admin@gmail.com' && nom === 'Membre Connecté') return 'Administrateur'; if (nom === 'Membre Connecté' && email) return email.split('@')[0]; return nom; };

        const openDemandeModal = (demande) => {
            activeDemande.value = { ...demande };
            if (!activeDemande.value.statut) activeDemande.value.statut = 'Nouveau';
            showEmailComposer.value = false; emailSentSuccess.value = false;
            emailSubject.value = `Concernant votre demande pour "${demande.oeuvres?.titre || 'l\'œuvre'}" - Galerie EXIT`;
            emailBody.value = `Bonjour ${displayClientName(demande.nom, demande.email)},\n\nSuite à votre demande concernant l'œuvre "${demande.oeuvres?.titre || 'l\'œuvre'}", nous avons le plaisir de vous informer que...\n\nLe prix est de : [PRIX] €.\n\nCordialement,\nSabrina, Galerie EXIT`;
            showDemandeModal.value = true;
        };
        const closeDemandeModal = () => { showDemandeModal.value = false; };
        const toggleEmailComposer = () => { showEmailComposer.value = !showEmailComposer.value; };

        const sendEmail = async () => {
            if (!emailSubject.value || !emailBody.value) return alert("Veuillez remplir le sujet et le message.");
            isSendingEmail.value = true;
            const templateParams = { to_email: activeDemande.value.email, to_name: activeDemande.value.nom, subject: emailSubject.value, message: emailBody.value };
            try {
                await emailjs.send('service_5jqvnbe', 'template_qgto97i', templateParams, '4YhmmUwZrYVZg7zTc');
                emailSentSuccess.value = true;
                activeDemande.value.statut = 'En cours';
                activeDemande.value.notes_internes = (activeDemande.value.notes_internes || '') + `\n[Email envoyé le ${new Date().toLocaleDateString()}] : ${emailSubject.value}`;
                await saveDemandeStatus();
            } catch (error) { alert("Erreur lors de l'envoi de l'email : " + (error.text || "Erreur inconnue")); } finally { isSendingEmail.value = false; }
        };

        const saveDemandeStatus = async () => {
            isSavingDemande.value = true;
            try {
                const { error } = await supabase.from('demandes').update({ statut: activeDemande.value.statut, notes_internes: activeDemande.value.notes_internes }).eq('id', activeDemande.value.id);
                if (error) throw error;
                const index = allDemandes.value.findIndex(d => d.id === activeDemande.value.id);
                if (index !== -1) Object.assign(allDemandes.value[index], activeDemande.value);
                closeDemandeModal();
            } catch (e) { alert("Erreur : " + e.message); } finally { isSavingDemande.value = false; }
        };

        const toggleNewsCategory = (cat) => { if (newNews.value.categorie.includes(cat)) { newNews.value.categorie = newNews.value.categorie.filter(c => c !== cat); } else { newNews.value.categorie.push(cat); } };

        const editNews = (news) => { isEditingNews.value = true; editingNewsId.value = news.id; newNews.value = { titre: news.titre, categorie: news.categorie ? news.categorie.split(', ') : [], image_url: news.image_url, description: news.description, event_date: news.event_date || '', event_end_date: news.event_end_date || '' }; showNewsModal.value = true; };
        const closeNewsModal = () => { showNewsModal.value = false; isEditingNews.value = false; editingNewsId.value = null; newNews.value = { titre: '', categorie: ['Actualité'], image_url: '', description: '', event_date: '', event_end_date: '' }; selectedFile.value = null; };

        const getCountByCat = (cat) => allOeuvres.value.filter(o => o.categorie === cat).length;

        const toggleFeatured = async (type, id, currentStatus) => {
            const tableName = type === 'oeuvre' ? 'oeuvres' : 'artistes';
            const { error } = await supabase.from(tableName).update({ is_featured: !currentStatus }).eq('id', id);
            if (!error) {
                if (type === 'oeuvre') { const item = allOeuvres.value.find(o => o.id === id); if (item) item.is_featured = !currentStatus; }
                else { const item = allArtistes.value.find(a => a.id === id); if (item) item.is_featured = !currentStatus; }
            } else { alert("Erreur."); }
        };

        const saveNews = async () => {
            if (!newNews.value.titre) return alert("Le titre est obligatoire");
            if (newNews.value.categorie.length === 0) return alert("Veuillez sélectionner au moins une catégorie");
            isSavingNews.value = true;
            try {
                let finalImageUrl = newNews.value.image_url;
                if (selectedFile.value) {
                    const fileName = `${Date.now()}_${selectedFile.value.name}`;
                    const { error: uploadError } = await supabase.storage.from('galerie_images').upload(`journal/${fileName}`, selectedFile.value);
                    if (uploadError) throw uploadError;
                    const { data: publicUrlData } = supabase.storage.from('galerie_images').getPublicUrl(`journal/${fileName}`);
                    finalImageUrl = publicUrlData.publicUrl;
                }
                const isEvent = newNews.value.categorie.includes('Exposition') || newNews.value.categorie.includes('Événement');
                const payload = { titre: newNews.value.titre, categorie: newNews.value.categorie.join(', '), description: newNews.value.description, image_url: finalImageUrl, event_date: isEvent ? newNews.value.event_date : null, event_end_date: isEvent ? newNews.value.event_end_date : null };
                if (isEditingNews.value) { const { error } = await supabase.from('journal').update(payload).eq('id', editingNewsId.value); if (error) throw error; }
                else { const { error } = await supabase.from('journal').insert([payload]); if (error) throw error; }
                closeNewsModal(); fetchAllData();
            } catch (e) { alert("Erreur: " + e.message); } finally { isSavingNews.value = false; }
        };

        const toggleFeaturedNews = async (id, currentStatus) => { await supabase.from('journal').update({ is_featured: false }).neq('id', id); const { error } = await supabase.from('journal').update({ is_featured: !currentStatus }).eq('id', id); if (!error) fetchAllData(); };
        const formatDate = (dateString) => new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
        const formatDateShort = (dateString) => new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });

        const askDelete = (type, id) => {
            deleteModal.value.type = type; deleteModal.value.id = id; deleteModal.value.show = true;
            if (type === 'oeuvre') deleteModal.value.message = "Cette action est irréversible. L'œuvre sera définitivement supprimée.";
            else if (type === 'artiste') deleteModal.value.message = "ATTENTION : Supprimer cet artiste supprimera également TOUTES ses œuvres.";
            else if (type === 'news') deleteModal.value.message = "Voulez-vous vraiment supprimer cet article ?";
        };
        const closeDeleteModal = () => { if (!isDeleting.value) { deleteModal.value.show = false; deleteModal.value.id = null; } };

        const confirmDelete = async () => {
            isDeleting.value = true;
            try {
                if (deleteModal.value.type === 'oeuvre') await supabase.from('oeuvres').delete().eq('id', deleteModal.value.id);
                else if (deleteModal.value.type === 'artiste') { await supabase.from('oeuvres').delete().eq('artiste_id', deleteModal.value.id); await supabase.from('artistes').delete().eq('id', deleteModal.value.id); }
                else if (deleteModal.value.type === 'news') await supabase.from('journal').delete().eq('id', deleteModal.value.id);
                await fetchAllData();
            } catch (error) { console.error(error); } finally { isDeleting.value = false; closeDeleteModal(); }
        };

        const handleFileUpload = (event) => { const file = event.target.files[0]; if (file) { const maxSize = 5 * 1024 * 1024; if (file.size > maxSize) { alert("Max 5 Mo."); event.target.value = ''; return; } selectedFile.value = file; selectedFileName.value = file.name; } };

        const logout = async () => { await supabase.auth.signOut(); window.location.href = '/galerie/'; };

        // --- ФУНКЦІЯ ЕКСПОРТУ В EXCEL (CSV) ---
        const exportToCSV = () => {
            if (allCommandes.value.length === 0) {
                alert("Aucune donnée à exporter.");
                return;
            }

            // 1. Заголовки колонок в Excel
            const headers = ['ID Commande', 'Date', 'Client', 'Email', 'Oeuvre', 'Montant (€)', 'Statut'];

            // 2. Формуємо рядки з даними
            const rows = allCommandes.value.map(cmd => {
                const date = new Date(cmd.created_at).toLocaleDateString('fr-FR');
                const clientNom = cmd.nom_client || 'Client';
                const clientEmail = cmd.user_email || '';
                const oeuvreTitre = cmd.oeuvres?.titre || 'Inconnu';
                const montant = cmd.montant_total || 0;
                const statut = cmd.status || 'En attente';

                return [cmd.id, date, clientNom, clientEmail, oeuvreTitre, montant, statut];
            });

            // 3. Склеюємо все до купи. 
            // ВАЖЛИВО: Використовуємо крапку з комою (;), бо французький Excel очікує саме її!
            const csvContent = [
                headers.join(';'),
                ...rows.map(row => row.map(item => `"${String(item).replace(/"/g, '""')}"`).join(';'))
            ].join('\n');

            // 4. Додаємо BOM (щоб Excel правильно читав французькі літери з акцентами: é, è, ç)
            const bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
            const blob = new Blob([bom, csvContent], { type: 'text/csv;charset=utf-8;' });

            // 5. Створюємо віртуальне посилання і змушуємо браузер завантажити файл
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            // Назва файлу з сьогоднішньою датою
            link.setAttribute("download", `export_ventes_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };// ОНОВЛЕНО

        onMounted(() => { checkAdminAccess(); });

        return {
            isAuthenticating, currentTab, stats, allOeuvres, allArtistes, totalRevenue, getCountByCat, toggleFeatured, askDelete, confirmDelete, closeDeleteModal, deleteModal, isDeleting, logout,
            allNews, showNewsModal, newNews, saveNews, isSavingNews, toggleFeaturedNews, formatDate, formatDateShort, selectedFile, handleFileUpload, editNews, closeNewsModal, isEditingNews, editingNewsId, toggleNewsCategory,
            allCommandes, allDemandes, paginatedCommandes, totalPagesCommandes, currentPageCommandes, prevPageCommandes, nextPageCommandes, paginatedDemandes, totalPagesDemandes, currentPageDemandes, prevPageDemandes,
            realRevenue, potentialRevenue, pendingDemandesCount, getStatusClass, updateStatut, pendingArtists, approvedArtists, soldOeuvresCount, approveArtist, exportToCSV,
            showAdminMsgModal, activeMsgArtist, adminMsgText, isSendingMsg, openMessageModal, sendAdminMessage, getDemandesByStatut, dragStart, dropCard, displayClientName,
            openDemandeModal, closeDemandeModal, showDemandeModal, activeDemande, saveDemandeStatus, isSavingDemande, showEmailComposer, toggleEmailComposer, emailSubject, emailBody, sendEmail, isSendingEmail, emailSentSuccess,
            allMessages, unreadMessagesCount, showReplyModal, activeMessage, replyText, isSendingReply, openReplyModal, sendContactReply
        };
    }
}).mount('#admin-dashboard');