<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * MpesaService for Edulink International College Nairobi
 * 
 * Handles M-Pesa Daraja API integration for STK Push payments
 * Manages authentication, payment initiation, and callback processing
 */
class MpesaService
{
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;
    protected string $passkey;
    protected string $callbackUrl;
    protected string $baseUrl;
    protected bool $sandbox;

    public function __construct()
    {
        $this->consumerKey = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode = config('services.mpesa.shortcode');
        $this->passkey = config('services.mpesa.passkey');
        $this->callbackUrl = config('services.mpesa.callback_url');
        $this->sandbox = config('services.mpesa.sandbox', true);
        
        $this->baseUrl = $this->sandbox 
            ? 'https://sandbox.safaricom.co.ke'
            : 'https://api.safaricom.co.ke';
    }

    /**
     * Get OAuth access token from M-Pesa API
     */
    public function getAccessToken(): ?string
    {
        $cacheKey = 'mpesa_access_token';
        
        // Check if token is cached and still valid
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'] ?? 3600;
                
                // Cache token for slightly less than expiry time
                Cache::put($cacheKey, $accessToken, now()->addSeconds($expiresIn - 60));
                
                Log::info('M-Pesa access token obtained successfully');
                return $accessToken;
            }

            Log::error('Failed to get M-Pesa access token', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return null;

        } catch (\Exception $e) {
            Log::error('M-Pesa access token request failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate password for STK Push
     */
    protected function generatePassword(): string
    {
        $timestamp = now()->format('YmdHis');
        return base64_encode($this->shortcode . $this->passkey . $timestamp);
    }

    /**
     * Get current timestamp for M-Pesa API
     */
    protected function getTimestamp(): string
    {
        return now()->format('YmdHis');
    }

    /**
     * Initiate STK Push payment
     */
    public function initiateSTKPush(
        string $phoneNumber,
        float $amount,
        string $accountReference,
        string $transactionDesc
    ): array {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to obtain M-Pesa access token'
                ];
            }

            // Format phone number (remove + and ensure it starts with 254)
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);
            
            if (!$phoneNumber) {
                return [
                    'success' => false,
                    'message' => 'Invalid phone number format'
                ];
            }

            $timestamp = $this->getTimestamp();
            $password = $this->generatePassword();

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => (int) $amount, // M-Pesa expects integer
                'PartyA' => $phoneNumber,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => $transactionDesc
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/mpesa/stkpush/v1/processrequest', $payload);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
                Log::info('M-Pesa STK Push initiated successfully', [
                    'phone' => $phoneNumber,
                    'amount' => $amount,
                    'checkout_request_id' => $responseData['CheckoutRequestID']
                ]);

