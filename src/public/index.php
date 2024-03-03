<?php

require_once dirname(__DIR__) . "/environment.php";

use Bramus\Router\Router;
use Seba\HTTP\ResponseHandler;

$router = new Router();
$response = new ResponseHandler(200);

$response->setHeaders([
    'Content-Type: application/json',
    'X-Powered-By: racca.me',
]);

$router->set404(
    fn () =>
    $response->setHttpCode(404)
        ->setBody([
            'ok' => false,
            'error' => 'Resource not found'
        ])->send()
);

$router->mount('/pay', new Seba\API\Controllers\PaymentController($router, $response));

$router->run();
