const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const loading = ref(true);
        const userProfile = ref(null);
        const currentUser = ref(null);
        
        const isEditing = ref(false);
        const isSaving = ref(false);
        const editForm = ref({ first_name: '', last_name: '', phone: '' });
        
        const successMsg = ref('');
        const errorMsg = ref('');

        const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

        const fetchProfile = async (userId) => {
            const { data, error } = await supabase
                .from('profiles')
                .select('*')
                .eq('id', userId)
                .single();

            if (data) {
                userProfile.value = data;
            } else if (error) {
                userProfile.value = { first_name: '', last_name: '', phone: '' };
            }
        };

        const startEdit = () => {
            editForm.value = { 
                first_name: userProfile.value.first_name || '', 
                last_name: userProfile.value.last_name || '', 
                phone: userProfile.value.phone || '' 
            };
            successMsg.value = '';
            errorMsg.value = '';
            isEditing.value = true;
        };

        const cancelEdit = () => {
            isEditing.value = false;
            successMsg.value = '';
            errorMsg.value = '';
        };

        const saveProfile = async () => {
            isSaving.value = true;
            successMsg.value = '';
            errorMsg.value = '';

            const { data, error } = await supabase
                .from('profiles')
                .upsert({ 
                    id: currentUser.value.id,
                    first_name: editForm.value.first_name,
                    last_name: editForm.value.last_name,
                    phone: editForm.value.phone,
                    updated_at: new Date()
                });

            isSaving.value = false;

            if (error) {
                errorMsg.value = "Erreur lors de la mise à jour: " + error.message;
            } else {
                userProfile.value = { ...userProfile.value, ...editForm.value };
                successMsg.value = "Profil mis à jour avec succès !";
                setTimeout(() => {
                    isEditing.value = false;
                }, 1500);
            }
        };

        const handleLogout = async () => {
            await supabase.auth.signOut();
            window.location.href = 'index.php';
        };

        onMounted(async () => {
            const { data: { session } } = await supabase.auth.getSession();
            if (session) {
                currentUser.value = session.user;

                // ==========================================
                // РОЗУМНИЙ РЕДИРЕКТ: Перевіряємо, чи це артист
                // ==========================================
                const { data: artistData } = await supabase
                    .from('artistes')
                    .select('id')
                    .eq('auth_user_id', session.user.id)
                    .single();

                if (artistData) {
                    // Якщо юзер знайдений в таблиці артистів — миттєво кидаємо його в студію
                    window.location.href = 'espace-artiste.php';
                    return; // Зупиняємо виконання коду, щоб не вантажити звичайний профіль
                }

                // Якщо це звичайний клієнт — вантажимо його дані
                await fetchProfile(session.user.id);
            }
            // Вимикаємо лоадер тільки якщо це не артист (інакше лоадер крутиться до самого редиректу)
            loading.value = false;
        });

        return { 
            loading, userProfile, isEditing, isSaving, editForm, 
            successMsg, errorMsg, startEdit, cancelEdit, saveProfile, handleLogout 
        };
    }
}).mount('#account-app');