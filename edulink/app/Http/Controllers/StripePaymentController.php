<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\StudentEnrollment;

class StripePaymentController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'enrollment_id' => 'required|exists:student_enrollments,id'
        ]);

        $enrollment = StudentEnrollment::findOrFail($request->enrollment_id);
        
        if ($enrollment->student_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access']);
        }

        $payment = Payment::create([
            'student_id' => auth()->id(),
            'student_enrollment_id' => $request->enrollment_id,
            'amount' => $request->amount,
            'payment_method' => 'stripe',
            'status' => 'completed',
            'transaction_id' => 'ST_' . time(),
            'gateway_transaction_id' => 'STX' . time(),
            'payment_reference' => Payment::generatePaymentReference(),
            'currency' => 'USD',
            'payment_type' => 'tuition',
            'payment_date' => now()
        ]);

        $enrollment->updatePaymentStatus($request->amount);
        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'redirect_url' => route('payment.success', $payment->id)
        ]);
    }
}