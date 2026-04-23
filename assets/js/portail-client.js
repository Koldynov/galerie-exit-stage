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
            if (error) alert("Email ou mot de passe incorrect."); // Можна замінити на красивий тост
            else window.location.href = 'mon-compte';
            isLoading.value = false;
        };

        const handleRegister = async () => {
            isLoading.value = true; 
            errorMessage.value = '';
            const { data, error } = await supabase.auth.signUp({
                email: regForm.value.email, 
                password: regForm.value.password,
                options: { data: { first_name: regForm.value.first_name, last_name: regForm.value.last_name } }
            });
            if (error) { alert(error.message); isLoading.value = false; return; }
            
            const { error: pErr } = await supabase.from('profiles').insert([{
                id: data.user.id, 
                first_name: regForm.value.first_name, 
                last_name: regForm.value.last_name, 
                phone: regForm.value.phone
            }]);
            
            if (pErr) alert(pErr.message);
            else window.location.href = 'mon-compte';
            isLoading.value = false;
        };

        return { currentTab, loginForm, regForm, handleLogin, handleRegister, isLoading, errorMessage };
    }
}).mount('#client-portal');