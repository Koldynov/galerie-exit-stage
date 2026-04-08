const { createApp, ref, reactive } = Vue;

const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';

const supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

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

            const { data, error } = await supabaseClient.auth.signUp({
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