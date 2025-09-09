<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentEnrollment;
use App\Services\PaymentService;
use App\Services\MpesaService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected MpesaService $mpesaService;
    protected StripeService $stripeService;

    public function __construct(
        PaymentService $paymentService,
        MpesaService $mpesaService,
        StripeService $stripeService
    ) {
        $this->paymentService = $paymentService;
        $this->mpesaService = $mpesaService;
        $this->stripeService = $stripeService;
    }

    /**
     * Initiate a payment
     */
    public function initiate(Request $request): RedirectResponse
    {
        $student = Auth::guard('student')->user();

        $request->validate([
            'enrollment_id' => 'required|exists:student_enrollments,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:mpesa,stripe,bank_transfer,cash',
            'phone' => 'required_if:payment_method,mpesa|string|max:20',
            'fee_breakdown' => 'nullable|array',
        ]);

        // Verify enrollment belongs to student
        $enrollment = StudentEnrollment::where('id', $request->enrollment_id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        // Check if payment amount is valid
        if ($request->amount > $enrollment->amount_pending) {
            return back()->withErrors([
                'amount' => 'Payment amount cannot exceed pending amount of ' . 
                           number_format($enrollment->amount_pending, 2)
            ]);
        }

        try {
            DB::beginTransaction();

            // Process payment based on method
            switch ($request->payment_method) {
                case 'mpesa':
                    $result = $this->processMpesaPayment($student, $enrollment, $request);
                    break;
                    
                case 'stripe':
                    $result = $this->processStripePayment($student, $enrollment, $request);
                    break;
                    
                case 'bank_transfer':
                    $result = $this->processBankTransferPayment($student, $enrollment, $request);
                    break;
                    
                case 'cash':
                    $result = $this->processCashPayment($student, $enrollment, $request);
                    break;
                    
                default:
                    throw new \Exception('Invalid payment method');
            }

            DB::commit();

            if ($result['success']) {
                return redirect()->route('student.payments.success')
                    ->with('payment_id', $result['payment_id'])
                    ->with('success', $result['message']);
            } else {
                return back()->withErrors(['payment' => $result['message']]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initiation failed', [
                'student_id' => $student->id,
                'enrollment_id' => $request->enrollment_id,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors(['payment' => 'Payment initiation failed. Please try again.']);
        }
    }

    /**
     * Process M-Pesa payment
     */
    protected function processMpesaPayment($student, $enrollment, $request): array
    {
        // Create payment record
        $payment = $this->paymentService->createPayment([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'amount' => $request->amount,
            'payment_method' => 'mpesa',
            'phone' => $request->phone,
            'fee_breakdown' => $request->fee_breakdown ?? [],
        ]);

        // Initiate M-Pesa STK Push
        $mpesaResult = $this->mpesaService->initiateSTKPush(
            $request->phone,
            $request->amount,
            "Fee payment for {$enrollment->course->name}",
            $payment->id
        );

        if ($mpesaResult['success']) {
            $payment->update([
                'mpesa_checkout_request_id' => $mpesaResult['checkout_request_id'],
                'gateway_response' => $mpesaResult,
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'message' => 'M-Pesa payment initiated. Please complete the payment on your phone.'
            ];
        } else {
            $payment->markAsFailed($mpesaResult['message']);
            
            return [
                'success' => false,
                'message' => $mpesaResult['message']
            ];
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment($student, $enrollment, $request): array
    {
        // Create or retrieve Stripe customer
        $customerResult = $this->stripeService->createOrRetrieveCustomer(
            $student->email,
            [
                'name' => $student->getFullNameAttribute(),
                'phone' => $student->phone,
                'metadata' => [
                    'student_id' => $student->student_id,
                    'enrollment_id' => $enrollment->id,
                ]
            ]
        );

        if (!$customerResult['success']) {
            return [
                'success' => false,
                'message' => 'Failed to create customer profile: ' . $customerResult['message']
            ];
        }

        // Create payment record
        $payment = $this->paymentService->createPayment([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'amount' => $request->amount,
            'payment_method' => 'stripe',
            'fee_breakdown' => $request->fee_breakdown ?? [],
        ]);

        // Create Stripe payment intent
        $paymentIntentResult = $this->stripeService->createPaymentIntent(
            $request->amount,
            config('services.stripe.currency', 'usd'),
            [
                'student_id' => $student->student_id,
                'enrollment_id' => $enrollment->id,
                'payment_id' => $payment->id,
                'course' => $enrollment->course->name,
            ],
            $customerResult['customer_id']
        );

        if ($paymentIntentResult['success']) {
            $payment->update([
                'stripe_payment_intent_id' => $paymentIntentResult['payment_intent_id'],
                'stripe_customer_id' => $customerResult['customer_id'],
                'gateway_response' => $paymentIntentResult,
            ]);

            // Store client secret in session for frontend
            session(['stripe_client_secret' => $paymentIntentResult['client_secret']]);
            session(['payment_id' => $payment->id]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'message' => 'Payment initiated. You will be redirected to complete the payment.',
                'redirect_url' => route('student.payments.stripe-checkout', ['payment' => $payment->id])
            ];
        } else {
            $payment->markAsFailed($paymentIntentResult['message']);
            
            return [
                'success' => false,
                'message' => $paymentIntentResult['message']
            ];
        }
    }

    /**
     * Process bank transfer payment
     */
    protected function processBankTransferPayment($student, $enrollment, $request): array
    {
        $payment = $this->paymentService->createPayment([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'amount' => $request->amount,
            'payment_method' => 'bank_transfer',
            'fee_breakdown' => $request->fee_breakdown ?? [],
            'status' => 'pending_verification',
        ]);

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'message' => 'Bank transfer payment recorded. Please make the transfer and upload proof of payment.'
        ];
    }

    /**
     * Process cash payment
     */
    protected function processCashPayment($student, $enrollment, $request): array
    {
        $payment = $this->paymentService->createPayment([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'amount' => $request->amount,
            'payment_method' => 'cash',
            'fee_breakdown' => $request->fee_breakdown ?? [],
            'status' => 'pending_verification',
        ]);

        return [
            'success' => true,
            'payment_id' => $payment->id,
            'message' => 'Cash payment recorded. Please visit the finance office to complete the payment.'
        ];
    }

    /**
     * Handle M-Pesa callback
     */
    public function mpesaCallback(Request $request)
    {
        Log::info('M-Pesa callback received', $request->all());

        $result = $this->mpesaService->handleCallback($request->all());

        if ($result) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
        }

        return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Failed']);
    }

    /**
     * Handle Stripe webhook
     */
    public function stripeWebhook(Request $request)
    {
        $signature = $request->header('Stripe-Signature');
        
        Log::info('Stripe webhook received', [
            'signature' => $signature,
            'payload' => $request->all()
        ]);

        $result = $this->stripeService->handleWebhook($request->all(), $signature);

        if ($result) {
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error'], 400);
    }

    /**
     * Display Stripe checkout page
     */
    public function stripeCheckout(Payment $payment): View
    {
        $student = Auth::guard('student')->user();

        // Ensure payment belongs to student
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        // Ensure payment is for Stripe and pending
        if ($payment->payment_method !== 'stripe' || $payment->status !== 'pending') {
            return redirect()->route('student.payments')
                ->withErrors(['payment' => 'Invalid payment for checkout.']);
        }

        $clientSecret = session('stripe_client_secret');
        $stripeKey = $this->stripeService->getPublishableKey();

        return view('student.stripe-checkout', compact('payment', 'clientSecret', 'stripeKey', 'student'));
    }

    /**
     * Payment success page
     */
    public function paymentSuccess(): View
    {
        $student = Auth::guard('student')->user();
        $paymentId = session('payment_id');
        
        $payment = null;
        if ($paymentId) {
            $payment = Payment::where('id', $paymentId)
                ->where('student_id', $student->id)
                ->with(['enrollment.course', 'enrollment.semester'])
                ->first();
        }

        return view('student.payment-success', compact('payment', 'student'));
    }

    /**
     * Payment cancel page
     */
    public function paymentCancel(): View
    {
        $student = Auth::guard('student')->user();
        return view('student.payment-cancel', compact('student'));
    }

    /**
     * Upload payment proof (for bank transfer)
     */
    public function uploadProof(Request $request, Payment $payment): RedirectResponse
    {
        $student = Auth::guard('student')->user();

        // Ensure payment belongs to student
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        $request->validate([
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        // Store the file
        $path = $request->file('proof_file')->store('payment-proofs', 'public');

        // Update payment with proof
        $payment->update([
            'proof_of_payment' => $path,
            'notes' => $request->notes,
            'status' => 'pending_verification',
        ]);

        return redirect()->route('student.payments.show', $payment)
            ->with('success', 'Payment proof uploaded successfully. It will be reviewed by our finance team.');
    }

    /**
     * Get payment status (AJAX)
     */
    public function getPaymentStatus(Payment $payment)
    {
        $student = Auth::guard('student')->user();

        // Ensure payment belongs to student
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment.');
        }

        return response()->json([
            'status' => $payment->status,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'processed_at' => $payment->processed_at?->toISOString(),
            'is_verified' => $payment->is_verified,
        ]);
    }
}
