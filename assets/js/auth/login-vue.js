const { createApp, ref, reactive } = Vue;

const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

createApp({
    setup() {
        const credentials = reactive({
            email: '',
            password: ''
        });

        const loading = ref(false);
        const message = reactive({ text: '', type: '' });

        const handleLogin = async () => {
            loading.value = true;
            message.text = '';

            const { data, error } = await supabase.auth.signInWithPassword({
                email: credentials.email,
                password: credentials.password,
            });

            if (error) {
                message.text = "Identifiants incorrects. Veuillez réessayer.";
                message.type = 'error';
            } else {
                // Кидаємо на красиві роути, а не на файли .php
                if (data.user && data.user.email === 'admin@gmail.com') {
                    window.location.href = '/dashboard';
                } else {
                    window.location.href = '/mon-compte';
                }
            }
            loading.value = false;
        };

        return { credentials, loading, message, handleLogin };
    }
}).mount('#login-app');