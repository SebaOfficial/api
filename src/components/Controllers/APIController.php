<?php

namespace Seba\API\Controllers;

use Bramus\Router\Router;
use Seba\HTTP\ResponseHandler;

abstract class APIController
{
    protected Router $router;
    protected ResponseHandler $response;

    public function __construct(Router $router, ResponseHandler $response)
    {
        $this->router = $router;
        $this->response = $response;
    }

    public function __invoke()
    {
        $this->init();
    }

    abstract public function init(): static;
}
