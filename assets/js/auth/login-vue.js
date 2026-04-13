const { createApp, ref, reactive } = Vue;

const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';

const supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

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

            const { data, error } = await supabaseClient.auth.signInWithPassword({
                email: credentials.email,
                password: credentials.password,
            });

            if (error) {
                message.text = "Identifiants incorrects. Veuillez réessayer.";
                message.type = 'error';
            } else {
                // ПЕРЕВІРКА НА АДМІНА: Кидаємо на дашборд або у звичайний кабінет
                if (data.user && data.user.email === 'admin@gmail.com') {
                    window.location.href = 'dashboard.php';
                } else {
                    window.location.href = 'mon-compte.php';
                }
            }
            loading.value = false;
        };

        return { credentials, loading, message, handleLogin };
    }
}).mount('#login-app');