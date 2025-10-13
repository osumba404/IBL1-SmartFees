<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Models\Payment;
use App\Models\StudentEnrollment;

class StripePaymentController extends Controller
{
    private $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function process(Request $request)
    {
        $request->validate([
            'stripeToken' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'enrollment_id' => 'required|exists:student_enrollments,id'
        ]);

        $enrollment = StudentEnrollment::findOrFail($request->enrollment_id);
        
        if ($enrollment->student_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized access to enrollment.');
        }

        $payment = Payment::create([
            'student_id' => auth()->id(),
            'enrollment_id' => $request->enrollment_id,
            'amount' => $request->amount,
            'payment_method' => 'stripe',
            'status' => 'pending',
            'transaction_id' => 'STRIPE_' . time() . '_' . auth()->id()
        ]);

        $response = $this->stripeService->processPayment(
            $request->stripeToken,
            $request->amount,
            $payment->transaction_id
        );

        if ($response['success']) {
            $payment->update([
                'status' => 'completed',
                'stripe_payment_intent_id' => $response['payment_intent_id']
            ]);
            $enrollment->updatePaymentStatus($request->amount);
            return redirect()->route('payment.success', $payment->id);
        }

        $payment->update(['status' => 'failed']);
        return redirect()->back()->with('error', 'Stripe payment failed: ' . $response['message']);
    }
}