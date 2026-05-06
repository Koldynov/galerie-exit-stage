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
    <div id="cookie-consent-banner" class="cookie-banner">
    <div class="cookie-banner-content">
        <h4>Respect de votre vie privée</h4>
        <p>Nous utilisons des cookies pour améliorer votre expérience sur La Galerie EXIT, analyser notre trafic et sécuriser vos transactions (Stripe). <a href="/galerie/cookies">En savoir plus</a>.</p>
        <div class="cookie-buttons">
            <button id="btn-accept-all" class="btn-primary">Accepter tout</button>
            <button id="btn-reject-all" class="btn-secondary">Refuser tout</button>
            <button id="btn-personalize" class="btn-text">Personnaliser</button>
        </div>
    </div>
</div>

<div id="cookie-modal" class="cookie-modal-overlay">
    <div class="cookie-modal-box">
        <h3>Paramètres des Cookies</h3>
        <p>Gérez vos préférences. Les cookies essentiels ne peuvent pas être désactivés car ils sont nécessaires au fonctionnement du site.</p>
        
        <div class="cookie-option">
            <div class="cookie-option-info">
                <h4>Essentiels (Stripe, Session)</h4>
                <p>Nécessaires pour le paiement et la connexion (Supabase).</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="cookie-essentiel" checked disabled>
                <span class="slider"></span>
            </label>
        </div>

        <div class="cookie-option">
            <div class="cookie-option-info">
                <h4>Analytiques</h4>
                <p>Nous aident à comprendre comment vous utilisez la galerie.</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="cookie-analytics">
                <span class="slider"></span>
            </label>
        </div>

        <div class="cookie-option">
            <div class="cookie-option-info">
                <h4>Marketing</h4>
                <p>Permettent de vous proposer des œuvres adaptées à vos goûts.</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="cookie-marketing">
                <span class="slider"></span>
            </label>
        </div>

        <button id="btn-save-cookies" class="btn-primary" style="width: 100%; margin-top: 20px;">Enregistrer mes choix</button>
    </div>
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

document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('cookie-consent-banner');
    const modal = document.getElementById('cookie-modal');
    
    // Перевіряємо, чи клієнт вже робив вибір
    const savedPreferences = localStorage.getItem('exit_cookie_prefs');

    if (!savedPreferences) {
        // Якщо вибору ще немає - показуємо банер, АЛЕ тільки після скролу!
        const scrollTrigger = () => {
            // Якщо проскролив більше 300px (пройшов логотип)
            if (window.scrollY > 300) {
                banner.classList.add('show');
                window.removeEventListener('scroll', scrollTrigger); // Видаляємо слухача, щоб не спамити
            }
        };
        
        // Якщо клієнт оновив сторінку і вже внизу - показуємо одразу
        if (window.scrollY > 300) {
            banner.classList.add('show');
        } else {
            window.addEventListener('scroll', scrollTrigger);
        }
    } else {
        // Якщо вибір є, застосовуємо його (тут ти можеш підключати Google Analytics тощо)
        applyCookiePreferences(JSON.parse(savedPreferences));
    }

    // --- ЛОГІКА КНОПОК ---

    // 1. Прийняти все
    document.getElementById('btn-accept-all')?.addEventListener('click', () => {
        const prefs = { essentiel: true, analytics: true, marketing: true };
        saveAndApplyCookies(prefs);
    });

    // 2. Відмовитись від усього (крім есенціал)
    document.getElementById('btn-reject-all')?.addEventListener('click', () => {
        const prefs = { essentiel: true, analytics: false, marketing: false };
        saveAndApplyCookies(prefs);
    });

    // 3. Відкрити налаштування
    document.getElementById('btn-personalize')?.addEventListener('click', () => {
        modal.classList.add('show');
    });

    // 4. Зберегти персоналізовані налаштування з модалки
    document.getElementById('btn-save-cookies')?.addEventListener('click', () => {
        const prefs = {
            essentiel: true, // Завжди true
            analytics: document.getElementById('cookie-analytics').checked,
            marketing: document.getElementById('cookie-marketing').checked
        };
        modal.classList.remove('show');
        saveAndApplyCookies(prefs);
    });

    // --- ФУНКЦІЇ ---

    function saveAndApplyCookies(prefs) {
        // Зберігаємо в LocalStorage
        localStorage.setItem('exit_cookie_prefs', JSON.stringify(prefs));
        
        // Ховаємо банер плавно
        banner.classList.remove('show');
        
        applyCookiePreferences(prefs);
    }

    function applyCookiePreferences(prefs) {
        console.log("Préférences Cookies appliquées :", prefs);
        
        // ТУТ РЕАЛЬНИЙ ФУНКЦІОНАЛ
        if (prefs.analytics) {
            // Якщо погодився на аналітику - запускаємо (наприклад Google Analytics)
            // loadGoogleAnalytics();
            console.log("Analytics Enabled");
        }
        
        if (prefs.marketing) {
            // Якщо погодився на маркетинг - запускаємо (наприклад Pixel Facebook)
            console.log("Marketing Enabled");
        }
    }
});
</script>