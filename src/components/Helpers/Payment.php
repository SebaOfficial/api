<?php

namespace Seba\API\Helpers;

class Payment
{
    private string $method;
    private int $amount;
    private array $paymentMethods;

    public function __construct(string $method, int $amount)
    {
        $this->method = $method;
        $this->amount = $amount;
    }

    public function getUrl(): string
    {
        if($this->method === 'paypal') {
            return $_ENV['PAYPAL_LINK'] . $this->amount / 100 . "EUR";
        }

        if($this->method === 'satispay') {
            return $_ENV['SATISPAY_LINK'] . "?amount=$this->amount";
        }

        if($this->method === "card") {
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
                        'unit_amount' => $this->amount,
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

    private function getAvailablePaymentMethods(): array
    {
        if(!$this->paymentMethods) {
            $this->paymentMethods = explode('|', $_ENV['PAYMENT_METHODS']);
        }

        return $this->paymentMethods;
    }
}
