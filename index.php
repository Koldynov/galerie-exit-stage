<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

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

$app->get('/', function (Request $request, Response $response) { return render($response, 'home.php'); });
$app->get('/peintures', function (Request $request, Response $response) { return render($response, 'peintures.php'); });
$app->get('/sculptures', function (Request $request, Response $response) { return render($response, 'sculptures.php'); });
$app->get('/photographies', function (Request $request, Response $response) { return render($response, 'photographies.php'); });
$app->get('/artistes', function (Request $request, Response $response) { return render($response, 'artistes.php'); });
$app->get('/journal', function (Request $request, Response $response) { return render($response, 'actualites.php'); });

$app->get('/oeuvre/{id}', function (Request $request, Response $response, array $args) { return render($response, 'oeuvre.php', ['item_id' => $args['id']]); });
$app->get('/artiste/{id}', function (Request $request, Response $response, array $args) { return render($response, 'profil-artiste.php', ['item_id' => $args['id']]); });

$app->get('/portail-artiste', function (Request $request, Response $response) { return render($response, 'portail-artiste.php'); });
$app->get('/studio', function (Request $request, Response $response) { return render($response, 'espace-artiste.php'); });
$app->get('/mon-compte', function (Request $request, Response $response) { return render($response, 'mon-compte.php'); });
$app->get('/dashboard', function (Request $request, Response $response) { return render($response, 'dashboard.php'); });

$app->get('/paiement/{id}', function (Request $request, Response $response, array $args) { return render($response, 'paiement.php', ['item_id' => $args['id']]); });
$app->get('/facture/{id}', function (Request $request, Response $response, array $args) { return render($response, 'facture.php', ['item_id' => $args['id']]); });
$app->get('/certificat/{id}', function (Request $request, Response $response, array $args) { return render($response, 'certificat.php', ['item_id' => $args['id']]); });

$app->run();