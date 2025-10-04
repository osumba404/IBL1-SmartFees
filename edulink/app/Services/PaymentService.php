<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\Payment;
use App\Models\PaymentNotification;
use App\Services\MpesaService;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PaymentService for Edulink International College Nairobi
 * 
 * Coordinates payment processing across different gateways
 * Handles payment creation, verification, and notifications
 */
class PaymentService
{
    protected MpesaService $mpesaService;
    protected StripeService $stripeService;

    public function __construct(MpesaService $mpesaService, StripeService $stripeService)
    {
        $this->mpesaService = $mpesaService;
        $this->stripeService = $stripeService;
    }

    /**
     * Process payment based on method
     */
    public function processPayment(
        StudentEnrollment $enrollment,
        float $amount,
        string $paymentMethod,
        array $paymentData = []
    ): array {
        try {
            DB::beginTransaction();

            // Create payment record
            $payment = $this->createPaymentRecord($enrollment, $amount, $paymentMethod, $paymentData);

            // Process payment based on method
            $result = match($paymentMethod) {
                'mpesa' => $this->processMpesaPayment($payment, $paymentData),
                'stripe' => $this->processStripePayment($payment, $paymentData),
                'bank_transfer' => $this->processBankTransferPayment($payment, $paymentData),
                'cash' => $this->processCashPayment($payment, $paymentData),
                default => throw new \InvalidArgumentException("Unsupported payment method: {$paymentMethod}")
            };

            if ($result['success']) {
                // Create payment received notification
                PaymentNotification::createPaymentReceived($enrollment->student, $payment);
                
                DB::commit();
                
                Log::info('Payment processed successfully', [
                    'payment_id' => $payment->id,
                    'student_id' => $enrollment->student_id,
                    'amount' => $amount,
                    'method' => $paymentMethod
                ]);
            } else {
                DB::rollBack();
                
                Log::error('Payment processing failed', [
                    'payment_id' => $payment->id,
                    'error' => $result['message']
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment processing exception', [
                'error' => $e->getMessage(),
                'enrollment_id' => $enrollment->id,
                'amount' => $amount,
                'method' => $paymentMethod
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'payment' => null
            ];
        }
    }

    /**
     * Create payment record in database
     */
    protected function createPaymentRecord(
        StudentEnrollment $enrollment,
        float $amount,
        string $paymentMethod,
        array $paymentData
    ): Payment {
        return Payment::create([
            'student_id' => $enrollment->student_id,
            'student_enrollment_id' => $enrollment->id,
            'payment_reference' => Payment::generatePaymentReference(),
            'transaction_id' => $paymentData['transaction_id'] ?? uniqid('txn_'),
            'amount' => $amount,
            'currency' => $paymentData['currency'] ?? 'KES',
            'payment_method' => $paymentMethod,
            'payment_type' => $paymentData['payment_type'] ?? 'tuition',
            'payment_date' => now(),
            'outstanding_balance_before' => $enrollment->outstanding_balance,
            'status' => 'pending',
        ]);
    }

    /**
     * Process M-Pesa payment
     */
    protected function processMpesaPayment(Payment $payment, array $paymentData): array
    {
        try {
            $result = $this->mpesaService->initiateSTKPush(
                $paymentData['phone_number'],
                $payment->amount,
                $payment->payment_reference,
                "Fee payment for {$payment->enrollment->course->name}"
            );

            if ($result['success']) {
                $payment->update([
                    'gateway_transaction_id' => $result['checkout_request_id'],
                    'gateway_response' => $result,
                    'mpesa_phone_number' => $paymentData['phone_number'],
                    'status' => 'processing'
                ]);

                return [
                    'success' => true,
                    'message' => 'M-Pesa payment initiated. Please check your phone for the payment prompt.',
                    'payment' => $payment,
                    'checkout_request_id' => $result['checkout_request_id']
                ];
            }

            $payment->markAsFailed($result['message']);
            return $result;

        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment(Payment $payment, array $paymentData): array
    {
        try {
            $result = $this->stripeService->createPaymentIntent(
                $payment->amount,
                $payment->currency,
                [
                    'student_id' => $payment->student_id,
                    'enrollment_id' => $payment->student_enrollment_id,
                    'payment_reference' => $payment->payment_reference,
                ]
            );

            if ($result['success']) {
                $payment->update([
                    'stripe_payment_intent_id' => $result['payment_intent_id'],
                    'gateway_response' => $result,
                    'status' => 'processing'
                ]);

                return [
                    'success' => true,
                    'message' => 'Stripe payment intent created successfully.',
                    'payment' => $payment,
                    'client_secret' => $result['client_secret']
                ];
            }

            $payment->markAsFailed($result['message']);
            return $result;

        } catch (\Exception $e) {
            $payment->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Process bank transfer payment
     */
    protected function processBankTransferPayment(Payment $payment, array $paymentData): array
    {
        $payment->update([
            'bank_name' => $paymentData['bank_name'] ?? null,
            'bank_reference' => $paymentData['bank_reference'] ?? null,
            'bank_transaction_date' => $paymentData['transaction_date'] ?? now(),
            'status' => 'pending', // Requires manual verification
        ]);

        return [
            'success' => true,
            'message' => 'Bank transfer payment recorded. It will be verified by our finance team.',
            'payment' => $payment
        ];
    }

    /**
     * Process cash payment
     */
    protected function processCashPayment(Payment $payment, array $paymentData): array
    {
        $payment->update([
            'receipt_number' => $paymentData['receipt_number'] ?? null,
            'status' => 'pending', // Requires manual verification
        ]);

        return [
            'success' => true,
            'message' => 'Cash payment recorded. It will be verified by our finance team.',
            'payment' => $payment
        ];
    }

    /**
     * Verify M-Pesa payment callback
     */
    public function verifyMpesaCallback(array $callbackData): bool
    {
        try {
            $checkoutRequestId = $callbackData['CheckoutRequestID'] ?? null;
            
            if (!$checkoutRequestId) {
                Log::error('M-Pesa callback missing CheckoutRequestID', $callbackData);
                return false;
            }

            $payment = Payment::where('gateway_transaction_id', $checkoutRequestId)->first();
            
            if (!$payment) {
                Log::error('Payment not found for M-Pesa callback', ['checkout_request_id' => $checkoutRequestId]);
                return false;
            }

            if ($callbackData['ResultCode'] == 0) {
                // Payment successful
                $callbackBody = $callbackData['CallbackMetadata']['Item'] ?? [];
                $mpesaData = $this->parseMpesaCallbackData($callbackBody);

                $payment->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'mpesa_receipt_number' => $mpesaData['receipt_number'],
                    'mpesa_transaction_date' => $mpesaData['transaction_date'],
                    'mpesa_transaction_cost' => $mpesaData['transaction_cost'],
                    'gateway_response' => $callbackData,
                    'is_verified' => true,
                    'verified_at' => now(),
                ]);

                // Update enrollment payment status
                $payment->enrollment->updatePaymentStatus($payment->amount);

                // Create payment confirmed notification
                PaymentNotification::createPaymentConfirmed($payment->student, $payment);

                Log::info('M-Pesa payment verified successfully', ['payment_id' => $payment->id]);
                return true;

            } else {
                // Payment failed
                $payment->markAsFailed($callbackData['ResultDesc'] ?? 'M-Pesa payment failed');
                Log::info('M-Pesa payment failed', ['payment_id' => $payment->id, 'reason' => $callbackData['ResultDesc']]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('M-Pesa callback verification failed', ['error' => $e->getMessage(), 'data' => $callbackData]);
            return false;
        }
    }

    /**
     * Parse M-Pesa callback data
     */
    protected function parseMpesaCallbackData(array $callbackItems): array
    {
        $data = [];
        
        foreach ($callbackItems as $item) {
            switch ($item['Name']) {
                case 'MpesaReceiptNumber':
                    $data['receipt_number'] = $item['Value'];
                    break;
                case 'TransactionDate':
                    $data['transaction_date'] = $item['Value'];
                    break;
                case 'TransactionCost':
                    $data['transaction_cost'] = $item['Value'];
                    break;
            }
        }

        return $data;
    }

    /**
     * Verify Stripe webhook
     */
    public function verifyStripeWebhook(array $webhookData): bool
    {
        try {
            return $this->stripeService->handleWebhook($webhookData);
        } catch (\Exception $e) {
            Log::error('Stripe webhook verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get payment methods available for student
     */
    public function getAvailablePaymentMethods(Student $student): array
    {
        $methods = [
            'mpesa' => [
                'name' => 'M-Pesa',
                'description' => 'Pay using M-Pesa mobile money',
                'enabled' => config('services.mpesa.enabled', true),
                'icon' => 'mpesa-icon'
            ],
            'stripe' => [
                'name' => 'Credit/Debit Card',
                'description' => 'Pay using Visa, Mastercard, or other cards',
                'enabled' => config('services.stripe.enabled', true),
                'icon' => 'card-icon'
            ],
            'bank_transfer' => [
                'name' => 'Bank Transfer',
                'description' => 'Transfer funds directly to our bank account',
                'enabled' => true,
                'icon' => 'bank-icon'
            ]
        ];

        return array_filter($methods, fn($method) => $method['enabled']);
    }

    /**
     * Verify a payment manually
     */
    public function verifyPayment(int $paymentId, array $verificationData = []): array
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            if ($payment->is_verified) {
                return [
                    'success' => false,
                    'message' => 'Payment is already verified.'
                ];
            }
            
            $payment->update([
                'is_verified' => true,
                'verified_at' => now(),
                'verified_by' => $verificationData['verified_by'] ?? null,
                'admin_notes' => $verificationData['verification_notes'] ?? null,
                'status' => 'completed',
                'processed_at' => now(),
            ]);
            
            // Update enrollment payment status if enrollment exists
            if ($payment->enrollment) {
                $payment->enrollment->updatePaymentStatus($payment->amount);
            }
            
            return [
                'success' => true,
                'message' => 'Payment verified successfully.'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process payment refund
     */
    public function refundPayment(int $paymentId, array $refundData): array
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            if ($payment->status !== 'completed') {
                return [
                    'success' => false,
                    'message' => 'Only completed payments can be refunded.'
                ];
            }
            
            if ($payment->is_refunded) {
                return [
                    'success' => false,
                    'message' => 'Payment has already been refunded.'
                ];
            }
            
            $refundAmount = $refundData['refund_amount'];
            if ($refundAmount > $payment->amount) {
                return [
                    'success' => false,
                    'message' => 'Refund amount cannot exceed payment amount.'
                ];
            }
            
            $payment->update([
                'is_refunded' => true,
                'refund_amount' => $refundAmount,
                'refund_reason' => $refundData['refund_reason'],
                'refund_reference' => 'REF' . strtoupper(uniqid()),
                'refunded_at' => now(),
                'status' => 'refunded',
            ]);
            
            // Update enrollment payment status (subtract refunded amount)
            if ($payment->enrollment) {
                $payment->enrollment->updatePaymentStatus(-$refundAmount);
            }
            
            return [
                'success' => true,
                'message' => 'Payment refunded successfully.'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Payment refund failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate payment breakdown
     */
    public function calculatePaymentBreakdown(StudentEnrollment $enrollment, float $amount): array
    {
        $breakdown = [];
        $remaining = $amount;
        $outstandingBalance = $enrollment->outstanding_balance;

        // Allocate payment to different fee types based on priority
        $feeTypes = [
            'tuition' => min($remaining, $outstandingBalance * 0.7), // 70% to tuition
            'registration' => min($remaining * 0.1, $outstandingBalance * 0.1), // 10% to registration
            'examination' => min($remaining * 0.1, $outstandingBalance * 0.1), // 10% to examination
            'library' => min($remaining * 0.05, $outstandingBalance * 0.05), // 5% to library
            'activity' => min($remaining * 0.05, $outstandingBalance * 0.05), // 5% to activity
        ];

        foreach ($feeTypes as $type => $allocatedAmount) {
            if ($allocatedAmount > 0 && $remaining > 0) {
                $actualAmount = min($allocatedAmount, $remaining);
                $breakdown[$type] = $actualAmount;
                $remaining -= $actualAmount;
            }
        }

        return $breakdown;
    }
}
