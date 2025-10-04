<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MpesaService;
use App\Models\Payment;

class MpesaController extends Controller
{
    public function callback(Request $request)
    {
        $mpesaService = new MpesaService();
        $result = $mpesaService->handleCallback($request->all());
        
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ]);
    }
    
    public function timeout(Request $request)
    {
        // Handle timeout
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Timeout received'
        ]);
    }
    
    public function result(Request $request)
    {
        // Handle result
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Result received'
        ]);
    }
    
    public function simulate($paymentId)
    {
        // Simulate successful payment for testing
        $payment = Payment::find($paymentId);
        
        if ($payment && $payment->status === 'pending') {
            $payment->update([
                'status' => 'completed',
                'transaction_id' => 'MPX' . time(),
                'payment_date' => now()
            ]);
            
            $enrollment = $payment->studentEnrollment;
            $enrollment->fees_paid += $payment->amount;
            $enrollment->save();
            
            return response()->json(['success' => true, 'message' => 'Payment simulated successfully']);
        }
        
        return response()->json(['success' => false, 'message' => 'Payment not found or already processed']);
    }
}