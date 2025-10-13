<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\StudentEnrollment;

class PaypalPaymentController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
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
            'payment_method' => 'paypal',
            'status' => 'completed',
            'transaction_id' => 'PAYPAL_' . time() . '_' . auth()->id()
        ]);

        $enrollment->updatePaymentStatus($request->amount);
        return redirect()->route('payment.success', $payment->id);
    }
}