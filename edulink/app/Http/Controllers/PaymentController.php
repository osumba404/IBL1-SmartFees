<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function create(Request $request = null)
    {
        $student = auth('student')->user();
        $enrollments = $student->enrollments()->with(['course', 'semester'])->get();
        $selectedEnrollment = null;
        
        if ($request && $request->has('enrollment_id')) {
            $selectedEnrollment = $enrollments->where('id', $request->enrollment_id)->first();
        }
        
        $enrollment = $selectedEnrollment ?: $enrollments->first();
        $prefilledAmount = request('amount');
        $paymentData = session('payment_data');
        $existingPayment = null;
        
        if ($paymentData && isset($paymentData['payment_id'])) {
            $existingPayment = Payment::find($paymentData['payment_id']);
        }
        
        return view('payment.create', compact('student', 'enrollment', 'enrollments', 'paymentData', 'existingPayment', 'prefilledAmount'));
    }
    
    public function process(Request $request)
    {
        switch ($request->payment_method) {
            case 'mpesa':
                return app(MpesaPaymentController::class)->process($request);
            case 'stripe':
                return app(StripePaymentController::class)->process($request);
            case 'paypal':
                return app(PaypalPaymentController::class)->process($request);
            default:
                return response()->json(['success' => false, 'message' => 'Payment method not supported']);
        }
    }
    
    public function pending($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return redirect()->route('payment.create')->with('error', 'Payment not found');
        }
        
        return view('payment.pending', compact('payment'));
    }
    
    public function status($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'transaction_id' => $payment->gateway_transaction_id ?? $payment->transaction_id,
            'amount' => $payment->amount
        ]);
    }
    

    
    public function success()
    {
        return view('payment.success');
    }
}