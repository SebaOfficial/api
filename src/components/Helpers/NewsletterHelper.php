<?php

namespace Seba\API\Helpers;

use Seba\API\Database;
use Seba\API\Properties\RequestError;
use Seba\HTTP\{IncomingRequestHandler, Authenticator, ResponseHandler};
use Seba\HTTP\Exceptions\{InvalidBodyException, InvalidContentTypeException};

class NewsletterHelper
{
    /**
     * Get an instance of the Newsletter's database.
     *
     * @return Database The instance.
     */
    public static function getNewsletterDb(): Database
    {
        return Database::getInstance(str_replace('{__DIR__}', \ROOT_DIR, $_ENV['NEWSLETTER_DB']));
    }

    /**
     * Check if the given email has valid sintax.
     *
     * @param string $email The email.
     *
     * @return bool Whether the email is valid or not.
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Returns the params of the request.
     *
     * @param IncomingRequestHandler $request The incoming request handler.
     * @param array $requiredParams The required params.
     *
     * @return array|RequestError The array of the required params of a RequestError on error.
     */
    public static function getParams(IncomingRequestHandler $request, array $requiredParams): array|RequestError
    {
        try {
            $params = $request->getRequiredParams($requiredParams);
            return $params ? $params : RequestError::NO_REQUIRED_PARAMS;
        } catch (InvalidBodyException $e) {
            return RequestError::INVALID_BODY;
        } catch (InvalidContentTypeException $e) {
            return RequestError::INVALID_CONTENT_TYPE_HEADER;
        }
    }

    public static function getNewToken(): string
    {
        return Utils::GUIDv4();
    }

    public static function getToken(ResponseHandler $response): ?string
    {
        return (new Authenticator($response, Authenticator::AUTH_BEARER))->getBearerToken();
    }
}
