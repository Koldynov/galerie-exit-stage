<?php require_once __DIR__ . '/../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/galerie/">
    <meta charset="UTF-8">
    <title>Facture - EXIT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; color: #111; font-family: 'Inter', sans-serif; margin: 0; display: flex; justify-content: center; align-items: flex-start; padding: 40px 20px; min-height: 100vh; box-sizing: border-box; }
        .invoice-container { width: 100%; max-width: 800px; background: #fff; padding: 50px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 8px solid #d4af37; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 50px; border-bottom: 2px solid #eee; padding-bottom: 30px; }
        .header-left h1 { margin: 0; font-size: 2.5rem; letter-spacing: 3px; }
        .header-left p { margin: 5px 0 0 0; color: #666; font-size: 0.9rem; }
        .header-right { text-align: right; }
        .header-right h2 { margin: 0 0 5px 0; color: #d4af37; font-size: 1.5rem; text-transform: uppercase; }
        .header-right p { margin: 0; color: #555; font-size: 0.9rem; }
        .client-info { margin-bottom: 40px; }
        .client-info h3 { font-size: 1rem; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .client-info p { margin: 5px 0; font-size: 1rem; font-weight: 600; }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .invoice-table th { background: #f9f9f9; text-align: left; padding: 12px; font-size: 0.85rem; text-transform: uppercase; color: #666; border-bottom: 2px solid #eee; }
        .invoice-table td { padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 1rem; }
        .invoice-table th.right, .invoice-table td.right { text-align: right; }
        .total-section { display: flex; justify-content: flex-end; margin-bottom: 50px; }
        .total-box { width: 300px; }
        .total-line { display: flex; justify-content: space-between; padding: 10px 0; font-size: 1rem; color: #555; }
        .total-line.grand-total { font-size: 1.3rem; font-weight: 800; color: #111; border-top: 2px solid #111; padding-top: 15px; margin-top: 5px; }
        .footer { text-align: center; color: #888; font-size: 0.85rem; border-top: 1px solid #eee; padding-top: 20px; }
        [v-cloak] { display: none; }
        @media print {
            body { background: white; padding: 0; }
            .invoice-container { box-shadow: none; width: 100%; max-width: 100%; padding: 20px; border-top: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div id="app" class="invoice-container" v-cloak>
        <div v-if="loading" style="text-align:center; padding: 50px;">Chargement de la facture...</div>
        <div v-else-if="commande">
            
            <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: flex-end;">
                <button onclick="window.print()" style="background: #111; border: none; color: white; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-size: 0.9rem; font-weight: 600;">
                    <svg style="width:16px; height:16px; fill:currentColor; margin-right:8px; vertical-align:text-bottom;" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M128 0C92.6 0 64 28.6 64 64v96h64V64H354.7L384 93.3V160h64V93.3c0-17-6.7-33.3-18.7-45.3L400 18.7C388 6.7 371.7 0 354.7 0H128zM512 256c0-35.3-28.7-64-64-64H64c-35.3 0-64 28.7-64 64v160c0 17.7 14.3 32 32 32h32v32c0 17.7 14.3 32 32 32h288c17.7 0 32-14.3 32-32v-32h32c17.7 0 32-14.3 32-32V256zM384 448H128V320h256v128z"/></svg>
                    Imprimer / PDF
                </button>
            </div>

            <div class="header">
                <div class="header-left">
                    <h1>EXIT</h1>
                    <p>Galerie d'Art Contemporain</p>
                    <p>12 Rue des Artistes, 75001 Paris, France</p>
                    <p>contact@galerie-exit.com</p>
                </div>
                <div class="header-right">
                    <h2>FACTURE</h2>
                    <p><strong>N° :</strong> FAC-{{ commande.id.substring(0,8).toUpperCase() }}</p>
                    <p><strong>Date :</strong> {{ formatDate(commande.created_at) }}</p>
                    <p style="color: #2ecc71; font-weight: bold; margin-top: 10px;">PAYÉE</p>
                </div>
            </div>

            <div class="client-info">
                <h3>Facturé à :</h3>
                <p>{{ commande.nom_client }}</p>
                <p style="font-weight: normal; color: #555;">{{ commande.user_email }}</p>
            </div>

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Description de l'article</th>
                        <th class="right">Qte</th>
                        <th class="right">Prix Unitaire</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ commande.oeuvres?.titre || 'Œuvre d\'art' }}</strong>
                            <br>
                            <span style="font-size: 0.85rem; color: #888;">ID: {{ commande.oeuvre_id }}</span>
                        </td>
                        <td class="right">1</td>
                        <td class="right">{{ commande.montant_total }} €</td>
                        <td class="right">{{ commande.montant_total }} €</td>
                    </tr>
                </tbody>
            </table>

            <div class="total-section">
                <div class="total-box">
                    <div class="total-line">
                        <span>Sous-total</span>
                        <span>{{ commande.montant_total }} €</span>
                    </div>
                    <div class="total-line">
                        <span>TVA (20%) - Inclus</span>
                        <span>{{ (commande.montant_total * 0.20).toFixed(2) }} €</span>
                    </div>
                    <div class="total-line grand-total">
                        <span>TOTAL TTC</span>
                        <span>{{ commande.montant_total }} €</span>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>Merci pour votre confiance et votre soutien à l'art contemporain.</p>
                <p>Numéro SIRET : 123 456 789 00012 | TVA Intracommunautaire : FR 12 345678901</p>
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
                const commande = ref(null);
                const loading = ref(true);

                const supabase = window.supabase.createClient(window.APP_CONFIG.SUPABASE_URL, window.APP_CONFIG.SUPABASE_ANON_KEY);

                const formatDate = (dateString) => {
                    return new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                };

                onMounted(async () => {
                    const id = window.CURRENT_ITEM_ID; // Оновлений спосіб отримання ID
                    
                    if (id) {
                        const { data } = await supabase.from('commandes').select('*, oeuvres(titre, id)').eq('id', id).single();
                        if (data) commande.value = data;
                    }
                    loading.value = false;
                });

                return { commande, loading, formatDate };
            }
        }).mount('#app');
    </script>
</body>
</html>