// assets/js/portail-client.js
const { createApp, ref } = Vue;

createApp({
    setup() {
        const currentTab = ref('login');
        const isLoading = ref(false);
        const errorMessage = ref('');
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const loginForm = ref({ email: '', password: '' });
        const regForm = ref({ first_name: '', last_name: '', phone: '', email: '', password: '' });

        const handleLogin = async () => {
            isLoading.value = true;
            errorMessage.value = '';
            const { error } = await supabase.auth.signInWithPassword(loginForm.value);

            if (error) {
                errorMessage.value = "Email ou mot de passe incorrect.";
            } else {
                window.location.href = 'mon-compte';
            }
            isLoading.value = false;
        };

        const handleRegister = async () => {
            isLoading.value = true;
            errorMessage.value = '';

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(regForm.value.email)) {
                errorMessage.value = "Veuillez entrer une adresse email valide avec un '@'.";
                isLoading.value = false;
                return;
            }

            if (regForm.value.password.length < 8 || !/[A-Z]/.test(regForm.value.password)) {
                errorMessage.value = "Le mot de passe doit contenir au moins 8 caractères et une majuscule.";
                isLoading.value = false;
                return;
            }

            const { data, error } = await supabase.auth.signUp({
                email: regForm.value.email,
                password: regForm.value.password,
                options: { data: { first_name: regForm.value.first_name, last_name: regForm.value.last_name } }
            });

            if (error) {
                errorMessage.value = error.message;
                isLoading.value = false;
                return;
            }

            const { error: pErr } = await supabase.from('profiles').insert([{
                id: data.user.id,
                first_name: regForm.value.first_name,
                last_name: regForm.value.last_name,
                phone: regForm.value.phone
            }]);

            if (pErr) {
                errorMessage.value = pErr.message;
            } else {
                window.location.href = 'mon-compte';
            }
            isLoading.value = false;
        };

        return { currentTab, loginForm, regForm, handleLogin, handleRegister, isLoading, errorMessage };
    }
}).mount('#client-portal');