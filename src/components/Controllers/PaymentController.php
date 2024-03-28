<?php

namespace Seba\API\Controllers;

use Seba\HTTP\ResponseHandler;
use Seba\HTTP\Router\RequestedMethods;
use Seba\HTTP\Router\Router;

class PaymentController extends APIController
{
    public function init(): static
    {
        // GET /pay
        $this->router->get('/?', fn () => $this->getIndex());
        // OPTIONS /pay
        $this->router->options('/?', fn () => $this->optionsIndex());

        // ALL /pay/:payment_method
        $this->router->mount('/(' . $_ENV['PAYMENT_METHODS'] . ')/?', function (Router $router) {

            // GET|POST /pay/:payment_method/:amount
            $router->match(
                RequestedMethods::GET || RequestedMethods::POST,
                '/(\d+)',
                fn ($paymentMethod, $amount) => $this->newPayment($paymentMethod, $amount)
            );

            $router->options('/(\d+)', fn () => $this->optionsNewPayment());

            // Amount is not a number
            $router->onError(404, fn () => $this->amountNotANumber());
        });

        // Payment method doesn't exist
        $this->router->onError(404, fn () => $this->paymentMethodNotExist());

        return $this;
    }

    private function getIndex(): void
    {
        $this->setIndexHeaders()->setHttpCode(200)
            ->setBody([
                'ok' => true,
                'available_payment_methods' => explode('|', $_ENV['PAYMENT_METHODS']),
            ])
        ->send();
    }

    private function optionsIndex(): void
    {
        $this->setIndexHeaders()->setHttpCode(204)->send();
    }

    private function setIndexHeaders(): ResponseHandler
    {
        return $this->response->setHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Max-Age' => '172800', // 48 hours
            'Etag' => md5($this->request->getMethod(). $this->request->getUri()),
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        ]);
    }

    private function newPayment($paymentMethod, $amount): void
    {
        $url = (new \Seba\API\Helpers\Payment($paymentMethod, $amount))->getUrl();

        $this->setNewPaymentHeaders()->setHttpCode(201)
            ->setBody([
                'ok' => true,
                'url' => $url,
            ])
            ->setHeaders([
                'Location' => $url,
            ])
        ->send();
    }

    private function optionsNewPayment(): void
    {
        $this->setNewPaymentHeaders()->setHttpCode(204)->send();
    }

    private function setNewPaymentHeaders(): ResponseHandler
    {
        return $this->response->setHeaders([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Max-Age' => '86400', // 24 hours
            'Etag' => md5($this->request->getMethod(). $this->request->getUri()),
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
        ]);
    }

    private function paymentMethodNotExist(): void
    {
        $this->response->setHttpCode(404)
            ->setBody([
                'ok' => false,
                'available_payment_methods' => explode('|', $_ENV['PAYMENT_METHODS']),
                'error' => 'Payment method not found',
            ])
        ->send();
    }

    private function amountNotANumber(): void
    {
        $this->response->setHttpCode(400)
            ->setBody([
                'ok' => false,
                'error' => 'Amount must be an integer',
            ])
        ->send();
    }

}