                return [
                    'success' => true,
                    'message' => 'STK Push initiated successfully',
                    'checkout_request_id' => $responseData['CheckoutRequestID'],
                    'merchant_request_id' => $responseData['MerchantRequestID'],
                    'response_code' => $responseData['ResponseCode'],
                    'response_description' => $responseData['ResponseDescription'],
                    'customer_message' => $responseData['CustomerMessage']
                ];
            }

            Log::error('M-Pesa STK Push failed', [
                'phone' => $phoneNumber,
                'amount' => $amount,
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'message' => $responseData['errorMessage'] ?? $responseData['ResponseDescription'] ?? 'STK Push failed',
                'error_code' => $responseData['errorCode'] ?? $responseData['ResponseCode'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push exception', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
                'amount' => $amount
            ]);

            return [
                'success' => false,
                'message' => 'STK Push request failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query STK Push transaction status
     */
    public function querySTKPushStatus(string $checkoutRequestId): array
    {
        try {
            $accessToken = $this->getAccessToken();
            
            if (!$accessToken) {
                return [
                    'success' => false,
                    'message' => 'Failed to obtain M-Pesa access token'
                ];
            }

            $timestamp = $this->getTimestamp();
            $password = $this->generatePassword();

            $payload = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'CheckoutRequestID' => $checkoutRequestId
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/mpesa/stkpushquery/v1/query', $payload);

            $responseData = $response->json();

            if ($response->successful()) {
                Log::info('M-Pesa STK Push status queried', [
                    'checkout_request_id' => $checkoutRequestId,
                    'result_code' => $responseData['ResultCode'] ?? null
                ]);

                return [
                    'success' => true,
                    'data' => $responseData
                ];
            }

            Log::error('M-Pesa STK Push status query failed', [
                'checkout_request_id' => $checkoutRequestId,
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to query transaction status',
                'data' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push status query exception', [
                'error' => $e->getMessage(),
                'checkout_request_id' => $checkoutRequestId
            ]);

            return [
                'success' => false,
                'message' => 'Status query failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number for M-Pesa API
     */
    protected function formatPhoneNumber(string $phoneNumber): ?string
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Handle different formats
        if (strlen($phoneNumber) == 10 && substr($phoneNumber, 0, 1) == '0') {
            // 0712345678 -> 254712345678
            return '254' . substr($phoneNumber, 1);
        } elseif (strlen($phoneNumber) == 9) {
            // 712345678 -> 254712345678
            return '254' . $phoneNumber;
        } elseif (strlen($phoneNumber) == 12 && substr($phoneNumber, 0, 3) == '254') {
            // 254712345678 -> valid
            return $phoneNumber;
        } elseif (strlen($phoneNumber) == 13 && substr($phoneNumber, 0, 4) == '2540') {
            // 2540712345678 -> 254712345678
            return '254' . substr($phoneNumber, 4);
        }
        
        return null; // Invalid format
    }

    /**
     * Validate M-Pesa callback
     */
    public function validateCallback(array $callbackData): bool
    {
        // Basic validation of callback structure
        $requiredFields = ['Body', 'Body.stkCallback'];
        
        foreach ($requiredFields as $field) {
            if (!data_get($callbackData, $field)) {
                Log::error('M-Pesa callback missing required field', ['field' => $field]);
                return false;
            }
        }

        return true;
    }

    /**
     * Process M-Pesa callback
     */
    public function processCallback(array $callbackData): array
    {
        try {
            if (!$this->validateCallback($callbackData)) {
                return [
                    'success' => false,
                    'message' => 'Invalid callback data structure'
                ];
            }

            $stkCallback = $callbackData['Body']['stkCallback'];
            
            $result = [
                'CheckoutRequestID' => $stkCallback['CheckoutRequestID'],
                'MerchantRequestID' => $stkCallback['MerchantRequestID'],
                'ResultCode' => $stkCallback['ResultCode'],
                'ResultDesc' => $stkCallback['ResultDesc']
            ];

            // If payment was successful, extract callback metadata
            if ($stkCallback['ResultCode'] == 0 && isset($stkCallback['CallbackMetadata'])) {
                $result['CallbackMetadata'] = $stkCallback['CallbackMetadata'];
            }

            Log::info('M-Pesa callback processed', [
                'checkout_request_id' => $result['CheckoutRequestID'],
                'result_code' => $result['ResultCode']
            ]);

            return [
                'success' => true,
                'data' => $result
            ];

        } catch (\Exception $e) {
            Log::error('M-Pesa callback processing failed', [
                'error' => $e->getMessage(),
                'callback_data' => $callbackData
            ]);

            return [
                'success' => false,
                'message' => 'Callback processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction fees (for display purposes)
     */
    public function getTransactionFee(float $amount): float
    {
        // M-Pesa transaction fees (approximate - check current rates)
        if ($amount <= 100) return 0;
        if ($amount <= 500) return 7;
        if ($amount <= 1000) return 13;
        if ($amount <= 1500) return 23;
        if ($amount <= 2500) return 33;
        if ($amount <= 3500) return 53;
        if ($amount <= 5000) return 57;
        if ($amount <= 7500) return 78;
        if ($amount <= 10000) return 90;
        if ($amount <= 15000) return 100;
        if ($amount <= 20000) return 105;
        if ($amount <= 35000) return 108;
        if ($amount <= 50000) return 110;
        
        return 110; // Maximum fee for amounts above 50,000
    }

    /**
     * Check if M-Pesa service is available
     */
    public function isServiceAvailable(): bool
    {
        return !empty($this->consumerKey) && 
               !empty($this->consumerSecret) && 
               !empty($this->shortcode) && 
               !empty($this->passkey);
    }
}
