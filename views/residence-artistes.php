<?php 
$page_title = "Résidence d'Artistes | La Galerie EXIT";
include 'includes/header.php'; 
?>

<main class="page-transition">
    <!-- Шапка сторінки -->
    <section class="residence-hero">
        <div class="container">
            <h1 class="page-title fade-in-up">Résidence d'Artistes</h1>
            <p class="page-subtitle fade-in-up" style="animation-delay: 0.2s;">Un espace de création, de collaboration et d'innovation.</p>
        </div>
    </section>

    <!-- Основний контент -->
    <section class="residence-container">
        
        <div class="fade-in-up" style="animation-delay: 0.3s;">
            <h2 style="color: var(--color-gold); font-size: 2rem; margin-bottom: 20px;">Le Programme</h2>
            <p style="color: #ccc; font-size: 1.1rem; line-height: 1.8; margin-bottom: 30px;">
                La Résidence d'Artistes de la Galerie EXIT est un programme exclusif conçu pour soutenir les créateurs émergents et confirmés. Pendant une période allant de 1 à 3 mois, nous offrons aux artistes un espace dédié pour développer un projet spécifique, expérimenter de nouvelles techniques et s'immerger dans un environnement stimulant.
            </p>
        </div>

        <!-- Блок з пропозиціями -->
        <div class="residence-offer-box fade-in-up" style="animation-delay: 0.4s;">
            <h3 style="color: white; margin-bottom: 15px;">Ce que nous offrons :</h3>
            <ul style="color: #aaa; line-height: 1.8; padding-left: 20px; font-size: 1.05rem;">
                <li style="margin-bottom: 10px;">Un espace de travail privatif au cœur d'Aix-les-Bains.</li>
                <li style="margin-bottom: 10px;">Un accompagnement curatorial et stratégique par Sabrina.</li>
                <li style="margin-bottom: 10px;">La possibilité d'exposer les œuvres créées lors d'une exposition de fin de résidence.</li>
                <li>Des rencontres organisées avec des collectionneurs et professionnels du marché de l'art.</li>
            </ul>
        </div>

        <!-- Блок "Як подати заявку" -->
        <div class="fade-in-up" style="animation-delay: 0.5s;">
            <h2 style="color: var(--color-gold); font-size: 2rem; margin-bottom: 20px;">Comment postuler ?</h2>
            <p style="color: #ccc; font-size: 1.1rem; line-height: 1.8; margin-bottom: 30px;">
                La sélection se fait sur dossier. Si vous souhaitez rejoindre notre prochaine session de résidence, veuillez nous faire parvenir votre portfolio, votre démarche artistique, ainsi qu'une note d'intention détaillant le projet que vous souhaitez développer au sein de la galerie EXIT.
            </p>
            
            <div class="residence-buttons">
                <a href="mailto:smalijhuk2017@gmail.com?subject=Candidature%20Résidence%20d'Artistes" class="btn-primary residence-btn">
                    <i class="fas fa-envelope"></i> Envoyer mon dossier par Email
                </a>
                
                <a href="https://wa.me/33616437683?text=Bonjour%20Sabrina,%20je%20souhaite%20obtenir%20des%20informations%20sur%20la%20Résidence%20d'Artistes." target="_blank" class="residence-btn whatsapp">
                    <i class="fab fa-whatsapp"></i> Contacter via WhatsApp
                </a>
            </div>
        </div>

    </section>
</main>

<?php include 'includes/footer.php'; ?>