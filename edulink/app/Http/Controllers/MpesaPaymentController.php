<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\Http;

class MpesaPaymentController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
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
            'payment_method' => 'mpesa',
            'status' => 'pending',
            'transaction_id' => 'REQ_' . time(),
            'mpesa_phone_number' => $this->formatPhone($request->phone),
            'payment_reference' => Payment::generatePaymentReference(),
            'currency' => 'KES',
            'payment_type' => 'tuition',
            'payment_date' => now()
        ]);

        $stkResult = $this->initiateSTKPush($request->phone, $request->amount, $payment->id);

        if ($stkResult['success']) {
            $payment->update(['transaction_id' => $stkResult['checkout_request_id']]);
            return response()->json([
                'success' => true,
                'message' => $stkResult['message'],
                'payment_id' => $payment->id,
                'redirect_url' => route('payment.pending', $payment->id)
            ]);
        }

        $payment->update(['status' => 'failed']);
        return response()->json(['success' => false, 'message' => $stkResult['message']]);
    }

    private function initiateSTKPush($phone, $amount, $paymentId)
    {
        $accessToken = $this->getAccessToken();
        $timestamp = date('YmdHis');
        $shortcode = '174379';
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
        $password = base64_encode($shortcode . $passkey . $timestamp);
        
        $phone = $this->formatPhone($phone);
        
        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$amount,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => 'https://metal-pugs-unite.loca.lt/api/mpesa/callback',
            'AccountReference' => 'FEES' . $paymentId,
            'TransactionDesc' => 'School Fees Payment'
        ];
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json'
        ])->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', $payload);
        
        $result = $response->json();
        
        if ($response->successful() && isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
            return [
                'success' => true,
                'message' => $result['CustomerMessage'] ?? 'STK Push sent successfully',
                'checkout_request_id' => $result['CheckoutRequestID']
            ];
        }
        
        return [
            'success' => false,
            'message' => $result['errorMessage'] ?? $result['ResponseDescription'] ?? 'STK Push failed'
        ];
    }

    private function getAccessToken()
    {
        $consumerKey = 'uQxYiAQaZ3KG9uNlSYLZhdGTFYvApzqt4lGqDyWnYnervPAv';
        $consumerSecret = 'KJWGYCvKvfLT1coq80uYLq9K4WwkmDKxcdF6F5vgvxg2X3DpkCu7bpaJ5GP60wg4';
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json'
        ])->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        
        $result = $response->json();
        
        if ($response->successful() && isset($result['access_token'])) {
            return $result['access_token'];
        }
        
        throw new \Exception('Failed to get access token');
    }

    private function formatPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) == '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) == '7' || substr($phone, 0, 1) == '1') {
            $phone = '254' . $phone;
        } elseif (substr($phone, 0, 4) == '+254') {
            $phone = substr($phone, 1);
        }
        
        return $phone;
    }
}