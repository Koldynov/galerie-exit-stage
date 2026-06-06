<?php include __DIR__ . '/../../includes/header.php'; ?>
<link rel="stylesheet" href="/assets/css/legal.css">

<div class="legal-wrapper" style="display: block;">
    <div class="legal-header">
        <h1 class="legal-title">Contact</h1>
        <p class="legal-subtitle">Une question ? Un projet ? Écrivez-nous.</p>
    </div>

    <div class="contact-grid">
        <div class="contact-info">
            <div class="contact-box">
                <h3 style="color: var(--color-gold); margin-bottom: 15px;">La Galerie EXIT.</h3>
                <p><i class="fas fa-map-marker-alt" style="width: 20px;"></i> 2 Rue des Bains, 73100 AIX LES BAINS</p>
                <p><i class="fas fa-envelope" style="width: 20px;"></i> exit.heartconcept@gmail.com</p>
                <p><i class="fas fa-clock" style="width: 20px;"></i> Lun - Ven : 10h00 - 18h00</p>
            </div>
        </div>

        <div class="contact-form-wrapper">
            <form id="contact-form" class="exit-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom complet</label>
                        <input type="text" name="nom" placeholder="Votre nom" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="votre@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Sujet du message</label>
                    <select name="sujet" required>
                        <option value="" disabled selected>Choisissez un sujet</option>
                        <option value="acquisition">Acquisition d'une œuvre</option>
                        <option value="artiste">Devenir artiste partenaire</option>
                        <option value="atelier">Question sur les Ateliers (Nouveau !)</option>
                        <option value="autre">Autre demande</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="6" placeholder="Comment pouvons-nous vous aider ?" required></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Envoyer le message</button>
                <div id="form-status" style="margin-top: 15px; font-size: 0.95rem; text-align: center; font-weight: bold;"></div>
            </form>
        </div>
    </div>
</div>

<script src="/galerie/assets/js/legal.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contact-form');
    
    // Ініціалізуємо Supabase (беремо ключі з нашого конфігу в хедері)
    const supabaseClient = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = contactForm.querySelector('button[type="submit"]');
        const status = document.getElementById('form-status');
        
        // Збираємо дані з форми
        const formData = new FormData(contactForm);
        const data = {
            nom: formData.get('nom'),
            email: formData.get('email'),
            sujet: formData.get('sujet'),
            message: formData.get('message'),
            statut: 'nouveau'
        };

        // Анімація кнопки
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
        btn.disabled = true;
        status.innerHTML = "";

        try {
            // Відправляємо в базу даних
            const { error } = await supabaseClient.from('contact_messages').insert([data]);
            
            if (error) throw error;

            btn.innerHTML = originalText;
            btn.disabled = false;
            status.innerHTML = "Merci ! Votre message a bien été envoyé. Nous vous répondrons sous 24/48h.";
            status.style.color = "#4CAF50";
            contactForm.reset();

        } catch (error) {
            console.error(error);
            btn.innerHTML = originalText;
            btn.disabled = false;
            status.innerHTML = "Une erreur est survenue. Veuillez réessayer.";
            status.style.color = "#ff4d4d";
        }
    });
});
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>