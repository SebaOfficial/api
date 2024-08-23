<?php

require_once dirname(__DIR__) . "/environment.php";

use Seba\HTTP\{ResponseHandler, IncomingRequestHandler};
use Seba\HTTP\Router\Router;

$request = new IncomingRequestHandler();
$response = new ResponseHandler(200);

$router = new Router($request, $response);

$response->setHeaders([
    'Content-Type' => 'application/json',
    'X-Powered-By' => 'racca.me',
]);

$router->mount('/pay', fn (Router $router) => (new Seba\API\Controllers\PaymentController($router, $response, $request))->init());
$router->mount('/newsletter', fn (Router $router) => (new Seba\API\Controllers\NewsletterController($router, $response, $request))->init());
$router->get('/ping', fn () => $response->setHeaders(['Content-Type' => 'text/plain'])->setBody('pong')->send());

$router->onError(
    404,
    fn () => $response->setHttpCode(404)
                ->setBody([
                    'ok' => false,
                    'error' => 'Resource not found'
                ])
            ->send()
);

$router->run();
