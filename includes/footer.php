<footer class="main-footer">
        <div class="footer-top">
            <div class="footer-brand">
                <h2 class="footer-logo">EXIT.</h2>
                <p>2 Rue des Bains<br>73100 AIX LES BAINS, France</p>
                <a href="mailto:exit.heartconcept@gmail.com">exit.heartconcept@gmail.com</a>
            </div>

            <div class="footer-links">
                <div class="link-group">
                    <h4>Navigation</h4>
                    <a href="index.php">Accueil</a>
                    <a href="#artistes">Artistes</a>
                    <a href="#heart-concept">Heart Concept</a>
                    <a href="#contact">Contact</a>
                </div>
                <div class="link-group">
                    <h4>Légal</h4>
                    <a href="mentions-legales.php">Mentions Légales</a>
                    <a href="politique-cookies.php">Politique de Cookies</a>
                    <a href="cgv.php">CGU & CGV</a>
                </div>
            </div>

            <div class="footer-action">
                <a href="portail-artiste.php" id="footer-artist-link" class="big-action-link">
                    <span class="text-lines">
                        ESPACE<br>ARTISTES.
                    </span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="social-links-footer">
                <span class="suivre-text">Suivre</span>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <p>&copy; 2026 La Galerie EXIT. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="assets/js/particles.js"></script>
    <script src="assets/js/carousel.js"></script>
    <script>
(async () => {
    const SUPABASE_URL = 'https://lnuadhnqrvfwzbndylod.supabase.co';
    const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxudWFkaG5xcnZmd3pibmR5bG9kIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzU1ODcxOTUsImV4cCI6MjA5MTE2MzE5NX0.VXPW6cRPSuLWQeCj3ZSOxjEPMfVuwop1sV7fGv7eLQw';
    
    const supabaseClient = window.supabase ? window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY) : null;
    
    if (supabaseClient) {
        const { data: { session } } = await supabaseClient.auth.getSession();
        
        if (session) {
            const artistLink = document.getElementById('footer-artist-link');
            if (!artistLink) return;

            // 1. ЛОГІКА ДЛЯ АДМІНІСТРАТОРА
            if (session.user.email === 'admin@gmail.com') {
                artistLink.href = 'dashboard.php'; // Посилання на адмінку
                artistLink.innerHTML = `
                    <span class="text-lines">
                        ESPACE<br>ADMIN
                    </span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                `;
                return; // Виходимо, щоб не спрацювала логіка для артистів
            }

            // 2. ЛОГІКА ДЛЯ АРТИСТА
            const { data: artistData } = await supabaseClient
                .from('artistes')
                .select('id')
                .eq('auth_user_id', session.user.id)
                .single();

            if (artistData) {
                artistLink.href = 'espace-artiste.php'; // Посилання на студіо артиста
                artistLink.innerHTML = `
                    <span class="text-lines">
                        MON<br>STUDIO.
                    </span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                `;
            }
        }
    }
})();
</script>

</body>
</html>