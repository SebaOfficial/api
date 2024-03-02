<?php

namespace Seba\API\Routes;

use Seba\HTTP\{ResponseHandler, IncomingRequestHandler};

class Pay implements RouteInterface
{
    private static function getPaymentUrl(string $method, int $amount): string
    {
        if($method === 'paypal') {
            return $_ENV['PAYPAL_LINK'] . $amount / 100 . "EUR";
        }

        if($method === 'satispay') {
            return $_ENV['SATISPAY_LINK'] . "?amount=$amount";
        }

        if($method === "card") {
            $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SECRET_KEY']);
            $session = $stripe->checkout->sessions->create([
                'success_url' => "https://" . $_SERVER['SERVER_NAME'],
                'line_items' => [
                    [
                      'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                          'name' => 'Checkout Session',
                        ],
                        'unit_amount' => $amount,
                      ],
                      'quantity' => 1,
                    ],
                  ],
                'mode' => 'payment',
            ]);

            return $session->url;

        }

        return "";
    }

    public static function exec(IncomingRequestHandler $request, ResponseHandler $response, ?array $params = null): void
    {
        $amount = $params[1];
        $url = self::getPaymentUrl($params[0], $amount);

        if(filter_var($amount, FILTER_VALIDATE_INT) === false) {
            $response->setHttpCode(400)
                ->setBody([
                    'ok' => false,
                    'error' => 'The amount must be an integer.'
                ])
            ->send();
        }

        $response->setHttpCode(201)
            ->setBody([
                'ok' => true,
                'url' => $url
            ])
            ->setHeaders([
                "Location: $url"
            ])
        ->send();
    }
}
