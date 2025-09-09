<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Payment;
use App\Models\PaymentNotification;
use Illuminate\Support\Facades\Log;

/**
 * StripeService for Edulink International College Nairobi
 * 
 * Handles Stripe API integration for credit/debit card payments
 * Manages payment intents, customers, and webhook processing
 */
class StripeService
{
    protected string $secretKey;
    protected string $publishableKey;
    protected string $webhookSecret;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        $this->publishableKey = config('services.stripe.key');
        $this->webhookSecret = config('services.stripe.webhook_secret');
        
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent(
        float $amount,
        string $currency = 'usd',
        array $metadata = [],
        ?string $customerId = null
    ): array {
        try {
            $paymentIntentData = [
                'amount' => $this->convertToSmallestUnit($amount, $currency),
                'currency' => strtolower($currency),
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ];

            if ($customerId) {
                $paymentIntentData['customer'] = $customerId;
            }

            $paymentIntent = PaymentIntent::create($paymentIntentData);

            Log::info('Stripe payment intent created', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $currency
            ]);

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $paymentIntent->status
            ];

        } catch (\Exception $e) {
            Log::error('Stripe payment intent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'currency' => $currency
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment intent: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve a payment intent
     */
    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
                'status' => $paymentIntent->status,
                'amount' => $this->convertFromSmallestUnit($paymentIntent->amount, $paymentIntent->currency),
                'currency' => $paymentIntent->currency
            ];

        } catch (\Exception $e) {
            Log::error('Stripe payment intent retrieval failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to retrieve payment intent: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Confirm a payment intent
     */
    public function confirmPaymentIntent(string $paymentIntentId, array $paymentMethodData = []): array
    {
        try {
            $confirmData = [];
            
            if (!empty($paymentMethodData)) {
                $confirmData['payment_method'] = $paymentMethodData;
            }

            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            $paymentIntent->confirm($confirmData);

            Log::info('Stripe payment intent confirmed', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $paymentIntent->status
            ]);

            return [
                'success' => true,
                'payment_intent' => $paymentIntent,
                'status' => $paymentIntent->status
            ];

        } catch (\Exception $e) {
            Log::error('Stripe payment intent confirmation failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId
            ]);

            return [
                'success' => false,
                'message' => 'Failed to confirm payment intent: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create or retrieve a Stripe customer
     */
    public function createOrRetrieveCustomer(string $email, array $customerData = []): array
    {
        try {
            // First, try to find existing customer by email
            $existingCustomers = Customer::all(['email' => $email, 'limit' => 1]);
            
            if (!empty($existingCustomers->data)) {
                $customer = $existingCustomers->data[0];
                
                Log::info('Stripe customer retrieved', ['customer_id' => $customer->id, 'email' => $email]);
                
                return [
                    'success' => true,
                    'customer' => $customer,
                    'customer_id' => $customer->id,
                    'created' => false
                ];
            }

            // Create new customer
            $customerData['email'] = $email;
            $customer = Customer::create($customerData);

            Log::info('Stripe customer created', ['customer_id' => $customer->id, 'email' => $email]);

            return [
                'success' => true,
                'customer' => $customer,
                'customer_id' => $customer->id,
                'created' => true
            ];

        } catch (\Exception $e) {
            Log::error('Stripe customer creation/retrieval failed', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create/retrieve customer: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function handleWebhook(array $payload, string $signature = null): bool
    {
        try {
            if ($signature && $this->webhookSecret) {
                $event = Webhook::constructEvent(
                    json_encode($payload),
                    $signature,
                    $this->webhookSecret
                );
            } else {
                $event = $payload;
            }

            Log::info('Stripe webhook received', ['type' => $event['type']]);

            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    return $this->handlePaymentIntentSucceeded($event['data']['object']);
                    
                case 'payment_intent.payment_failed':
                    return $this->handlePaymentIntentFailed($event['data']['object']);
                    
                case 'payment_intent.canceled':
                    return $this->handlePaymentIntentCanceled($event['data']['object']);
                    
                default:
                    Log::info('Unhandled Stripe webhook event', ['type' => $event['type']]);
                    return true;
            }

        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return false;
            
        } catch (\Exception $e) {
            Log::error('Stripe webhook handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded(array $paymentIntent): bool
    {
        try {
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
            
            if (!$payment) {
                Log::error('Payment not found for Stripe payment intent', ['payment_intent_id' => $paymentIntent['id']]);
                return false;
            }

            $payment->update([
                'status' => 'completed',
                'processed_at' => now(),
                'stripe_charge_id' => $paymentIntent['charges']['data'][0]['id'] ?? null,
                'gateway_response' => $paymentIntent,
                'is_verified' => true,
                'verified_at' => now(),
            ]);

            // Update enrollment payment status
            $payment->enrollment->updatePaymentStatus($payment->amount);

            // Create payment confirmed notification
            PaymentNotification::createPaymentConfirmed($payment->student, $payment);

            Log::info('Stripe payment completed successfully', ['payment_id' => $payment->id]);
            return true;

        } catch (\Exception $e) {
            Log::error('Stripe payment success handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed(array $paymentIntent): bool
    {
        try {
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
            
            if (!$payment) {
                Log::error('Payment not found for failed Stripe payment intent', ['payment_intent_id' => $paymentIntent['id']]);
                return false;
            }

            $failureReason = $paymentIntent['last_payment_error']['message'] ?? 'Payment failed';
            $payment->markAsFailed($failureReason);

            Log::info('Stripe payment failed', ['payment_id' => $payment->id, 'reason' => $failureReason]);
            return true;

        } catch (\Exception $e) {
            Log::error('Stripe payment failure handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Handle canceled payment intent
     */
    protected function handlePaymentIntentCanceled(array $paymentIntent): bool
    {
        try {
            $payment = Payment::where('stripe_payment_intent_id', $paymentIntent['id'])->first();
            
            if (!$payment) {
                Log::error('Payment not found for canceled Stripe payment intent', ['payment_intent_id' => $paymentIntent['id']]);
                return false;
            }

            $payment->update([
                'status' => 'cancelled',
                'processed_at' => now(),
                'gateway_response' => $paymentIntent,
            ]);

            Log::info('Stripe payment canceled', ['payment_id' => $payment->id]);
            return true;

        } catch (\Exception $e) {
            Log::error('Stripe payment cancellation handling failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Convert amount to smallest currency unit (cents for USD, etc.)
     */
    protected function convertToSmallestUnit(float $amount, string $currency): int
    {
        $zeroDecimalCurrencies = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
        
        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return (int) $amount;
        }
        
        return (int) ($amount * 100);
    }

    /**
     * Convert amount from smallest currency unit
     */
    protected function convertFromSmallestUnit(int $amount, string $currency): float
    {
        $zeroDecimalCurrencies = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
        
        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return (float) $amount;
        }
        
        return $amount / 100;
    }

    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'KES' => 'Kenyan Shilling',
            'UGX' => 'Ugandan Shilling',
            'TZS' => 'Tanzanian Shilling',
        ];
    }

    /**
     * Calculate Stripe fees (approximate)
     */
    public function calculateStripeFees(float $amount, string $currency = 'usd'): array
    {
        $currency = strtolower($currency);
        
        // Stripe fees vary by region and currency
        $feeStructure = match($currency) {
            'usd', 'eur', 'gbp' => ['percentage' => 2.9, 'fixed' => 0.30],
            'kes' => ['percentage' => 3.8, 'fixed' => 0],
            default => ['percentage' => 3.4, 'fixed' => 0],
        };
        
        $percentageFee = ($amount * $feeStructure['percentage']) / 100;
        $totalFee = $percentageFee + $feeStructure['fixed'];
        
        return [
            'percentage_fee' => $percentageFee,
            'fixed_fee' => $feeStructure['fixed'],
            'total_fee' => $totalFee,
            'net_amount' => $amount - $totalFee
        ];
    }

    /**
     * Check if Stripe service is available
     */
    public function isServiceAvailable(): bool
    {
        return !empty($this->secretKey) && !empty($this->publishableKey);
    }

    /**
     * Get publishable key for frontend
     */
    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }
}
