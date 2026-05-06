<?php
session_start(); // ОБОВ'ЯЗКОВО для роботи з авторизацією та підписками

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/config.php';

$app = AppFactory::create();
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
            'apikey: ' . SUPABASE_SERVICE_KEY, // ЗМІНИЛИ ТУТ
            'Authorization: Bearer ' . SUPABASE_SERVICE_KEY, // І ТУТ
            'Content-Type: application/json',
            'Prefer: return=representation'
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
$app->post('/api/notify-achat', function ($request, $response, $args) {
    try {
        if (file_exists(__DIR__ . '/includes/config.php')) require_once __DIR__ . '/includes/config.php';
        elseif (file_exists(__DIR__ . '/config.php')) require_once __DIR__ . '/config.php';

        if (!defined('BREVO_API_KEY')) throw new Exception("Brevo API Key is missing");

        $data = json_decode($request->getBody()->getContents(), true);
        $clientName = $data['nom'] ?? 'Client';
        $clientEmail = $data['email'] ?? '';
        $oeuvreTitre = $data['oeuvre_titre'] ?? 'Œuvre';
        $montant = $data['montant'] ?? '0';
        $artistName = $data['artist_name'] ?? 'L\'artiste';
        $artistEmail = $data['artist_email'] ?? null; // Email художника

        $apiKey = BREVO_API_KEY;

        $sendBrevoEmail = function($toEmail, $toName, $subject, $htmlContent) use ($apiKey) {
            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json', 'api-key: ' . $apiKey, 'content-type: application/json']);
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

        // 1. ЛИСТ ДЛЯ КЛІЄНТА 
        $subjectClient = "Confirmation de votre acquisition : " . $oeuvreTitre;
        $htmlClient = "
        <div style='background-color: #0a0a0a; color: #ffffff; padding: 40px; font-family: sans-serif; max-width: 600px; margin: 0 auto;'>
            <h1 style='color: #d4af37; text-align: center;'>EXIT<span style='color: #fff;'>.</span></h1>
            <div style='background-color: #111; padding: 30px; border-left: 4px solid #d4af37;'>
                <h2>Félicitations pour votre acquisition !</h2>
                <p>Bonjour <strong>$clientName</strong>,</p>
                <p>Votre paiement de <strong>$montant €</strong> pour l'œuvre <strong>$oeuvreTitre</strong> a bien été validé.</p>
                <p style='color: #d4af37;'><strong>Que se passe-t-il maintenant ?</strong></p>
                <p>L'artiste <strong>$artistName</strong> a été immédiatement notifié de votre achat. Il prépare actuellement votre œuvre pour l'expédition. Vous recevrez un nouvel email avec votre numéro de suivi (tracking) dès que le colis sera confié au transporteur.</p>
                <p>Vous pouvez suivre l'état de votre commande à tout moment dans votre <a href='http://localhost/galerie/portail-client' style='color: #d4af37;'>espace client</a>.</p>
            </div>
        </div>";
        
        // 2. ЛИСТ ДЛЯ ХУДОЖНИКА (Радісна новина)
        $subjectArtist = "🎉 Bonne nouvelle ! Votre œuvre '$oeuvreTitre' a été vendue !";
        $htmlArtist = "
        <div style='background-color: #f4f4f4; color: #333; padding: 40px; font-family: sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #fff; padding: 30px; border-top: 4px solid #d4af37;'>
                <h2 style='color: #d4af37; margin-top: 0;'>Félicitations $artistName !</h2>
                <p>Nous avons le plaisir de vous annoncer que votre œuvre <strong>$oeuvreTitre</strong> vient d'être acquise par un collectionneur sur La Galerie EXIT.</p>
                <p><strong>Prochaine étape :</strong></p>
                <p>Connectez-vous à votre <a href='http://localhost/galerie/studio' style='color: #d4af37; font-weight: bold;'>Studio Artiste</a> pour consulter l'adresse de livraison du client. Une fois l'œuvre expédiée, n'oubliez pas de cliquer sur le bouton <strong>'Expédier'</strong> pour transmettre le numéro de suivi au client.</p>
                <p>Sabrina conserve les fonds en sécurité jusqu'à la réception de l'œuvre par le client.</p>
            </div>
        </div>";

        // 3. ЛИСТ ДЛЯ САБРІНИ
        $subjectAdmin = "💰 VENTE RÉALISÉE : " . $oeuvreTitre;
        $htmlAdmin = "<div style='font-family: sans-serif; padding: 20px;'><h2>Nouvelle Vente : $oeuvreTitre</h2><p>Client : $clientName ($clientEmail)</p><p>Artiste : $artistName</p><p>Montant : $montant €</p><a href='http://localhost/galerie/dashboard'>Voir dans l'admin</a></div>";

        // Відправляємо всім
        $sendBrevoEmail($clientEmail, $clientName, $subjectClient, $htmlClient);
        $sendBrevoEmail(ADMIN_EMAIL, 'Sabrina', $subjectAdmin, $htmlAdmin);
        if ($artistEmail) {
            $sendBrevoEmail($artistEmail, $artistName, $subjectArtist, $htmlArtist);
        }

        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// --- API РОУТ ДЛЯ СТВОРЕННЯ STRIPE PAYMENT INTENT ---
$app->post('/api/create-payment-intent', function ($request, $response, $args) {
    try {
        if (file_exists(__DIR__ . '/includes/config.php')) require_once __DIR__ . '/includes/config.php';
        elseif (file_exists(__DIR__ . '/config.php')) require_once __DIR__ . '/config.php';

        $data = json_decode($request->getBody()->getContents(), true);
        $montant = $data['montant'] ?? 0;

        if ($montant <= 0) {
            throw new Exception("Montant invalide");
        }

        // Ініціалізуємо Stripe
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

        // Створюємо PaymentIntent
        // Stripe приймає суму в центах! Тому множимо на 100
        // Створюємо PaymentIntent (ТІЛЬКИ КАРТКИ, без Link та Klarna)
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int)round($montant * 100), // Заодно фіксимо можливі копійки
            'currency' => 'eur',
            'payment_method_types' => ['card'], // Жорстко кажемо: тільки кредитки!
        ]);

        $response->getBody()->write(json_encode([
            'clientSecret' => $paymentIntent->client_secret,
            'paymentIntentId' => $paymentIntent->id, 
            'publishableKey' => STRIPE_PUBLISHABLE_KEY
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Throwable $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});
// =====================================================================
// НОВІ API РОУТИ ДЛЯ ПІДПИСКИ НА ХУДОЖНИКА
// =====================================================================

// 1. Перевірка: чи підписаний вже користувач
$app->get('/api/check-follow/{artist_id}', function (Request $request, Response $response, array $args) {
    $artistId = $args['artist_id'];
    $userId = $_SESSION['user_id'] ?? null; // Отримуємо ID користувача з сесії

    // Якщо клієнт не залогінений — він точно не підписаний
    if (!$userId) {
        $response->getBody()->write(json_encode(['is_following' => false]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    try {
        global $pdo; // Використовуємо підключення до БД з config.php
        
        $stmt = $pdo->prepare("SELECT id FROM user_artist_follows WHERE user_id = :user_id AND artist_id = :artist_id");
        $stmt->execute(['user_id' => $userId, 'artist_id' => $artistId]);
        $isFollowing = (bool) $stmt->fetch();

        $response->getBody()->write(json_encode(['is_following' => $isFollowing]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

// 2. Додавання або видалення підписки (Toggle)
$app->post('/api/follow-artist', function (Request $request, Response $response) {
    if (!isset($_SESSION['user_id'])) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Ви повинні увійти в систему']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    $userId = $_SESSION['user_id'];
    $data = json_decode($request->getBody()->getContents(), true);
    $artistId = $data['artist_id'] ?? null;

    if (!$artistId) {
        $response->getBody()->write(json_encode(['success' => false, 'message' => 'Не вказано ID художника']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
        global $pdo; // Використовуємо підключення до БД з config.php

        // Перевіряємо поточний статус
        $stmt = $pdo->prepare("SELECT id FROM user_artist_follows WHERE user_id = :user_id AND artist_id = :artist_id");
        $stmt->execute(['user_id' => $userId, 'artist_id' => $artistId]);
        $follow = $stmt->fetch();

        if ($follow) {
            // Якщо підписка є — видаляємо її (відписуємось)
            $stmt = $pdo->prepare("DELETE FROM user_artist_follows WHERE id = :id");
            $stmt->execute(['id' => $follow['id']]);
            $isFollowing = false;
        } else {
            // Якщо підписки немає — створюємо (підписуємось)
            $stmt = $pdo->prepare("INSERT INTO user_artist_follows (user_id, artist_id) VALUES (:user_id, :artist_id)");
            $stmt->execute(['user_id' => $userId, 'artist_id' => $artistId]);
            $isFollowing = true;
        }

        $response->getBody()->write(json_encode(['success' => true, 'is_following' => $isFollowing]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Throwable $e) {
        $response->getBody()->write(json_encode(['success' => false, 'error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});


// --- API РОУТ ДЛЯ WEBHOOK ВІД STRIPE ---
$app->post('/api/webhook/stripe', function ($request, $response, $args) {
    if (file_exists(__DIR__ . '/includes/config.php')) require_once __DIR__ . '/includes/config.php';
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $payload = $request->getBody()->getContents();
    $event = json_decode($payload);

    // Якщо Stripe каже, що платіж успішний!
    if ($event && $event->type == 'payment_intent.succeeded') {
        $paymentIntent = $event->data->object;
        $pi_id = $paymentIntent->id; 

        // 1. ОНОВЛЮЄМО ЗАМОВЛЕННЯ (Використовуємо SERVICE KEY для обходу захисту бази)
        $ch = curl_init(SUPABASE_URL . '/rest/v1/commandes?stripe_pi_id=eq.' . $pi_id);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['status' => 'paye'])); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . SUPABASE_SERVICE_KEY, 
            'Authorization: Bearer ' . SUPABASE_SERVICE_KEY, 
            'Content-Type: application/json',
            'Prefer: return=representation'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $updatedOrders = json_decode($result, true);

        // 2. ОНОВЛЮЄМО СТАТУС КАРТИНИ НА "vendu"
        if (!empty($updatedOrders) && isset($updatedOrders[0])) {
            $oeuvre_id = $updatedOrders[0]['oeuvre_id'];
            
            $ch2 = curl_init(SUPABASE_URL . '/rest/v1/oeuvres?id=eq.' . $oeuvre_id);
            curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode(['statut' => 'vendu']));
            curl_setopt($ch2, CURLOPT_HTTPHEADER, [
                'apikey: ' . SUPABASE_SERVICE_KEY, // Змінено на сервісний ключ
                'Authorization: Bearer ' . SUPABASE_SERVICE_KEY, // Змінено на сервісний ключ
                'Content-Type: application/json'
            ]);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch2);
        }
    }

    $response->getBody()->write(json_encode(['status' => 'success']));
    return $response->withHeader('Content-Type', 'application/json');
});

// =====================================================================
// API РОУТ: ПОВІДОМЛЕННЯ КЛІЄНТУ ПРО ВІДПРАВЛЕННЯ КАРТИНИ (BREVO)
// =====================================================================
$app->post('/api/notify-shipping', function ($request, $response, $args) {
    try {
        if (file_exists(__DIR__ . '/includes/config.php')) {
            require_once __DIR__ . '/includes/config.php';
        } elseif (file_exists(__DIR__ . '/config.php')) {
            require_once __DIR__ . '/config.php';
        }

        if (!defined('BREVO_API_KEY')) {
            throw new Exception("Brevo API Key is missing");
        }

        // Отримуємо дані від JavaScript (з кабінету художника)
        $data = json_decode($request->getBody()->getContents(), true);
        $clientEmail = $data['client_email'] ?? '';
        $clientName = $data['client_name'] ?? 'Client';
        $oeuvreTitre = $data['oeuvre_titre'] ?? 'Œuvre';
        $trackingNumber = $data['tracking_number'] ?? '';
        $artistName = $data['artist_name'] ?? 'L\'artiste';

        if (empty($clientEmail)) {
            throw new Exception("Email клієнта відсутній");
        }

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
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
            
            if(curl_errno($ch)){
                throw new Exception("Помилка cURL: " . curl_error($ch));
            }
            if ($httpCode >= 400) {
                throw new Exception("Помилка Brevo: " . $result);
            }
            curl_close($ch);
            return $result;
        };

        // ДИЗАЙН ЛИСТА ДЛЯ КЛІЄНТА (Темна тема із золотим акцентом)
        $subjectClient = "Votre œuvre de $artistName est en route ! 🚚";
        $htmlClient = "
        <div style='background-color: #0a0a0a; color: #ffffff; padding: 40px; font-family: \"Helvetica Neue\", Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <h1 style='color: #d4af37; font-size: 36px; letter-spacing: 4px; margin: 0;'>EXIT<span style='color: #ffffff;'>.</span></h1>
            </div>
            <div style='background-color: #111111; padding: 35px; border-left: 4px solid #d4af37;'>
                <h2 style='margin-top: 0; font-weight: 300; letter-spacing: 1px;'>Excellente nouvelle !</h2>
                <p style='font-size: 16px; line-height: 1.6;'>Bonjour <strong>$clientName</strong>,</p>
                <p style='font-size: 16px; line-height: 1.6; color: #cccccc;'>L'artiste <strong>$artistName</strong> vient de nous informer que votre œuvre <strong style='color: #ffffff;'>$oeuvreTitre</strong> a été préparée et confiée au transporteur.</p>
                
                <div style='background: rgba(212, 175, 55, 0.1); padding: 20px; border-radius: 4px; margin: 25px 0; border: 1px solid rgba(212, 175, 55, 0.3); text-align: center;'>
                    <p style='margin: 0; color: #d4af37; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;'>Numéro de suivi :</p>
                    <p style='margin: 10px 0 0 0; font-size: 22px; font-weight: bold; letter-spacing: 2px;'>$trackingNumber</p>
                </div>

                <p style='font-size: 16px; line-height: 1.6; color: #cccccc;'>Vous pouvez dès à présent suivre l'acheminement de votre colis. Une fois l'œuvre réceptionnée et validée par vos soins, la transaction sera définitivement clôturée.</p>
                <p style='font-size: 16px; line-height: 1.6; color: #cccccc;'>Si vous avez la moindre question, Sabrina reste à votre entière disposition.</p>
            </div>
            <div style='text-align: center; margin-top: 40px; color: #666666; font-size: 12px;'>
                <p>La Galerie EXIT | Art Contemporain</p>
            </div>
        </div>
        ";

        // Відправляємо лист
        $sendBrevoEmail($clientEmail, $clientName, $subjectClient, $htmlClient);

        // Віддаємо успішну відповідь фронтенду
        $response->getBody()->write(json_encode(['success' => true]));
        return $response->withHeader('Content-Type', 'application/json');

    } catch (\Throwable $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});

$app->run();