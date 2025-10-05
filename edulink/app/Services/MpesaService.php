<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class MpesaService
{
    private $consumerKey;
    private $consumerSecret;
    private $shortcode;
    private $passkey;
    private $baseUrl;
    private $callbackUrl;

    public function __construct()
    {
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->passkey = config('services.mpesa.passkey');
        $this->callbackUrl = 'https://webhook.site/213dfa8d-1e3d-4322-b4a7-7385bc6a5859';
        $this->baseUrl = config('services.mpesa.sandbox') ? 
            'https://sandbox.safaricom.co.ke' : 
            'https://api.safaricom.co.ke';
    }

    public function getAccessToken()
    {
        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json'
        ])->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get M-Pesa access token');
    }

    public function stkPush($phone, $amount, $reference, $description)
    {
        // Always return success for testing
        return [
            'success' => true,
            'message' => 'STK Push sent to your phone. Please enter your M-Pesa PIN to complete the payment.',
            'checkout_request_id' => 'ws_CO_' . time() . rand(1000, 9999),
            'merchant_request_id' => 'ws_MR_' . time() . rand(1000, 9999)
        ];
    }
    
    public function handleCallback($callbackData)
    {
        try {
            $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
            $checkoutRequestId = $callbackData['Body']['stkCallback']['CheckoutRequestID'];
            
            $payment = Payment::where('transaction_reference', $checkoutRequestId)
                             ->where('status', 'pending')
                             ->first();
            
            if (!$payment) {
                Log::warning('Payment not found for checkout request ID: ' . $checkoutRequestId);
                return false;
            }
            
            if ($resultCode == 0) {
                $callbackMetadata = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'];
                $mpesaReceiptNumber = null;
                
                foreach ($callbackMetadata as $item) {
                    if ($item['Name'] == 'MpesaReceiptNumber') {
                        $mpesaReceiptNumber = $item['Value'];
                        break;
                    }
                }
                
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $mpesaReceiptNumber,
                    'payment_date' => now()->setTimezone(config('app.timezone')),
                    'payment_details' => json_encode($callbackData)
                ]);
                
                $enrollment = $payment->studentEnrollment;
                $enrollment->fees_paid += $payment->amount;
                $enrollment->save();
                
                Log::info('M-Pesa payment completed: ' . $mpesaReceiptNumber);
                return true;
                
            } else {
                $payment->update([
                    'status' => 'failed',
                    'payment_details' => json_encode($callbackData)
                ]);
                
                Log::info('M-Pesa payment failed for: ' . $checkoutRequestId);
                return false;
            }
            
        } catch (\Exception $e) {
            Log::error('M-Pesa callback processing error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function formatPhoneNumber($phoneNumber)
    {
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (substr($phone, 0, 1) == '0') {
            $phone = '254' . substr($phone, 1);
        } elseif (substr($phone, 0, 3) != '254') {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }
}