<?php

namespace Seba\API\Properties;

/**
 * Represents different types of request errors.
 *
 * @package Seba\API\Properties
 * @author Sebastiano Racca <sebastiano@racca.me>
 */
enum RequestError: int
{
    /**
     * Indicates that required parameters are missing in the request.
    */
    case NO_REQUIRED_PARAMS = 0;

    /**
     * Indicates that the body of the request is invalid.
     */
    case INVALID_BODY = 1;

    /**
     * Indicates that the Content-Type header of the request is invalid.
     */
    case INVALID_CONTENT_TYPE_HEADER = 2;
}
