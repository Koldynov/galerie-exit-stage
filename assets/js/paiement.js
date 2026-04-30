const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const oeuvre = ref(null);
        const isLoading = ref(true);
        const isProcessing = ref(false);
        const isSuccess = ref(false);
        const currentUser = ref(null);

        const form = ref({ nom: '', email: '', adresse: '', code_postal: '', ville: '', pays: 'France', numero: '', expiration: '', cvc: '' });
        const errors = ref({ nom: '', email: '', adresse: '', code_postal: '', ville: '', numero: '', expiration: '', cvc: '' });

        const addressSuggestions = ref([]);
        let timeoutId = null;

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                currentUser.value = session.user;
                form.value.email = session.user.email;
            }
            const id = window.CURRENT_ITEM_ID;
            if (id) {
                // ДОДАНО ОБРОБКУ ПОМИЛОК!
                const { data, error } = await supabase.from('oeuvres').select('*, artistes(nom, email)').eq('id', id).single();

                if (error) {
                    console.error("❌ Помилка Supabase:", error.message);
                    alert("Увага: Помилка завантаження. Перевірте, чи додали ви колонку 'email' у таблицю artistes!");
                    // Якщо сталася помилка з email, робимо резервний запит БЕЗ email-у, щоб сайт не падав:
                    const fallback = await supabase.from('oeuvres').select('*, artistes(nom)').eq('id', id).single();
                    if (fallback.data) oeuvre.value = fallback.data;
                } else if (data) {
                    oeuvre.value = data;
                }
            }
            isLoading.value = false;
        });

        const clearError = (field) => errors.value[field] = '';
        const formatCardNumber = (e) => { clearError('numero'); let v = e.target.value.replace(/\D/g, '').substring(0, 16); form.value.numero = v.match(/.{1,4}/g)?.join(' ') || v; };
        const formatExpiry = (e) => { clearError('expiration'); let v = e.target.value.replace(/\D/g, '').substring(0, 4); if (v.length >= 2) { let m = parseInt(v.substring(0, 2)); if (m > 12) v = '12' + v.substring(2); if (m === 0) v = '01' + v.substring(2); if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2); else if (e.inputType !== 'deleteContentBackward') v += '/'; } form.value.expiration = v; };
        const formatCvc = (e) => { clearError('cvc'); form.value.cvc = e.target.value.replace(/\D/g, '').substring(0, 3); };

        const searchAddress = async () => {
            clearError('adresse');
            if (form.value.pays !== 'France') return;
            if (form.value.adresse.length < 5) {
                addressSuggestions.value = [];
                return;
            }

            clearTimeout(timeoutId);
            timeoutId = setTimeout(async () => {
                try {
                    const res = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(form.value.adresse)}&limit=5`);
                    const data = await res.json();
                    addressSuggestions.value = data.features;
                } catch (e) { console.error("Помилка API Adresse", e); }
            }, 300);
        };

        const selectAddress = (feature) => {
            form.value.adresse = feature.properties.name;
            form.value.code_postal = feature.properties.postcode;
            form.value.ville = feature.properties.city;
            addressSuggestions.value = [];
            clearError('code_postal'); clearError('ville');
        };

        const validateForm = () => {
            let i = true;
            errors.value = { nom: '', email: '', adresse: '', code_postal: '', ville: '', numero: '', expiration: '', cvc: '' };

            if (!form.value.nom || form.value.nom.length < 3) { errors.value.nom = "Nom requis."; i = false; }
            if (!form.value.email || !/^\S+@\S+\.\S+$/.test(form.value.email)) { errors.value.email = "Email invalide."; i = false; }

            if (!form.value.adresse) { errors.value.adresse = "Adresse requise."; i = false; }
            if (!form.value.code_postal) { errors.value.code_postal = "Code postal requis."; i = false; }
            if (!form.value.ville) { errors.value.ville = "Ville requise."; i = false; }

            if (form.value.numero.replace(/\s/g, '').length !== 16) { errors.value.numero = "16 chiffres requis."; i = false; }
            if (form.value.expiration.length !== 5) { errors.value.expiration = "Format MM/YY."; i = false; }
            if (form.value.cvc.length !== 3) { errors.value.cvc = "3 chiffres requis."; i = false; }

            return i;
        };

        const processPayment = async () => {
            if (!validateForm()) return;
            isProcessing.value = true;

            try {
                await new Promise(r => setTimeout(r, 2000));

                const adresseComplete = `${form.value.adresse}, ${form.value.code_postal} ${form.value.ville}, ${form.value.pays}`;

                const { error: cErr } = await supabase.from('commandes').insert([{
                    oeuvre_id: oeuvre.value.id,
                    nom_client: form.value.nom,
                    user_email: form.value.email,
                    adresse_livraison: adresseComplete,
                    ville: form.value.ville,
                    code_postal: form.value.code_postal,
                    pays: form.value.pays,
                    montant_total: oeuvre.value.prix,
                    status: 'en_attente',
                    user_id: currentUser.value ? currentUser.value.id : null
                }]);
                if (cErr) throw cErr;

                await supabase.from('oeuvres').update({ statut: 'vendu' }).eq('id', oeuvre.value.id);

                try {
                    await fetch('/galerie/api/notify-achat', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            nom: form.value.nom,
                            email: form.value.email,
                            oeuvre_titre: oeuvre.value.titre,
                            montant: oeuvre.value.prix,
                            artist_name: oeuvre.value.artistes?.nom || 'Artiste',
                            artist_email: oeuvre.value.artistes?.email || null
                        })
                    });
                } catch (e) { console.error("Erreur Brevo:", e); }

                isProcessing.value = false;
                isSuccess.value = true;

            } catch (e) {
                alert("Erreur: " + e.message);
                isProcessing.value = false;
            }
        };

        return {
            oeuvre, isLoading, form, errors, clearError, formatCardNumber, formatExpiry, formatCvc,
            isProcessing, isSuccess, processPayment,
            addressSuggestions, searchAddress, selectAddress
        };
    }
}).mount('#paiement-app');