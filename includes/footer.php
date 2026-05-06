<footer class="main-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <h2 class="footer-logo">EXIT.</h2>
            <p>2 Rue des Bains<br>73100 AIX LES BAINS, France</p>
            <a href="mailto:exit.heartconcept@gmail.com">exit.heartconcept@gmail.com</a>
        </div>

        <div class="footer-links">
            <!-- Колонка 1: Навігація -->
            <div class="link-group">
                <h4>Navigation</h4>
                <a href="./">Accueil</a>
                <a href="peintures">Peintures</a>
                <a href="sculptures">Sculptures</a>
                <a href="artistes">Artistes</a>
                <a href="journal">Le Journal</a>
            </div>
            
            <!-- Колонка 2: Допомога та Сервіс -->
            <div class="link-group">
                <h4>Service Client</h4>
                <a href="faq">F.A.Q / Aide</a>
                <a href="livraison-retours">Livraison & Retours</a>
                <a href="contact">Contactez-nous</a>
            </div>

            <!-- Колонка 3: Юридична інформація (RGPD) -->
            <div class="link-group">
                <h4>Légal & RGPD</h4>
                <a href="mentions-legales">Mentions Légales</a>
                <a href="cgv">CGV & CGU</a>
                <a href="confidentialite">Politique de Confidentialité</a>
                <a href="cookies">Gestion des Cookies</a>
            </div>
        </div>

        <div class="footer-action">
            <a href="portail-artiste" id="footer-artist-link" class="big-action-link">
                <span class="text-lines">ESPACE<br>ARTISTES.</span>
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
<script>
(async () => {
    // Використовуємо конфіг, який вже є в хедері
    const supabaseClient = window.supabase ? window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY) : null;
    
    if (supabaseClient) {
        const { data: { session } } = await supabaseClient.auth.getSession();
        
        if (session) {
            const artistLink = document.getElementById('footer-artist-link');
            if (!artistLink) return;

            // 1. ЛОГІКА ДЛЯ АДМІНІСТРАТОРА
            if (session.user.email === 'admin@gmail.com') {
                artistLink.href = 'dashboard'; 
                artistLink.innerHTML = `
                    <span class="text-lines">ESPACE<br>ADMIN</span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                `;
                return; // Зупиняємо скрипт
            }

            // 2. ПЕРЕВІРКА НА ХУДОЖНИКА
            const { data: artistData } = await supabaseClient
                .from('artistes')
                .select('id')
                .eq('auth_user_id', session.user.id)
                .single();

            if (artistData) {
                // Якщо це художник
                artistLink.href = 'studio'; 
                artistLink.innerHTML = `
                    <span class="text-lines">MON<br>STUDIO.</span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                `;
            } else {
                // 3. ЛОГІКА ДЛЯ ЗВИЧАЙНОГО КЛІЄНТА (Нове!)
                // Якщо сесія є, але це не адмін і не художник, значить це клієнт
                artistLink.href = 'mon-compte'; 
                artistLink.innerHTML = `
                    <span class="text-lines">MON<br>COMPTE.</span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                `;
            }
        }
    }
})();
</script>