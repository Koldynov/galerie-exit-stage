const { createApp, ref } = Vue;
createApp({
    setup() {
        const isLoginMode = ref(true); const isLoading = ref(false); const errorMsg = ref(''); const successMsg = ref('');
        const loginForm = ref({ email: '', password: '' }); const registerForm = ref({ nom: '', email: '', password: '', biographie: '' });
        
        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);
        
        const handleLogin = async () => { 
            errorMsg.value = ''; if (!loginForm.value.email || !loginForm.value.password) { errorMsg.value = "Remplissez les champs."; return; } 
            isLoading.value = true; 
            try { 
                const { data, error } = await supabase.auth.signInWithPassword({ email: loginForm.value.email, password: loginForm.value.password }); 
                if (error) throw error; 
                
                // Перевірка чи є адміном
                if (loginForm.value.email === 'admin@gmail.com') {
                    window.location.href = '/galerie/dashboard';
                    return;
                }

                const { data: a } = await supabase.from('artistes').select('id').eq('auth_user_id', data.user.id).single(); 
                if (!a) { 
                    // Якщо не артист і не адмін - значить клієнт
                    window.location.href = '/galerie/mon-compte'; 
                    return;
                } 
                
                window.location.href = '/galerie/studio'; // ОНОВЛЕНО
            } catch (e) { errorMsg.value = e.message; } finally { isLoading.value = false; } 
        };
        
        const handleRegister = async () => { 
            errorMsg.value = ''; if (!registerForm.value.nom || !registerForm.value.email || !registerForm.value.password) { errorMsg.value = "Champs obligatoires manquants."; return; } 
            isLoading.value = true; 
            try { 
                const { data: a, error: e } = await supabase.auth.signUp({ email: registerForm.value.email, password: registerForm.value.password }); 
                if (e) throw e; 
                if (a.user) { await supabase.from('artistes').insert([{ auth_user_id: a.user.id, nom: registerForm.value.nom, biographie: registerForm.value.biographie }]); } 
                successMsg.value = "Candidature envoyée !"; 
                setTimeout(() => window.location.href = '/galerie/studio', 2000); // ОНОВЛЕНО
            } catch (e) { errorMsg.value = e.message; } finally { isLoading.value = false; } 
        };
        
        return { isLoginMode, isLoading, errorMsg, successMsg, loginForm, registerForm, handleLogin, handleRegister };
    }
}).mount('#portail-artiste-app');