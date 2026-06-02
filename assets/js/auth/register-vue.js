const { createApp, ref, reactive } = Vue;

const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

createApp({
    setup() {
        const user = reactive({
            firstName: '',
            lastName: '',
            phone: '',
            email: '',
            password: ''
        });

        const loading = ref(false);
        const message = reactive({ text: '', type: '' });

        const handleRegister = async () => {
            loading.value = true;
            message.text = '';

            const { data, error } = await supabase.auth.signUp({
                email: user.email,
                password: user.password,
                options: {
                    data: {
                        first_name: user.firstName,
                        last_name: user.lastName,
                        phone: user.phone
                    }
                }
            });

            if (error) {
                message.text = "Erreur: " + error.message;
                message.type = 'error';
            } else {
                message.text = "Succès ! Vérifiez votre email pour confirmer l'inscription.";
                message.type = 'success';
                Object.keys(user).forEach(key => user[key] = '');
            }
            loading.value = false;
        };

        return { user, loading, message, handleRegister };
    }
}).mount('#register-app');