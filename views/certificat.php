<?php require_once __DIR__ . '/../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/galerie/">
    <meta charset="UTF-8">
    <title>Certificat d'Authenticité - EXIT</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #fff; color: #111; font-family: 'Inter', sans-serif; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .cert-container { width: 800px; padding: 60px; border: 2px solid #d4af37; outline: 10px solid #fff; outline-offset: -12px; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: relative; }
        .header { text-align: center; margin-bottom: 50px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .header h1 { font-family: 'Cinzel', serif; font-size: 3rem; color: #111; margin: 0; letter-spacing: 5px; }
        .header p { color: #d4af37; font-family: 'Cinzel', serif; font-size: 1.2rem; margin: 5px 0 0 0; letter-spacing: 2px; }
        .content { text-align: center; margin-bottom: 50px; }
        .content h2 { font-family: 'Cinzel', serif; font-size: 2.2rem; margin-bottom: 10px; }
        .content p.artist { font-size: 1.3rem; color: #555; margin-bottom: 40px; }
        .details { text-align: left; width: 60%; margin: 0 auto; border-top: 1px solid #eee; border-bottom: 1px solid #eee; padding: 20px 0; }
        .details p { display: flex; justify-content: space-between; font-size: 1.1rem; margin: 10px 0; }
        .details strong { color: #888; font-weight: normal; }
        .details span { font-weight: 600; }
        .footer { display: flex; justify-content: space-between; margin-top: 60px; align-items: flex-end; }
        .signature { text-align: center; width: 250px; }
        .signature-line { border-bottom: 1px solid #111; margin-bottom: 10px; height: 40px; }
        .seal { width: 100px; height: 100px; border: 3px solid #d4af37; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-family: 'Cinzel', serif; color: #d4af37; font-weight: bold; font-size: 1.2rem; transform: rotate(-15deg); opacity: 0.7; }
        [v-cloak] { display: none; }
        @media print {
            body { background: white; }
            .cert-container { box-shadow: none; border-color: #d4af37; width: 100%; padding: 40px; outline: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div id="app" class="cert-container" v-cloak>
        <div v-if="loading" style="text-align:center; padding: 50px;">Chargement...</div>
        <div v-else-if="oeuvre">
            
            <div class="header">
                <h1>EXIT</h1>
                <p>Certificat d'Authenticité</p>
            </div>

            <div class="content">
                <p style="font-size: 1.1rem; line-height: 1.6; color: #555; margin-bottom: 30px;">
                    Ce document certifie que l'œuvre d'art décrite ci-dessous est une création originale et authentique de l'artiste.
                </p>

                <h2>"{{ oeuvre.titre }}"</h2>
                <p class="artist">Par <strong>{{ oeuvre.artistes?.nom || 'Artiste inconnu' }}</strong></p>

                <div class="details">
                    <p><strong>Catégorie :</strong> <span>{{ oeuvre.categorie }}</span></p>
                    <p><strong>Date d'émission :</strong> <span>{{ currentDate }}</span></p>
                    <p><strong>ID Œuvre :</strong> <span style="font-size: 0.8rem;">{{ oeuvre.id }}</span></p>
                </div>
            </div>

            <div class="footer">
                <div class="seal">
                    EXIT<br>GALERIE
                </div>
                <div class="signature">
                    <div class="signature-line">
                        <img src="/galerie/assets/img/signature-sabrina.png" alt="Signature" style="height: 40px;" onerror="this.style.display='none'">
                    </div>
                    <p style="margin:0; font-size: 0.9rem; color: #555;">Sabrina, Directrice de la Galerie</p>
                </div>
            </div>

            <div class="no-print" style="text-align: center; margin-top: 50px;">
                <button onclick="window.print()" style="background: #d4af37; border: none; color: black; padding: 12px 25px; font-weight: bold; cursor: pointer; border-radius: 4px; font-size: 1rem;">
                    Imprimer / Sauvegarder en PDF
                </button>
            </div>

        </div>
    </div>

    <script>
    window.APP_CONFIG = {
        SUPABASE_URL: "<?php echo SUPABASE_URL; ?>",
        SUPABASE_ANON_KEY: "<?php echo SUPABASE_ANON_KEY; ?>"
    };
    window.CURRENT_ITEM_ID = "<?php echo $item_id; ?>";
    </script>

    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script>
        const { createApp, ref, onMounted } = Vue;

        createApp({
            setup() {
                const oeuvre = ref(null);
                const loading = ref(true);
                const currentDate = new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });

                const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

                onMounted(async () => {
                    const id = window.CURRENT_ITEM_ID; // Оновлений спосіб отримання ID
                    
                    if (id) {
                        const { data } = await supabase.from('oeuvres').select('*, artistes(nom)').eq('id', id).single();
                        if (data) oeuvre.value = data;
                    }
                    loading.value = false;
                });

                return { oeuvre, loading, currentDate };
            }
        }).mount('#app');
    </script>
</body>
</html>