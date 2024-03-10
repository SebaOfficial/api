<?php

namespace Seba\API\Controllers;

use Seba\HTTP\{ResponseHandler, IncomingRequestHandler, Router\Router};

abstract class APIController
{
    protected Router $router;
    protected ResponseHandler $response;
    protected IncomingRequestHandler $request;

    public function __construct(Router $router, ResponseHandler $response, IncomingRequestHandler $request)
    {
        $this->router = $router;
        $this->response = $response;
        $this->request = $request;
    }

    public function __invoke()
    {
        $this->init();
    }

    abstract public function init(): static;
}
