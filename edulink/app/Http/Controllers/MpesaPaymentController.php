<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MpesaService;
use App\Models\Payment;
use App\Models\StudentEnrollment;

class MpesaPaymentController extends Controller
{
    private $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    public function process(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
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
            'payment_method' => 'mpesa',
            'status' => 'pending',
            'transaction_id' => 'MPESA_' . time() . '_' . auth()->id(),
            'phone_number' => $request->phone
        ]);

        $response = $this->mpesaService->initiateSTKPush(
            $request->phone,
            $request->amount,
            $payment->transaction_id
        );

        if ($response['success']) {
            $payment->update(['mpesa_checkout_request_id' => $response['CheckoutRequestID']]);
            return redirect()->route('payment.success', $payment->id);
        }

        $payment->update(['status' => 'failed']);
        return redirect()->back()->with('error', 'M-Pesa payment failed: ' . $response['message']);
    }
}