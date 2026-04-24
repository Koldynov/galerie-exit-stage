<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

$app = AppFactory::create();

// ОБОВ'ЯЗКОВО: Базовий шлях для XAMPP
$app->setBasePath('/galerie');

$app->addErrorMiddleware(true, true, true);

function render($response, $filename, $params = []) {
    extract($params);
    ob_start();
    include __DIR__ . '/views/' . $filename;
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
}

// МАРШРУТИ
$app->get('/', function (Request $request, Response $response) { return render($response, 'home.php'); });
$app->get('/peintures', function (Request $request, Response $response) { return render($response, 'peintures.php'); });
$app->get('/sculptures', function (Request $request, Response $response) { return render($response, 'sculptures.php'); });
$app->get('/photographies', function (Request $request, Response $response) { return render($response, 'photographies.php'); });
$app->get('/artistes', function (Request $request, Response $response) { return render($response, 'artistes.php'); });
$app->get('/journal', function (Request $request, Response $response) { return render($response, 'actualites.php'); });

$app->get('/oeuvre/{id}', function (Request $request, Response $response, array $args) {
    return render($response, 'oeuvre.php', ['item_id' => $args['id']]);
});

$app->get('/artiste/{id}', function (Request $request, Response $response, array $args) {
    return render($response, 'profil-artiste.php', ['item_id' => $args['id']]);
});


$app->get('/portail-client', function (Request $request, Response $response) { return render($response, 'portail-client.php'); });
$app->get('/portail-artiste', function (Request $request, Response $response) { return render($response, 'portail-artiste.php'); });
$app->get('/studio', function (Request $request, Response $response) { return render($response, 'espace-artiste.php'); });
$app->get('/mon-compte', function (Request $request, Response $response) { return render($response, 'mon-compte.php'); });
$app->get('/dashboard', function (Request $request, Response $response) { return render($response, 'dashboard.php'); });
$app->get('/paiement/{id}', function (Request $request, Response $response, array $args) {
    return render($response, 'paiement.php', ['item_id' => $args['id']]);
});

$app->get('/facture/{id}', function (Request $request, Response $response, array $args) {
    return render($response, 'facture.php', ['item_id' => $args['id']]);
});

$app->get('/certificat/{id}', function (Request $request, Response $response, array $args) {
    return render($response, 'certificat.php', ['item_id' => $args['id']]);
});


