<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentIntent($amount, $currency = 'usd', $metadata = [])
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount * 100, // Stripe uses cents
                'currency' => $currency,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment intent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}