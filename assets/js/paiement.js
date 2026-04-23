const { createApp, ref, onMounted } = Vue;
createApp({
    setup() {
        const oeuvre = ref(null); const isLoading = ref(true); const isProcessing = ref(false); const isSuccess = ref(false); const currentUser = ref(null);
        const form = ref({ nom: '', email: '', numero: '', expiration: '', cvc: '' }); const errors = ref({ nom: '', email: '', numero: '', expiration: '', cvc: '' });
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        
        onMounted(async () => { const { data: { session } } = await supabase.auth.getSession(); if (session) { currentUser.value = session.user; form.value.email = session.user.email; } const id = window.CURRENT_ITEM_ID; if (id) { const { data } = await supabase.from('oeuvres').select('*, artistes(nom)').eq('id', id).single(); if (data) oeuvre.value = data; } isLoading.value = false; });
        
        const clearError = (field) => errors.value[field] = '';
        const formatCardNumber = (e) => { clearError('numero'); let v = e.target.value.replace(/\D/g, '').substring(0, 16); form.value.numero = v.match(/.{1,4}/g)?.join(' ') || v; };
        const formatExpiry = (e) => { clearError('expiration'); let v = e.target.value.replace(/\D/g, '').substring(0, 4); if (v.length >= 2) { let m = parseInt(v.substring(0, 2)); if (m > 12) v = '12' + v.substring(2); if (m === 0) v = '01' + v.substring(2); if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2); else if (e.inputType !== 'deleteContentBackward') v += '/'; } form.value.expiration = v; };
        const formatCvc = (e) => { clearError('cvc'); form.value.cvc = e.target.value.replace(/\D/g, '').substring(0, 3); };
        
        const validateForm = () => { let i = true; errors.value = { nom: '', email: '', numero: '', expiration: '', cvc: '' }; if (!form.value.nom || form.value.nom.length < 3) { errors.value.nom = "Nom requis."; i = false; } if (!form.value.email || !/^\S+@\S+\.\S+$/.test(form.value.email)) { errors.value.email = "Email invalide."; i = false; } if (form.value.numero.replace(/\s/g, '').length !== 16) { errors.value.numero = "16 chiffres requis."; i = false; } if (form.value.expiration.length !== 5) { errors.value.expiration = "Format MM/YY."; i = false; } if (form.value.cvc.length !== 3) { errors.value.cvc = "3 chiffres requis."; i = false; } return i; };
        
        const processPayment = async () => { if (!validateForm()) return; isProcessing.value = true; try { await new Promise(r => setTimeout(r, 2000)); const { error: cErr } = await supabase.from('commandes').insert([{ oeuvre_id: oeuvre.value.id, nom_client: form.value.nom, user_email: form.value.email, montant_total: oeuvre.value.prix, status: 'Payé', user_id: currentUser.value ? currentUser.value.id : null }]); if (cErr) throw cErr; await supabase.from('oeuvres').update({ statut: 'vendu' }).eq('id', oeuvre.value.id); try { await emailjs.send('service_5jqvnbe', 'template_ajjg9f3', { oeuvre_titre: oeuvre.value.titre, montant: oeuvre.value.prix, nom_client: form.value.nom, email_client: form.value.email }, '4YhmmUwZrYVZg7zTc'); } catch (e) {} isProcessing.value = false; isSuccess.value = true; } catch (e) { alert("Erreur: " + e.message); isProcessing.value = false; } };
        
        return { oeuvre, isLoading, form, errors, clearError, formatCardNumber, formatExpiry, formatCvc, isProcessing, isSuccess, processPayment };
    }
}).mount('#paiement-app');