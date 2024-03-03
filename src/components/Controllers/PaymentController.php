<?php

namespace Seba\API\Controllers;

use Seba\HTTP\ResponseHandler;

class PaymentController extends APIController
{
    public function init(): static
    {
        $newPaymentRegex = '/(' . $_ENV['PAYMENT_METHODS'] . ')/(\d+)';

        // GET /pay
        $this->router->get('/', fn () => $this->getIndex());
        // OPTIONS /pay
        $this->router->options('/', fn () => $this->optionsIndex());

        // GET|POST /pay/:payment_method/:amount
        $this->router->match('GET|POST', $newPaymentRegex, fn ($paymentMethod, $amount) => $this->newPayment($paymentMethod, $amount));
        // OPTIONS /pay/:payment_method/:amount
        $this->router->options($newPaymentRegex, fn () => $this->optionsNewPayment());

        // Not Found
        $this->router->set404('/(/.*)?', fn () => $this->set404());

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
            'Access-Control-Allow-Origin: *',
            'Access-Control-Max-Age: 172800', // 48 hours
            'Etag: ' . md5($this->router->getRequestMethod(). $this->router->getCurrentUri()),
            'Access-Control-Allow-Methods: GET, POST, OPTIONS',
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
                'Location: ' . $url,
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
            'Access-Control-Allow-Origin: *',
            'Access-Control-Max-Age: 86400', // 24 hours
            'Etag: ' . md5($this->router->getRequestMethod(). $this->router->getCurrentUri()),
            'Access-Control-Allow-Methods: GET, POST, OPTIONS',
        ]);
    }

    private function set404(): void
    {
        $this->response->setHttpCode(404)
            ->setBody([
                'ok' => false,
                'available_payment_methods' => explode('|', $_ENV['PAYMENT_METHODS']),
                'error' => 'Payment method not found',
            ])
        ->send();
    }

}
