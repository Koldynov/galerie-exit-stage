const { createApp, ref } = Vue;

createApp({
    setup() {
        const currentTab = ref('login');
        const isLoading = ref(false);
        const errorMessage = ref('');
        const successMessage = ref('');
        const imageFile = ref(null);

        const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

        const loginForm = ref({ email: '', password: '' });
        const regForm = ref({
            nom: '',
            titre_pro: '',
            biographie: '',
            citation: '',
            website: '',
            instagram: '',
            email: '',
            password: ''
        });

        const handleFileUpload = (event) => {
            imageFile.value = event.target.files[0];
        };

        const handleLogin = async () => {
            isLoading.value = true;
            errorMessage.value = '';

            const { error } = await supabase.auth.signInWithPassword(loginForm.value);

            if (error) {
                errorMessage.value = "Email ou mot de passe incorrect.";
            } else {
                window.location.href = 'studio';
            }
            isLoading.value = false;
        };

        const handleApply = async () => {
            isLoading.value = true;
            errorMessage.value = '';
            successMessage.value = '';

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

            try {
                let uploadedImageUrl = null;
                if (imageFile.value) {
                    const fileExt = imageFile.value.name.split('.').pop();
                    const fileName = `${Date.now()}_${Math.random().toString(36).substring(7)}.${fileExt}`;

                    const { error: uploadError } = await supabase.storage.from('avatars').upload(fileName, imageFile.value);
                    if (uploadError) throw new Error("Erreur lors du téléchargement de l'image.");

                    const { data } = supabase.storage.from('avatars').getPublicUrl(fileName);
                    uploadedImageUrl = data.publicUrl;
                }

                const { data: authData, error: authError } = await supabase.auth.signUp({
                    email: regForm.value.email,
                    password: regForm.value.password
                });
                if (authError) throw authError;

                const { error: artError } = await supabase.from('artistes').insert([{
                    nom: regForm.value.nom,
                    titre_pro: regForm.value.titre_pro,
                    biographie: regForm.value.biographie,
                    citation: regForm.value.citation,
                    website: regForm.value.website,
                    instagram: regForm.value.instagram,
                    image_url: uploadedImageUrl,
                    auth_user_id: authData.user.id,
                    is_approved: false
                }]);
                if (artError) throw artError;

                successMessage.value = "Félicitations ! Votre candidature a été envoyée avec succès. Nous l'examinerons prochainement.";
                regForm.value = { nom: '', titre_pro: '', biographie: '', citation: '', website: '', instagram: '', email: '', password: '' };
                imageFile.value = null;

            } catch (err) {
                errorMessage.value = err.message;
            }
            isLoading.value = false;
        };

        return { currentTab, loginForm, regForm, handleFileUpload, handleLogin, handleApply, isLoading, errorMessage, successMessage };
    }
}).mount('#artiste-portal');