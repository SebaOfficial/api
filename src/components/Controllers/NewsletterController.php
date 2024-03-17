<?php

namespace Seba\API\Controllers;

use Seba\API\Helpers\{NewsletterHelper, Utils};
use Seba\API\Properties\RequestError;
use Seba\HTTP\Router\Router;

class NewsletterController extends APIController
{
    public function init(): static
    {
        // ALL /newsletter
        $this->router->mount('/?', function (Router $router) {

            // POST /newsletter/sub
            $router->post('/sub/?', fn () => $this->newSub());


            // DELETE /newsletter/sub/
            $router->delete('/sub/?', fn () => $this->unsub(NewsletterHelper::getToken($this->response)));
            // GET /newsletter/unsub/:token
            $router->get('/unsub/([^/]+)/?', fn ($token) => $this->unsub($token));
        });

        // POST /newsletter/post
        $this->router->post('/post/?', fn () => $this->newPost());

        return $this;
    }

    private function newSub(): void
    {
        $email = $this->getRequiredParams(['email'])['email'];

        if(!NewsletterHelper::isValidEmail($email)) {
            $this->response->setHttpCode(400)
                ->setBody([
                    'ok' => false,
                    'error' => 'Invalid email format.',
                ])
            ->send();
        }

        $token = NewsletterHelper::getNewToken();

        $res = NewsletterHelper::getNewsletterDb()->query(function (\PDO $pdo) use ($email, $token) {
            $stmt = $pdo->prepare('INSERT INTO subs (email, token) VALUES (:email, :token)');
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':token', $token);

            return (object)['ok' => $stmt->execute()];
        });

        if(!$res->ok) {
            error_log($res->error);
            $this->response->setHttpCode(409)
                ->setBody([
                    'ok' => false,
                    'error' => 'The user is already subscribed.',
                ])
            ->send();
        }

        $this->response->setHttpCode(201)
            ->setBody([
                'ok' => true,
            ])
        ->send();
    }

    private function unsub(string $token): void
    {
        if($token == null) {
            $this->missingToken();
        }

        $res = NewsletterHelper::getNewsletterDb()->query(function (\PDO $pdo) use ($token) {
            $stmt = $pdo->prepare('DELETE FROM subs WHERE token=:token');
            $stmt->bindParam(':token', $token);

            return (object)['ok' => $stmt->execute() && $stmt->rowCount() === 1];
        });

        if(!$res->ok) {
            $this->response->setHttpCode(401)
                ->setBody([
                    'ok' => false,
                    'error' => 'The provided auth token is invalid.',
                ])
                ->setHeaders([$this->getWWWAuthenticateHeader('invalid_token', 'The provided auth token is invalid')])
            ->send();
        }

        $this->response->setHttpCode(200)
            ->setBody([
                'ok' => true,
            ])
        ->send();
    }

    private function missingToken(): void
    {
        $this->response->setHttpCode(401)
            ->setBody([
                'ok' => false,
                'error' => 'Must specify the auth token.',
            ])
            ->setHeaders([$this->getWWWAuthenticateHeader('missing_token', 'Must specify the auth token')])
        ->send();
    }


    private function newPost(): void
    {
        $token = NewsletterHelper::getToken($this->response);

        if($token === null) {
            $this->missingToken();
        }

        $adm_token = file_get_contents(Utils::getAdminPasswordPath());

        if(!password_verify($token, $adm_token)) {
            $this->response->setHttpCode(401)
                ->setBody([
                    'ok' => false,
                    'error' => 'The provided auth token is invalid.',
                ])
                ->setHeaders([$this->getWWWAuthenticateHeader('invalid_token', 'The provided auth token is invalid')])
            ->send();
        }

        $params = $this->getRequiredParams(['subject', 'body', 'alt_body']);

        $res = NewsletterHelper::getNewsletterDb()->query(function (\PDO $pdo) use ($params) {
            $stmt = $pdo->prepare('INSERT INTO posts_queue (subject, body, alt_body) VALUES (:subject, :body, :alt_body)');
            $stmt->bindParam(':subject', $params['subject']);
            $stmt->bindParam(':body', $params['body']);
            $stmt->bindParam(':alt_body', $params['alt_body']);

            return (object)['ok' => $stmt->execute()];
        });

        if(!$res->ok) {
            $this->response->setHttpCode(500)
                ->setBody([
                    'ok' => false,
                    'error' => 'An error occurred: ' . $res->error,
                ])
            ->send();
        }

        $this->response->setHttpCode(201)
            ->setBody([
                'ok' => true,
            ])
        ->send();
    }

    private function getWWWAuthenticateHeader(string $error, string $errorDescription): string
    {
        return "WWW-Authenticate: Bearer, error=\"$error\", error_description=\"$errorDescription\"";
    }

    private function getRequiredParams(array $requiredParams): array
    {
        $params = NewsletterHelper::getParams($this->request, $requiredParams);

        if(!is_array($params)) {
            switch($params) {
                case RequestError::INVALID_BODY:
                    $error = 'Invalid request body. Please check your request and try again.';
                    break;

                case RequestError::INVALID_CONTENT_TYPE_HEADER:
                    $error = 'Unsupported Content-Type. Please ensure your request includes a valid Content-Type header.';
                    break;

                case RequestError::NO_REQUIRED_PARAMS:
                    $error = 'You must specify all the required params in the request.';
                    break;
            }

            $body = [
                'ok' => false,
                'error' => $error,
            ];

            if ($params === RequestError::NO_REQUIRED_PARAMS) {
                $body['required_params'] = $requiredParams;
            }

            $this->response->setHttpCode(400)->setBody($body)->send();
        }

        return $params;
    }
}
