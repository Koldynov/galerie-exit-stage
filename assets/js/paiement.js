// ДОДАНО nextTick
const { createApp, ref, onMounted, nextTick } = Vue;

createApp({
    setup() {
        const oeuvre = ref(null);
        const isLoading = ref(true);
        const isProcessing = ref(false);
        const isSuccess = ref(false);
        const currentUser = ref(null);

        const form = ref({ nom: '', email: '', adresse: '', code_postal: '', ville: '', pays: 'France' });
        const errors = ref({ nom: '', email: '', adresse: '', code_postal: '', ville: '' });

        const addressSuggestions = ref([]);
        let timeoutId = null;

        // Змінні для Stripe
        let stripe = null;
        let elements = null;

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const initStripe = async (prix) => {
            try {
                // Запитуємо у нашого бекенду client_secret
                const res = await fetch('/api/create-payment-intent', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ montant: prix })
                });

                if (!res.ok) {
                    const err = await res.json();
                    console.error("Помилка сервера:", err);
                    return;
                }

                const { clientSecret, paymentIntentId, publishableKey } = await res.json();
                if (!publishableKey) return;

                form.value.pi_id = paymentIntentId;

                stripe = window.Stripe(publishableKey);

                // Налаштовуємо темну тему для форми
                // Налаштовуємо темну тему для форми
                // Кастомна темна тема, що копіює дизайн твого сайту
                const appearance = {
                    theme: 'night',
                    variables: {
                        colorPrimary: '#d4af37',       // Золотий акцент
                        colorBackground: '#111111',    // Фон як у твоєї форми
                        colorText: '#ffffff',
                        colorDanger: '#ff4d4d',
                        fontFamily: 'Helvetica Neue, Helvetica, Arial, sans-serif',
                        borderRadius: '4px',
                    },
                    rules: {
                        '.Input': {
                            backgroundColor: 'rgba(255,255,255,0.05)',
                            border: '1px solid rgba(255,255,255,0.1)',
                            boxShadow: 'none',
                        },
                        '.Input:focus': {
                            border: '1px solid #d4af37',
                        }
                    }
                };

                // Ініціалізуємо елементи з ПРИМУСОВОЮ французькою мовою
                elements = stripe.elements({
                    appearance,
                    clientSecret,
                    locale: 'fr' // Більше ніяких "Карта" чи "ММ/ГГ"
                });

                // Створюємо елемент оплати і монтуємо його в DOM
                // Створюємо елемент оплати і кажемо Stripe не збирати адресу
                const paymentElement = elements.create("payment", {
                    fields: {
                        billingDetails: 'never' // Це приховає поле "Pays" та інші поля адреси у блоці Stripe
                    }
                });
                paymentElement.mount("#payment-element");

            } catch (error) {
                console.error("Помилка ініціалізації Stripe:", error);
            }
        };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                currentUser.value = session.user;
                form.value.email = session.user.email;
            }
            const id = window.CURRENT_ITEM_ID;
            if (id) {
                const { data, error } = await supabase.from('oeuvres').select('*, artistes(nom, email)').eq('id', id).single();

                if (data) {
                    oeuvre.value = data;
                    isLoading.value = false; // Спочатку вимикаємо загрузку
                    await nextTick(); // ЧЕКАЄМО, ПОКИ VUE НАМАЛЮЄ DIV ДЛЯ СТРАЙПУ
                    await initStripe(data.prix); // Тепер запускаємо Stripe!
                } else {
                    const fallback = await supabase.from('oeuvres').select('*, artistes(nom)').eq('id', id).single();
                    if (fallback.data) {
                        oeuvre.value = fallback.data;
                        isLoading.value = false;
                        await nextTick();
                        await initStripe(fallback.data.prix);
                    } else {
                        isLoading.value = false;
                    }
                }
            } else {
                isLoading.value = false;
            }
        });

        const clearError = (field) => errors.value[field] = '';

        const searchAddress = async () => {
            clearError('adresse');
            if (form.value.pays !== 'France') return;
            if (form.value.adresse.length < 5) { addressSuggestions.value = []; return; }
            clearTimeout(timeoutId);
            timeoutId = setTimeout(async () => {
                try {
                    const res = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(form.value.adresse)}&limit=5`);
                    const data = await res.json();
                    addressSuggestions.value = data.features;
                } catch (e) { }
            }, 300);
        };

        const selectAddress = (feature) => {
            form.value.adresse = feature.properties.name; form.value.code_postal = feature.properties.postcode; form.value.ville = feature.properties.city;
            addressSuggestions.value = []; clearError('code_postal'); clearError('ville');
        };

        const validateForm = () => {
            let i = true;
            errors.value = { nom: '', email: '', adresse: '', code_postal: '', ville: '' };
            if (!form.value.nom || form.value.nom.length < 3) { errors.value.nom = "Nom requis."; i = false; }
            if (!form.value.email || !/^\S+@\S+\.\S+$/.test(form.value.email)) { errors.value.email = "Email invalide."; i = false; }
            if (!form.value.adresse) { errors.value.adresse = "Adresse requise."; i = false; }
            if (!form.value.code_postal) { errors.value.code_postal = "Code postal requis."; i = false; }
            if (!form.value.ville) { errors.value.ville = "Ville requise."; i = false; }
            return i;
        };

        const processPayment = async () => {
            if (!validateForm()) return;
            if (!stripe || !elements) return;

            isProcessing.value = true;
            const messageContainer = document.querySelector('#payment-message');
            messageContainer.style.display = 'none';

            try {
                // 1. СТВОРЮЄМО НЕОПЛАЧЕНЕ ЗАМОВЛЕННЯ (КОШИК) ПЕРЕД ОПЛАТОЮ
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
                    status: 'en_attente_de_paiement', // Новий статус: чекаємо гроші
                    user_id: currentUser.value ? currentUser.value.id : null,
                    stripe_pi_id: form.value.pi_id // Прив'язуємо замовлення до платежу Stripe
                }]);

                if (cErr) throw cErr;

                // Визначаємо код країни для Stripe
                let countryCode = 'FR';
                if (form.value.pays === 'Belgique') countryCode = 'BE';
                else if (form.value.pays === 'Suisse') countryCode = 'CH';
                else if (form.value.pays === 'Canada') countryCode = 'CA';


                const { error: stripeError } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        receipt_email: form.value.email,
                        payment_method_data: {
                            billing_details: {
                                name: form.value.nom,
                                email: form.value.email,
                                phone: '', // Stripe хоче телефон
                                address: {
                                    line1: form.value.adresse,
                                    line2: '', // Stripe хоче другу лінію адреси
                                    city: form.value.ville,
                                    state: '', // САМЕ ТЕ, ЩО ВІН ПРОСИТЬ ЗАРАЗ (Штат/Регіон)
                                    postal_code: form.value.code_postal,
                                    country: countryCode
                                }
                            }
                        }
                    },
                    redirect: 'if_required'
                });

                // 3. ЯКЩО КАРТКА ВІДХИЛЕНА АБО ПОМИЛКА
                if (stripeError) {
                    if (stripeError.type === "card_error" || stripeError.type === "validation_error") {
                        messageContainer.textContent = stripeError.message;
                    } else {
                        messageContainer.textContent = "Une erreur inattendue s'est produite.";
                    }
                    messageContainer.style.display = 'block';
                    isProcessing.value = false;
                    return;
                }

                // 4. ЯКЩО ПЛАТІЖ УСПІШНИЙ!
                // JS більше не пише статус 'vendu' в базу і не відправляє листи.
                // Це тепер обов'язок нашого сервера (через Webhook), бо так безпечніше!
                isProcessing.value = false;
                isSuccess.value = true;

            } catch (e) {
                alert("Erreur: " + e.message);
                isProcessing.value = false;
            }
        };

        return {
            oeuvre, isLoading, form, errors, clearError, isProcessing, isSuccess, processPayment, addressSuggestions, searchAddress, selectAddress
        };
    }
}).mount('#paiement-app');