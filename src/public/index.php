<?php

require_once dirname(__DIR__) . "/environment.php";

use Bramus\Router\Router;
use Seba\HTTP\{ResponseHandler, IncomingRequestHandler};

$router = new Router();
$response = new ResponseHandler(200);
$request = new IncomingRequestHandler();

$response->setHeaders([
    'Content-Type: application/json',
    'X-Powered-By: racca.me',
    'Access-Control-Allow-Origin: *',
]);

$router->set404(function () use ($response) {
    $response->setHttpCode(404)
        ->setBody([
            'ok' => false,
            'error' => 'Resource not found'
        ])->send();
});


$router->mount('/pay', function () use ($router, $response, $request) {
    $response->setHeaders([
        'Access-Control-Max-Age: 86400',
        'Etag: ' . md5($router->getCurrentUri()),
    ]);

    $router->get('/', function () use ($response) {
        $response->setHttpCode(200)
            ->setBody([
                'available_payment_methods' => explode('|', $_ENV['PAYMENT_METHODS'])
            ])
        ->send();
    });

    $router->options('/', function () use ($response) {
        $response->setHttpCode(204)
            ->setHeaders([
                'Access-Control-Allow-Methods: GET, OPTIONS',
            ])
        ->send();
    });

    $newPaymentRegex = '/(' . $_ENV['PAYMENT_METHODS'] . ')/(\d+)';
    $router->match('GET|POST', $newPaymentRegex, function ($paymentMethod, $amount) use ($response, $request) {
        Seba\API\Routes\Pay::exec($request, $response, [$paymentMethod, $amount]);
    });

    $router->options($newPaymentRegex, function () use ($response) {
        $response->setHttpCode(204)
            ->setHeaders([
                'Access-Control-Allow-Methods: GET, POST, OPTIONS',
            ])
        ->send();
    });

    $router->set404('(/.*)?', function () use ($response) {
        $response->setHttpCode(404)
            ->setBody([
                'ok' => false,
                'available_payment_methods' => explode('|', $_ENV['PAYMENT_METHODS']),
                'error' => 'Payment method not found'
            ])
        ->send();
    });
});


$router->run();
