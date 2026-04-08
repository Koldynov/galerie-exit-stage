const { createApp, ref, onMounted } = Vue;

const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';

const supabaseClient = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

createApp({
    setup() {
        const loading = ref(true);
        const userProfile = ref(null);

        onMounted(async () => {
            const { data: { session } } = await supabaseClient.auth.getSession();

            if (!session) {
                console.log("Сесія не знайдена, йдемо на логін");
                window.location.href = 'login.php';
                return;
            }

            const { data: profile } = await supabaseClient
                .from('profiles')
                .select('*')
                .eq('id', session.user.id)
                .single();

            userProfile.value = profile;
            loading.value = false;
        });

        const handleLogout = async () => {
            await supabaseClient.auth.signOut();
            window.location.href = 'login.php';
        };

        return { loading, userProfile, handleLogout };
    },
    render() {
        if (this.loading) {
            return Vue.h('div', { class: 'loading-state' }, 'Chargement...');
        }
        if (!this.userProfile) return Vue.h('div', 'Profil introuvable');

        return Vue.h('div', { class: 'profile-card' }, [
            Vue.h('h2', 'Mon Compte'),
            Vue.h('div', { class: 'profile-info' }, [
                Vue.h('p', `Prénom: ${this.userProfile.first_name}`),
                Vue.h('p', `Nom: ${this.userProfile.last_name}`),
                Vue.h('p', `Téléphone: ${this.userProfile.phone}`)
            ]),
            Vue.h('button', { class: 'btn-primary', onClick: this.handleLogout }, 'Se déconnecter')
        ]);
    }
}).mount('#account-app');