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

        const currentPageOeuvres = ref(1);
        const totalPagesOeuvres = computed(() => Math.ceil(allOeuvres.value.length / itemsPerPage) || 1);
        const paginatedOeuvres = computed(() => {
            const start = (currentPageOeuvres.value - 1) * itemsPerPage;
            return allOeuvres.value.slice(start, start + itemsPerPage);
        });
        const prevPageOeuvres = () => { if (currentPageOeuvres.value > 1) currentPageOeuvres.value--; };
        const nextPageOeuvres = () => { if (currentPageOeuvres.value < totalPagesOeuvres.value) currentPageOeuvres.value++; };

        const showDemandeModal = ref(false);
        const activeDemande = ref({});
        const isSavingDemande = ref(false);

        // ЗМІННІ ДЛЯ NEWSLETTER
        const subscribersList = ref([]);
        const showNewsletterModal = ref(false);
        const newsletterTarget = ref('all');
        const newsletterSubject = ref('');
        const newsletterBody = ref('');
        const isSendingNewsletter = ref(false);
        const newsletterProgress = ref({ current: 0, total: 0 });
        const newsletterAttachment = ref(null);
        const newsletterAttachmentName = ref('');

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

        const allAteliers = ref([]);
        const ateliersOnly = computed(() => allAteliers.value.filter(a => a.type_experience === 'Atelier' || !a.type_experience));
        const evenementsOnly = computed(() => allAteliers.value.filter(a => a.type_experience === 'Événement'));
        const showAtelierModal = ref(false);
        const isEditingAtelier = ref(false);
        const isSavingAtelier = ref(false);
        const atelierForm = ref({ titre: '', professeur: 'Sabrina', description: '', date_atelier: '', prix: 50, places_totales: 10, image_url: '', type_experience: 'Atelier', categorie: 'Peinture', duree: '', lieu: 'Galerie EXIT', avantage_1: '', avantage_2: '', avantage_3: '' });
        const atelierFile = ref(null);

        const showParticipantsModal = ref(false);
        const currentAtelierParticipants = ref([]);
        const currentAtelierName = ref('');
        const isLoadingParticipants = ref(false);

        const viewParticipants = async (atelier) => {
            currentAtelierName.value = atelier.titre;
            isLoadingParticipants.value = true;
            showParticipantsModal.value = true;
            currentAtelierParticipants.value = [];

            try {
                const { data, error } = await supabase
                    .from('atelier_reservations')
                    .select('*')
                    .eq('atelier_id', atelier.id)
                    .order('created_at', { ascending: false });

                if (error) throw error;
                if (data) currentAtelierParticipants.value = data;
            } catch (err) {
                console.error(err);
                alert("Erreur lors du chargement des participants.");
            } finally {
                isLoadingParticipants.value = false;
            }
        };

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
        const newNews = ref({ titre: '', categorie: ['Actualité'], image_url: '', description: '', contenu: '', images_secondaires: [], event_date: '', event_end_date: '' });
        const selectedFile = ref(null);
        const selectedFileName = ref('');
        const isEditingNews = ref(false);
        const editingNewsId = ref(null);
        const deleteModal = ref({ show: false, type: '', id: null, message: '' });
        const isDeleting = ref(false);

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const checkAdminAccess = async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (!session || session.user.email !== 'admin@gmail.com') {
                window.location.href = '/galerie/';
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
            const { data: ateliers } = await supabase.from('ateliers').select('*').order('date_atelier', { ascending: true });
            if (ateliers) allAteliers.value = ateliers;
            const { data: messages } = await supabase.from('contact_messages').select('*').order('created_at', { ascending: false });
            if (messages) allMessages.value = messages;
            const { data: commandes } = await supabase
                .from('commandes')
                .select('*, oeuvres(titre, artistes(nom))')
                .order('created_at', { ascending: false });
            if (commandes) allCommandes.value = commandes;
            const { data: demandes } = await supabase.from('demandes').select('*, oeuvres(titre, image_url)').order('created_at', { ascending: false });
            if (demandes) allDemandes.value = demandes;

            // Автоматичне завантаження контактів розсилки
            const { data: subs } = await supabase.from('newsletter_subscribers').select('*').order('created_at', { ascending: false });
            if (subs) subscribersList.value = subs;

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

            const templateParams = {
                to_email: activeMessage.value.email,
                to_name: activeMessage.value.nom,
                subject: `RE: ${activeMessage.value.sujet} - Galerie EXIT`,
                message: replyText.value
            };

            try {
                await emailjs.send('service_5jqvnbe', 'template_qgto97i', templateParams, '4YhmmUwZrYVZg7zTc');
                const { error } = await supabase.from('contact_messages').update({ statut: 'repondu' }).eq('id', activeMessage.value.id);
                if (error) throw error;

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

        const openAtelierModal = () => {
            isEditingAtelier.value = false;
            atelierForm.value = { titre: '', professeur: 'Sabrina', description: '', date_atelier: '', prix: 50, places_totales: 10, image_url: '', type_experience: 'Atelier', categorie: 'Peinture', duree: '', lieu: 'Galerie EXIT', avantage_1: '', avantage_2: '', avantage_3: '' };
            showAtelierModal.value = true;
        };

        const openEvenementModal = () => {
            isEditingAtelier.value = false;
            atelierForm.value = { titre: '', professeur: 'Sabrina', description: '', date_atelier: '', prix: 50, places_totales: 10, image_url: '', type_experience: 'Événement', categorie: 'Dégustation', duree: '', lieu: 'Galerie EXIT', avantage_1: '', avantage_2: '', avantage_3: '' };
            showAtelierModal.value = true;
        };

        const closeAtelierModal = () => { showAtelierModal.value = false; };

        const editAtelier = (at) => {
            isEditingAtelier.value = true;
            const date = new Date(at.date_atelier);
            const formattedDate = date.toISOString().slice(0, 16);
            atelierForm.value = { ...at, date_atelier: formattedDate };
            showAtelierModal.value = true;
        };

        const handleAtelierFile = (e) => { atelierFile.value = e.target.files[0]; };

        const saveAtelier = async () => {
            isSavingAtelier.value = true;
            try {
                let imageUrl = atelierForm.value.image_url;

                if (atelierFile.value) {
                    const fileName = `${Date.now()}_${atelierFile.value.name}`;
                    await supabase.storage.from('galerie_images').upload(`ateliers/${fileName}`, atelierFile.value);
                    const { data } = supabase.storage.from('galerie_images').getPublicUrl(`ateliers/${fileName}`);
                    imageUrl = data.publicUrl;
                }

                const payload = {
                    titre: atelierForm.value.titre,
                    professeur: atelierForm.value.professeur,
                    description: atelierForm.value.description,
                    date_atelier: atelierForm.value.date_atelier,
                    prix: atelierForm.value.prix,
                    places_totales: atelierForm.value.places_totales,
                    places_reservees: parseInt(atelierForm.value.places_reservees) || 0,
                    image_url: imageUrl,
                    type_experience: atelierForm.value.type_experience,
                    categorie: atelierForm.value.categorie,
                    duree: atelierForm.value.duree,
                    lieu: atelierForm.value.lieu,
                    avantage_1: atelierForm.value.avantage_1,
                    avantage_2: atelierForm.value.avantage_2,
                    avantage_3: atelierForm.value.avantage_3
                };

                if (isEditingAtelier.value) {
                    await supabase.from('ateliers').update(payload).eq('id', atelierForm.value.id);
                } else {
                    await supabase.from('ateliers').insert([payload]);
                }

                showAtelierModal.value = false;
                await fetchAllData();
                alert("Enregistré avec succès !");
            } catch (e) {
                alert("Erreur: " + e.message);
            } finally {
                isSavingAtelier.value = false;
            }
        };

        // --- ЛОГІКА ДЛЯ NEWSLETTER ---

        const handleNewsletterAttachment = (event) => {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert("L'image est trop volumineuse (Max 5 Mo).");
                    event.target.value = '';
                    return;
                }
                newsletterAttachment.value = file;
                newsletterAttachmentName.value = file.name;
            }
        };

        const openNewsletterComposer = (target) => {
            newsletterTarget.value = target;
            newsletterSubject.value = '';
            newsletterBody.value = '';
            newsletterAttachment.value = null; // Скидаємо файл
            newsletterAttachmentName.value = '';
            newsletterProgress.value = { current: 0, total: 0 };
            showNewsletterModal.value = true;
        };

        const closeNewsletterModal = () => {
            if (!isSendingNewsletter.value) {
                showNewsletterModal.value = false;
            }
        };

        // ВІДПРАВКА ЧЕРЕЗ BREVO API
        const sendNewsletter = async () => {
            if (!newsletterSubject.value.trim() || !newsletterBody.value.trim()) {
                return alert("Veuillez remplir le sujet et le message.");
            }

            // Використовуємо ключ з конфігу (або твій як запасний)
            const BREVO_API_KEY = window.APP_CONFIG?.BREVO_API_KEY || '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';
            const SENDER_EMAIL = 'smalijhuk2017@gmail.com';
            const SENDER_NAME = 'La Galerie EXIT';

            isSendingNewsletter.value = true;

            // 1. Формуємо HTML-лист (замінюємо переноси рядків на <br>)
            let finalMessageHtml = newsletterBody.value.replace(/\n/g, '<br>');

            // 2. Якщо є фото - вантажимо в Supabase і вставляємо картинкою
            if (newsletterAttachment.value) {
                try {
                    const fileName = `${Date.now()}_${newsletterAttachment.value.name}`;
                    const { error: uploadError } = await supabase.storage.from('galerie_images').upload(`journal/${fileName}`, newsletterAttachment.value);
                    if (uploadError) throw uploadError;

                    const { data: publicUrlData } = supabase.storage.from('galerie_images').getPublicUrl(`journal/${fileName}`);
                    const imageUrl = publicUrlData.publicUrl;

                    // Додаємо фото в кінець листа
                    finalMessageHtml += `<br><br><hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">`;
                    finalMessageHtml += `<div style="text-align: center;">`;
                    finalMessageHtml += `<img src="${imageUrl}" alt="Image jointe" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">`;
                    finalMessageHtml += `</div>`;
                } catch (e) {
                    console.error("Помилка фото:", e);
                    alert("Erreur lors de l'upload de l'image.");
                    isSendingNewsletter.value = false;
                    return;
                }
            }

            // Загортаємо весь лист у красивий контейнер
            const emailTemplate = `
                <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;">
                    ${finalMessageHtml}
                </div>
            `;

            // 3. Збираємо отримувачів
            let targets = [];
            if (newsletterTarget.value === 'all') {
                targets = subscribersList.value.map(s => s.email);
            } else {
                targets = [newsletterTarget.value];
            }

            newsletterProgress.value.total = targets.length;
            newsletterProgress.value.current = 0;

            let successCount = 0;
            let failCount = 0;

            // 4. Відправляємо через Brevo REST API
            for (const email of targets) {
                try {
                    const response = await fetch('https://api.brevo.com/v3/smtp/email', {
                        method: 'POST',
                        headers: {
                            'accept': 'application/json',
                            'api-key': BREVO_API_KEY,
                            'content-type': 'application/json'
                        },
                        body: JSON.stringify({
                            sender: { name: SENDER_NAME, email: SENDER_EMAIL },
                            to: [{ email: email }],
                            subject: newsletterSubject.value,
                            htmlContent: emailTemplate
                        })
                    });

                    if (response.ok) {
                        successCount++;
                    } else {
                        const errData = await response.json();
                        console.error("Brevo API Error:", errData);
                        failCount++;
                    }
                } catch (err) {
                    console.error("Erreur d'envoi à", email, err);
                    failCount++;
                }

                newsletterProgress.value.current++;
                await new Promise(resolve => setTimeout(resolve, 300)); // Затримка 300мс між листами
            }

            isSendingNewsletter.value = false;
            alert(`Campagne terminée !\nSuccès : ${successCount}\nÉchecs : ${failCount}`);
            closeNewsletterModal();
        };

        const exportCSV = () => {
            if (subscribersList.value.length === 0) {
                alert("Aucun abonné à exporter.");
                return;
            }
            let csvContent = "Email;Date d'inscription\n";
            subscribersList.value.forEach(sub => {
                const date = new Date(sub.created_at).toLocaleDateString('fr-FR');
                csvContent += `${sub.email};${date}\n`;
            });
            const bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
            const blob = new Blob([bom, csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", `export_abonnes_newsletter_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        const getTime = (dateStr) => { if (!dateStr) return ''; return new Date(dateStr).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }); };
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

        const editNews = (news) => {
            isEditingNews.value = true;
            editingNewsId.value = news.id;
            let parsedImages = [];
            if (news.images_secondaires) {
                parsedImages = typeof news.images_secondaires === 'string' ? JSON.parse(news.images_secondaires) : news.images_secondaires;
            }
            newNews.value = {
                titre: news.titre,
                categorie: news.categorie ? news.categorie.split(', ') : [],
                image_url: news.image_url,
                description: news.description,
                event_date: news.event_date || '',
                event_end_date: news.event_end_date || '',
                contenu: news.contenu || '',
                images_secondaires: parsedImages
            };
            showNewsModal.value = true;
        };

        const closeNewsModal = () => {
            showNewsModal.value = false;
            isEditingNews.value = false;
            editingNewsId.value = null;
            newNews.value = { titre: '', categorie: ['Actualité'], image_url: '', description: '', contenu: '', images_secondaires: [], event_date: '', event_end_date: '' };
            selectedFile.value = null;
        };

        const getCountByCat = (cat) => allOeuvres.value.filter(o => o.categorie === cat).length;

        const toggleFeatured = async (type, id, currentStatus) => {
            const tableName = type === 'oeuvre' ? 'oeuvres' : 'artistes';
            const { error } = await supabase.from(tableName).update({ is_featured: !currentStatus }).eq('id', id);
            if (!error) {
                if (type === 'oeuvre') { const item = allOeuvres.value.find(o => o.id === id); if (item) item.is_featured = !currentStatus; }
                else { const item = allArtistes.value.find(a => a.id === id); if (item) item.is_featured = !currentStatus; }
            } else { alert("Erreur."); }
        };

        const uploadSecondaryImage = async (event, index) => {
            const file = event.target.files[0];
            if (!file) return;

            // Перевірка розміру (макс 5 МБ)
            if (file.size > 5 * 1024 * 1024) {
                alert("L'image est trop volumineuse (Max 5 Mo).");
                return;
            }

            try {
                // Ставимо заглушку-текст поки вантажиться
                newNews.value.images_secondaires[index] = "Chargement en cours...";

                const fileName = `${Date.now()}_secondary_${file.name}`;

                // Завантажуємо файл у бакет Supabase
                const { error: uploadError } = await supabase.storage
                    .from('galerie_images')
                    .upload(`journal/${fileName}`, file);

                if (uploadError) throw uploadError;

                // Отримуємо публічне посилання на картинку
                const { data: publicUrlData } = supabase.storage
                    .from('galerie_images')
                    .getPublicUrl(`journal/${fileName}`);

                // Записуємо готове посилання в інпут!
                newNews.value.images_secondaires[index] = publicUrlData.publicUrl;

            } catch (e) {
                newNews.value.images_secondaires[index] = ""; // очищаємо при помилці
                alert("Erreur d'upload : " + e.message);
            }
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

                const payload = {
                    titre: newNews.value.titre,
                    categorie: newNews.value.categorie.join(', '),
                    description: newNews.value.description,
                    image_url: finalImageUrl,
                    event_date: isEvent ? newNews.value.event_date : null,
                    event_end_date: isEvent ? newNews.value.event_end_date : null,
                    contenu: newNews.value.contenu,
                    images_secondaires: newNews.value.images_secondaires || []
                };

                if (isEditingNews.value) {
                    const { error } = await supabase.from('journal').update(payload).eq('id', editingNewsId.value);
                    if (error) throw error;
                } else {
                    const { error } = await supabase.from('journal').insert([payload]);
                    if (error) throw error;
                }

                closeNewsModal();
                fetchAllData();
            } catch (e) {
                alert("Erreur: " + e.message);
            } finally {
                isSavingNews.value = false;
            }
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
                let err = null;
                if (deleteModal.value.type === 'oeuvre') {
                    const { error } = await supabase.from('oeuvres').delete().eq('id', deleteModal.value.id);
                    err = error;
                }
                else if (deleteModal.value.type === 'artiste') {
                    await supabase.from('oeuvres').delete().eq('artiste_id', deleteModal.value.id);
                    const { error } = await supabase.from('artistes').delete().eq('id', deleteModal.value.id);
                    err = error;
                }
                else if (deleteModal.value.type === 'atelier') {
                    const { error } = await supabase.from('ateliers').delete().eq('id', deleteModal.value.id);
                    err = error;
                }
                else if (deleteModal.value.type === 'news') {
                    const { error } = await supabase.from('journal').delete().eq('id', deleteModal.value.id);
                    err = error;
                }

                if (err) throw err;

                await fetchAllData();
            } catch (error) {
                console.error(error);
                alert("Erreur de suppression : L'élément est probablement lié à d'autres données (Favoris, Commandes, etc.). Retirez ces liens d'abord.");
            } finally {
                isDeleting.value = false; closeDeleteModal();
            }
        };

        const handleFileUpload = (event) => { const file = event.target.files[0]; if (file) { const maxSize = 5 * 1024 * 1024; if (file.size > maxSize) { alert("Max 5 Mo."); event.target.value = ''; return; } selectedFile.value = file; selectedFileName.value = file.name; } };

        const logout = async () => { await supabase.auth.signOut(); window.location.href = '/galerie/'; };

        const exportToCSV = () => {
            if (allCommandes.value.length === 0) { alert("Aucune donnée à exporter."); return; }
            const headers = ['ID Commande', 'Date', 'Client', 'Email', 'Oeuvre', 'Montant (€)', 'Statut'];
            const rows = allCommandes.value.map(cmd => {
                const date = new Date(cmd.created_at).toLocaleDateString('fr-FR');
                const clientNom = cmd.nom_client || 'Client';
                const clientEmail = cmd.user_email || '';
                const oeuvreTitre = cmd.oeuvres?.titre || 'Inconnu';
                const montant = cmd.montant_total || 0;
                const statut = cmd.status || 'En attente';
                return [cmd.id, date, clientNom, clientEmail, oeuvreTitre, montant, statut];
            });
            const csvContent = [headers.join(';'), ...rows.map(row => row.map(item => `"${String(item).replace(/"/g, '""')}"`).join(';'))].join('\n');
            const bom = new Uint8Array([0xEF, 0xBB, 0xBF]);
            const blob = new Blob([bom, csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", `export_ventes_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        };

        onMounted(() => { checkAdminAccess(); });

        return {
            isAuthenticating, currentTab, stats, allOeuvres, allArtistes, totalRevenue, getCountByCat, toggleFeatured, askDelete, confirmDelete, closeDeleteModal, deleteModal, isDeleting, logout,
            allNews, showNewsModal, newNews, saveNews, isSavingNews, toggleFeaturedNews, formatDate, formatDateShort, selectedFile, handleFileUpload, editNews, closeNewsModal, isEditingNews, editingNewsId, toggleNewsCategory,
            allCommandes, allDemandes, paginatedCommandes, totalPagesCommandes, currentPageCommandes, prevPageCommandes, nextPageCommandes, paginatedDemandes, totalPagesDemandes, currentPageDemandes, prevPageDemandes,
            realRevenue, potentialRevenue, pendingDemandesCount, getStatusClass, updateStatut, pendingArtists, approvedArtists, soldOeuvresCount, approveArtist, exportToCSV,
            showAdminMsgModal, activeMsgArtist, adminMsgText, isSendingMsg, openMessageModal, sendAdminMessage, getDemandesByStatut, dragStart, dropCard, displayClientName,
            openDemandeModal, closeDemandeModal, showDemandeModal, activeDemande, saveDemandeStatus, isSavingDemande, showEmailComposer, toggleEmailComposer, emailSubject, emailBody, sendEmail, isSendingEmail, emailSentSuccess,
            allMessages, unreadMessagesCount, showReplyModal, activeMessage, replyText, isSendingReply, openReplyModal, sendContactReply, allAteliers, showAtelierModal, isEditingAtelier, isSavingAtelier,
            atelierForm, openAtelierModal, closeAtelierModal, editAtelier, saveAtelier, handleAtelierFile, getTime,
            showParticipantsModal, currentAtelierParticipants, currentAtelierName, isLoadingParticipants, viewParticipants, ateliersOnly, evenementsOnly, openEvenementModal,
            subscribersList, exportCSV, showNewsletterModal, newsletterTarget, newsletterSubject, newsletterBody,
            isSendingNewsletter, newsletterProgress, openNewsletterComposer, uploadSecondaryImage, paginatedOeuvres, totalPagesOeuvres, currentPageOeuvres, prevPageOeuvres, nextPageOeuvres,
            closeNewsletterModal, sendNewsletter, newsletterAttachmentName, handleNewsletterAttachment
        };
    }
}).mount('#admin-dashboard');