// --- API РОУТ ДЛЯ ВІДПРАВКИ EMAIL (BREVO) ---
// --- API РОУТ ДЛЯ ЗАПИТІВ (DEMANDES - BREVO) ---
$app->post('/api/notify-demande', function ($request, $response, $args) {
    try {
        if (file_exists(__DIR__ . '/includes/config.php')) {
            require_once __DIR__ . '/includes/config.php';
        } elseif (file_exists(__DIR__ . '/config.php')) {
            require_once __DIR__ . '/config.php';
        }

        $data = json_decode($request->getBody()->getContents(), true);
        $clientName = $data['nom'] ?? 'Client';
        $clientEmail = $data['email'] ?? '';
        $oeuvreTitre = $data['oeuvre_titre'] ?? 'Œuvre';

        $apiKey = BREVO_API_KEY;

        $sendBrevoEmail = function($toEmail, $toName, $subject, $htmlContent) use ($apiKey) {
            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'accept: application/json',
                'api-key: ' . $apiKey,
                'content-type: application/json'
            ]);
            $postData = [
                'sender' => ['name' => 'La Galerie EXIT', 'email' => 'smalijhuk2017@gmail.com'], 
                'to' => [['email' => $toEmail, 'name' => $toName]],
                'subject' => $subject,
                'htmlContent' => $htmlContent
            ];
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        };

        // 1. ЕЛЕГАНТНИЙ ЛИСТ КЛІЄНТУ (Підтвердження отримання запиту)
        $subjectClient = "Votre demande pour l'œuvre : " . $oeuvreTitre;
        $htmlClient = "
        <div style='background-color: #0a0a0a; color: #ffffff; padding: 40px; font-family: Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #d4af37; font-size: 32px; letter-spacing: 4px; margin: 0;'>EXIT<span style='color: #ffffff;'>.</span></h1>
            </div>
            <div style='background-color: #111111; padding: 35px; border-left: 4px solid #3498db;'>
                <h2 style='margin-top: 0; font-weight: 300; color: #ffffff;'>Demande reçue</h2>
                <p style='font-size: 16px; line-height: 1.6;'>Bonjour <strong>$clientName</strong>,</p>
                <p style='font-size: 16px; line-height: 1.6; color: #cccccc;'>Nous avons bien reçu votre demande d'information concernant l'œuvre <strong style='color: #3498db;'>$oeuvreTitre</strong>.</p>
                <p style='font-size: 16px; line-height: 1.6; color: #cccccc;'>Sabrina étudie actuellement votre message et reviendra vers vous très prochainement avec tous les détails demandés.</p>
            </div>
            <div style='text-align: center; margin-top: 40px; color: #666666; font-size: 12px;'>
                <p>© La Galerie EXIT | Paris</p>
            </div>
        </div>
        ";
        
        // 2. ІНФОРМАТИВНИЙ ЛИСТ ДЛЯ САБРІНИ (Новий лід - Синій акцент)
        $subjectAdmin = "🔥 Nouveau Lead : " . $oeuvreTitre;
        $htmlAdmin = "
        <div style='background-color: #f8f9fa; color: #333333; padding: 40px; font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #ffffff; padding: 30px; border-top: 4px solid #3498db; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);'>
                <h2 style='color: #3498db; margin-top: 0;'>Nouveau contact client</h2>
                <p style='font-size: 15px;'>Sabrina, un client souhaite obtenir des informations sur une œuvre :</p>
                <div style='background: #f0f7ff; padding: 20px; border-radius: 4px; margin: 20px 0;'>
                    <p style='margin: 5px 0;'><strong>Nom :</strong> $clientName</p>
                    <p style='margin: 5px 0;'><strong>Email :</strong> <a href='mailto:$clientEmail' style='color: #3498db;'>$clientEmail</a></p>
                    <p style='margin: 5px 0;'><strong>Œuvre :</strong> $oeuvreTitre</p>
                </div>
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='http://localhost/galerie/dashboard#demandes' style='background-color: #3498db; color: #ffffff; padding: 12px 25px; text-decoration: none; font-weight: bold; border-radius: 4px; display: inline-block;'>GÉRER DANS LE KANBAN</a>
                </div>
            </div>
        </div>
        ";

        $sendBrevoEmail($clientEmail, $clientName, $subjectClient, $htmlClient);
        $sendBrevoEmail(ADMIN_EMAIL, 'Sabrina', $subjectAdmin, $htmlAdmin);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Throwable $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// --- API РОУТ ДЛЯ ПІДТВЕРДЖЕННЯ ПОКУПКИ (BREVO) ---
// --- API РОУТ ДЛЯ ПІДТВЕРДЖЕННЯ ПОКУПКИ (BREVO) ---
// --- API РОУТ ДЛЯ ПІДТВЕРДЖЕННЯ ПОКУПКИ (BREVO) ---
$app->post('/api/notify-achat', function ($request, $response, $args) {
    try {
        // 1. ПРИМУСОВО ПІДКЛЮЧАЄМО КОНФІГ (перевіряємо обидва варіанти шляху)
        if (file_exists(__DIR__ . '/includes/config.php')) {
            require_once __DIR__ . '/includes/config.php';
        } elseif (file_exists(__DIR__ . '/config.php')) {
            require_once __DIR__ . '/config.php';
        }

        if (!defined('BREVO_API_KEY')) {
            throw new Exception("Файл config.php не підключився або ключі відсутні!");
        }

        $data = json_decode($request->getBody()->getContents(), true);
        $clientName = $data['nom'] ?? 'Client';
        $clientEmail = $data['email'] ?? '';
        $oeuvreTitre = $data['oeuvre_titre'] ?? 'Œuvre';
        $montant = $data['montant'] ?? '0';

        $apiKey = BREVO_API_KEY;

        $sendBrevoEmail = function($toEmail, $toName, $subject, $htmlContent) use ($apiKey) {
            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'accept: application/json',
                'api-key: ' . $apiKey,
                'content-type: application/json'
            ]);
            $postData = [
                'sender' => ['name' => 'La Galerie EXIT', 'email' => 'smalijhuk2017@gmail.com'], // Твоя верифікована пошта
                'to' => [['email' => $toEmail, 'name' => $toName]],
                'subject' => $subject,
                'htmlContent' => $htmlContent
            ];
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Отримуємо код відповіді від Brevo
            
            if(curl_errno($ch)){
                throw new Exception("Помилка cURL: " . curl_error($ch));
            }
            
            // Якщо Brevo повертає помилку (наприклад, блокує gmail)
            if ($httpCode >= 400) {
                throw new Exception("Помилка від Brevo: " . $result);
            }
            
            curl_close($ch);
            return $result;
        };

        // 1. ЛЮКСОВИЙ ЛИСТ ДЛЯ КЛІЄНТА (Темна тема, золото)
        $subjectClient = "Confirmation de votre acquisition : " . $oeuvreTitre;
        $htmlClient = "
        <div style='background-color: #0a0a0a; color: #ffffff; padding: 40px; font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #d4af37; font-size: 36px; letter-spacing: 4px; margin: 0;'>EXIT<span style='color: #ffffff;'>.</span></h1>
            </div>
            <div style='background-color: #111111; padding: 35px; border-left: 4px solid #d4af37;'>
                <h2 style='margin-top: 0; font-weight: 300; letter-spacing: 1px;'>Félicitations pour votre acquisition !</h2>
                <p style='font-size: 16px; line-height: 1.6;'>Bonjour <strong>$clientName</strong>,</p>
                <p style='font-size: 16px; line-height: 1.6; color: #cccccc;'>Nous vous confirmons la bonne réception de votre paiement de <strong style='color: #d4af37; font-size: 18px;'>$montant €</strong> pour l'œuvre <strong style='color: #ffffff;'>$oeuvreTitre</strong>.</p>
                <p style='font-size: 16px; line-height: 1.6; color: #cccccc;'>Notre galeriste Sabrina vous contactera dans les plus brefs délais pour organiser la livraison sécurisée de votre nouvelle œuvre.</p>
            </div>
            <div style='text-align: center; margin-top: 40px; color: #666666; font-size: 12px;'>
                <p>La Galerie EXIT | Art Contemporain</p>
                <p>Retrouvez votre facture et certificat d'authenticité dans votre <a href='http://localhost/galerie/portail-client' style='color: #d4af37; text-decoration: none;'>espace membre</a>.</p>
            </div>
        </div>
        ";
        
        // 2. ЗРУЧНИЙ ЛИСТ ДЛЯ САБРІНИ (Світла тема, акцент на гроші і лід)
        $subjectAdmin = "💰 VENTE RÉALISÉE : " . $oeuvreTitre;
        $htmlAdmin = "
        <div style='background-color: #f4f4f4; color: #333333; padding: 40px; font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #ffffff; padding: 30px; border-top: 4px solid #27ae60; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);'>
                <h2 style='color: #27ae60; margin-top: 0;'>Nouvelle Vente Réalisée !</h2>
                <p style='font-size: 16px;'>Excellente nouvelle Sabrina, une œuvre vient d'être payée en ligne.</p>
                <table style='width: 100%; margin-top: 20px; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 12px 0; border-bottom: 1px solid #eeeeee; color: #666;'><strong>Client :</strong></td>
                        <td style='padding: 12px 0; border-bottom: 1px solid #eeeeee;'>$clientName</td>
                    </tr>
                    <tr>
                        <td style='padding: 12px 0; border-bottom: 1px solid #eeeeee; color: #666;'><strong>Email :</strong></td>
                        <td style='padding: 12px 0; border-bottom: 1px solid #eeeeee;'><a href='mailto:$clientEmail' style='color: #3498db; text-decoration: none;'>$clientEmail</a></td>
                    </tr>
                    <tr>
                        <td style='padding: 12px 0; border-bottom: 1px solid #eeeeee; color: #666;'><strong>Œuvre :</strong></td>
                        <td style='padding: 12px 0; border-bottom: 1px solid #eeeeee;'><strong>$oeuvreTitre</strong></td>
                    </tr>
                    <tr>
                        <td style='padding: 12px 0; color: #666;'><strong>Montant encaissé :</strong></td>
                        <td style='padding: 12px 0;'><strong style='color: #27ae60; font-size: 20px;'>$montant €</strong></td>
                    </tr>
                </table>
                <div style='text-align: center; margin-top: 35px;'>
                    <a href='http://localhost/galerie/dashboard' style='background-color: #0a0a0a; color: #d4af37; padding: 14px 28px; text-decoration: none; font-weight: bold; border-radius: 4px; display: inline-block; letter-spacing: 1px;'>ALLER AU DASHBOARD</a>
                </div>
            </div>
        </div>
        ";

        $sendBrevoEmail($clientEmail, $clientName, $subjectClient, $htmlClient);
        $sendBrevoEmail(ADMIN_EMAIL, 'Sabrina', $subjectAdmin, $htmlAdmin);

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Throwable $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->run();