<?php

namespace Seba\API\Routes;

use Seba\HTTP\{ResponseHandler, IncomingRequestHandler};

/**
 * Interface RouteInterface
 * Defines the contract for implementing routes in the API.
 */
interface RouteInterface
{
    /**
     * Executes the route logic based on the incoming request and provides a response.
     *
     * @param IncomingRequestHandler $request The incoming request handler.
     * @param ResponseHandler $response The response handler.
     * @param array|null $params Optional parameters for the route.
     * @return void
     */
    public static function exec(IncomingRequestHandler $request, ResponseHandler $response, ?array $params = null): void;
}
