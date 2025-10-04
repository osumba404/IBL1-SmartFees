<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create()
    {
        $student = Auth::guard('student')->user();
        $enrollment = $student->enrollments()->with(['course', 'semester'])->first();
        
        return view('student.payments.create', compact('student', 'enrollment'));
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:mpesa,stripe,paypal,bank_transfer',
            'phone' => 'required_if:payment_method,mpesa'
        ]);

        // For now, just redirect to success page
        return redirect()->route('student.payments.success')
            ->with('success', 'Payment initiated successfully');
    }

    public function success()
    {
        $student = Auth::guard('student')->user();
        return view('student.payments.success', compact('student'));
    }

    public function cancel()
    {
        $student = Auth::guard('student')->user();
        return view('student.payments.cancel', compact('student'));
    }

    public function mpesaCallback(Request $request)
    {
        // Handle M-Pesa callback
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    public function stripeWebhook(Request $request)
    {
        // Handle Stripe webhook
        return response()->json(['status' => 'success']);
    }

    public function paypalWebhook(Request $request)
    {
        // Handle PayPal webhook
        return response()->json(['status' => 'success']);
    }
}