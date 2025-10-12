<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        $student = Auth::guard('student')->user();
        $enrollments = $student->enrollments()->with(['course', 'semester'])->get();
        
        // Get specific enrollment if provided
        $selectedEnrollment = null;
        if ($request->has('enrollment_id')) {
            $selectedEnrollment = $enrollments->where('id', $request->enrollment_id)->first();
        }
        
        // Default to first enrollment if none selected
        $enrollment = $selectedEnrollment ?: $enrollments->first();
        
        // Get pre-filled amount if provided
        $prefilledAmount = $request->get('amount');
        
        // Check if this is the main payment route or student payment route
        if ($request->route()->getName() === 'payment.create') {
            return view('payment.create', compact('student', 'enrollment', 'enrollments', 'prefilledAmount'));
        }
        
        return view('student.payments.create', compact('student', 'enrollment', 'enrollments', 'prefilledAmount'));
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:mpesa,stripe,paypal,bank_transfer',
            'phone' => 'required_if:payment_method,mpesa',
            'enrollment_id' => 'nullable|exists:student_enrollments,id',
            'course_id' => 'nullable|exists:courses,id'
        ]);

        $student = Auth::guard('student')->user();
        $enrollment = null;
        
        if ($request->enrollment_id) {
            $enrollment = $student->enrollments()->with('course')->find($request->enrollment_id);
        } else {
            $enrollment = $student->enrollments()->with('course')->first();
        }
        
        // Create payment record with proper enrollment linking
        $payment = new \App\Models\Payment();
        $payment->student_id = $student->id;
        $payment->student_enrollment_id = $enrollment ? $enrollment->id : null;
        $payment->amount = $request->amount;
        $payment->currency = 'KES';
        $payment->payment_method = $request->payment_method;
        $payment->payment_type = 'tuition';
        $payment->status = 'pending';
        $payment->payment_reference = \App\Models\Payment::generatePaymentReference();
        $payment->transaction_id = 'TXN_' . time();
        $payment->payment_date = now();
        
        if ($request->payment_method === 'mpesa') {
            $payment->mpesa_phone_number = $request->phone;
        }
        
        $payment->save();

        // Redirect to the main payment processing route with payment data
        return redirect()->route('payment.create', ['enrollment_id' => $enrollment->id])
            ->with('success', 'Payment initiated successfully')
            ->with('payment_data', [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'phone' => $request->phone
            ]);
    }

    public function success()
    {
        $student = Auth::guard('student')->user();
        $recentPayment = \App\Models\Payment::where('student_id', $student->id)
            ->latest()
            ->first();
        return view('student.payments.success', compact('student', 'recentPayment'));
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