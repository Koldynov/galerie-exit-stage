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
                <a href="./">Accueil</a>
                <a href="peintures">Peintures</a>
                <a href="sculptures">Sculptures</a>
                <a href="artistes">Artistes</a>
                <a href="journal">Le Journal</a>
            </div>
            
            <div class="link-group">
                <h4>Service Client</h4>
                <a href="faq">F.A.Q / Aide</a>
                <a href="livraison-retours">Livraison & Retours</a>
                <a href="contact">Contactez-nous</a>
            </div>

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

    <div class="footer-newsletter-bar" style="border-top: 1px solid rgba(255,255,255,0.05); padding-top: 35px; margin-top: 20px; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 30px; align-items: center; justify-content: space-between;">
        <div style="flex: 1 1 400px;">
            <h4 style="color: var(--color-gold); font-size: 1rem; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1.5px;">Rejoignez le cercle EXIT</h4>
            <p style="color: #888; font-size: 0.85rem; margin: 0;">Soyez informés en avant-première de nos nouvelles expositions et événements exclusifs.</p>
        </div>
        <div style="flex: 1 1 400px;">
            <form id="newsletter-form" style="display: flex; gap: 10px; width: 100%;">
                <input type="email" id="nl-email" placeholder="Votre adresse email" required style="flex: 1; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); padding: 12px 15px; color: white; border-radius: 4px; outline: none; font-size: 0.9rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#d4af37'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                <button type="submit" class="btn-primary" style="padding: 12px 25px; font-size: 0.9rem; margin: 0; white-space: nowrap;" id="nl-btn">S'inscrire</button>
            </form>
            <p id="nl-msg" style="margin-top: 8px; font-size: 0.85rem; display: none; font-weight: 500;"></p>
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
                    <p>Permettent de vous proposer des œuvres d'art adaptées à vos goûts.</p>
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
    const supabaseClient = window.supabase ? window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY) : null;
    
    if (supabaseClient) {
        const { data: { session } } = await supabaseClient.auth.getSession();
        
        if (session) {
            const artistLink = document.getElementById('footer-artist-link');
            if (!artistLink) return;

            if (session.user.email === 'admin@gmail.com') {
                artistLink.href = 'dashboard'; 
                artistLink.innerHTML = `
                    <span class="text-lines">ESPACE<br>ADMIN</span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                `;
                return;
            }

            const { data: artistData } = await supabaseClient
                .from('artistes')
                .select('id')
                .eq('auth_user_id', session.user.id)
                .single();

            if (artistData) {
                artistLink.href = 'studio'; 
                artistLink.innerHTML = `
                    <span class="text-lines">MON<br>STUDIO.</span>
                    <i class="far fa-arrow-alt-circle-right"></i>
                `;
            } else {
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
    
    const savedPreferences = localStorage.getItem('exit_cookie_prefs');

    if (!savedPreferences) {
        const scrollTrigger = () => {
            if (window.scrollY > 300) {
                banner.classList.add('show');
                window.removeEventListener('scroll', scrollTrigger);
            }
        };
        
        if (window.scrollY > 300) {
            banner.classList.add('show');
        } else {
            window.addEventListener('scroll', scrollTrigger);
        }
    } else {
        applyCookiePreferences(JSON.parse(savedPreferences));
    }

    // --- ОБРОБНИК ФОРМИ NEWSLETTER ---
    document.getElementById('newsletter-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const emailInput = document.getElementById('nl-email');
        const msg = document.getElementById('nl-msg');
        const btn = document.getElementById('nl-btn');
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        try {
            const supabaseUrl = window.APP_CONFIG.SUPABASE_URL;
            const supabaseKey = window.APP_CONFIG.SUPABASE_ANON_KEY;
            
            const response = await fetch(`${supabaseUrl}/rest/v1/newsletter_subscribers`, {
                method: 'POST',
                headers: {
                    'apikey': supabaseKey,
                    'Authorization': `Bearer ${supabaseKey}`,
                    'Content-Type': 'application/json',
                    'Prefer': 'return=minimal'
                },
                body: JSON.stringify({ email: emailInput.value })
            });

            msg.style.display = 'block';
            if (response.ok) {
                msg.style.color = '#2ecc71';
                msg.innerHTML = '<i class="fas fa-check"></i> Inscription réussie !';
                emailInput.value = '';
            } else {
                msg.style.color = '#e74c3c';
                msg.innerHTML = '<i class="fas fa-info-circle"></i> Déjà inscrit avec cet email.';
            }
        } catch (error) {
            msg.style.color = '#e74c3c';
            msg.innerHTML = "Une erreur est survenue.";
        }
        
        btn.innerHTML = "S'inscrire";
        btn.disabled = false;
        setTimeout(() => { msg.style.display = 'none'; }, 4000);
    });

    // --- ЛОГІКА КНОПОК COOKIES ---
    document.getElementById('btn-accept-all')?.addEventListener('click', () => {
        const prefs = { essentiel: true, analytics: true, marketing: true };
        saveAndApplyCookies(prefs);
    });

    document.getElementById('btn-reject-all')?.addEventListener('click', () => {
        const prefs = { essentiel: true, analytics: false, marketing: false };
        saveAndApplyCookies(prefs);
    });

    document.getElementById('btn-personalize')?.addEventListener('click', () => {
        modal.classList.add('show');
    });

    document.getElementById('btn-save-cookies')?.addEventListener('click', () => {
        const prefs = {
            essentiel: true,
            analytics: document.getElementById('cookie-analytics').checked,
            marketing: document.getElementById('cookie-marketing').checked
        };
        modal.classList.remove('show');
        saveAndApplyCookies(prefs);
    });

    function saveAndApplyCookies(prefs) {
        localStorage.setItem('exit_cookie_prefs', JSON.stringify(prefs));
        banner.classList.remove('show');
        applyCookiePreferences(prefs);
    }

    function applyCookiePreferences(prefs) {
        console.log("Préférences Cookies appliquées :", prefs);
        if (prefs.analytics) console.log("Analytics Enabled");
        if (prefs.marketing) console.log("Marketing Enabled");
    }
});


document.addEventListener('DOMContentLoaded', () => {
    // Знаходимо всі головні пункти меню, які мають підкатегорії (клас .dropdown)
    const dropdownLinks = document.querySelectorAll('.nav-links .dropdown > a');
    
    dropdownLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Перевіряємо, чи це мобільний пристрій (ширина екрану <= 768px)
            if (window.innerWidth <= 768) {
                e.preventDefault(); // Забороняємо перехід на сторінку "Peintures"!
                
                // Додаємо або забираємо клас 'active-mobile' щоб відкрити підменю
                const parentDropdown = this.parentElement;
                parentDropdown.classList.toggle('active-mobile');
            }
        });
    });
});
</script>