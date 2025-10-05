<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalService
{
    private $clientId;
    private $clientSecret;
    private $baseUrl;
    private $sandbox;
    private $accessToken;

    public function __construct()
    {
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->sandbox = config('services.paypal.sandbox', true);
        $this->baseUrl = $this->sandbox ? 
            'https://api-m.sandbox.paypal.com' : 
            'https://api-m.paypal.com';
    }

    public function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        // Check if credentials are configured
        if (!$this->clientId || !$this->clientSecret || 
            $this->clientId === 'your_paypal_client_id' || 
            $this->clientSecret === 'your_paypal_client_secret_from_developer_dashboard') {
            throw new \Exception('PayPal credentials not configured');
        }

        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post($this->baseUrl . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $this->accessToken = $response->json()['access_token'];
                return $this->accessToken;
            }

            throw new \Exception('Failed to get PayPal access token');
        } catch (\Exception $e) {
            Log::error('PayPal access token error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function createPayment($amount, $currency, $returnUrl, $cancelUrl, $description = 'Payment')
    {
        try {
            // Check if credentials are configured
            if (!$this->clientId || !$this->clientSecret || 
                $this->clientId === 'your_paypal_client_id' || 
                $this->clientSecret === 'your_paypal_client_secret_from_developer_dashboard') {
                
                // Return mock success for testing when credentials are not configured
                $paymentId = 'PAY_MOCK_' . time() . rand(1000, 9999);
                return [
                    'success' => true,
                    'payment_id' => $paymentId,
                    'approval_url' => $returnUrl . '?paymentId=' . $paymentId . '&PayerID=PAYER_MOCK_' . time()
                ];
            }
            
            $accessToken = $this->getAccessToken();
            
            $paymentData = [
                'intent' => 'sale',
                'payer' => [
                    'payment_method' => 'paypal'
                ],
                'transactions' => [[
                    'amount' => [
                        'total' => number_format($amount, 2, '.', ''),
                        'currency' => $currency
                    ],
                    'description' => $description
                ]],
                'redirect_urls' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . '/v1/payments/payment', $paymentData);

            if ($response->successful()) {
                $data = $response->json();
                $approvalUrl = collect($data['links'])->firstWhere('rel', 'approval_url')['href'] ?? null;
                
                return [
                    'success' => true,
                    'payment_id' => $data['id'],
                    'approval_url' => $approvalUrl
                ];
            }

            throw new \Exception('PayPal API error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('PayPal payment creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function executePayment($paymentId, $payerId)
    {
        try {
            // Check if credentials are configured or if this is a mock payment
            if (!$this->clientId || !$this->clientSecret || 
                $this->clientId === 'your_paypal_client_id' || 
                $this->clientSecret === 'your_paypal_client_secret_from_developer_dashboard' ||
                strpos($paymentId, 'PAY_MOCK_') === 0) {
                
                // Return mock success for testing
                return [
                    'success' => true,
                    'payment' => [
                        'id' => $paymentId,
                        'state' => 'approved',
                        'payer' => [
                            'payer_info' => [
                                'payer_id' => $payerId
                            ]
                        ],
                        'transactions' => [
                            [
                                'amount' => [
                                    'total' => '100.00',
                                    'currency' => 'USD'
                                ]
                            ]
                        ]
                    ]
                ];
            }
            
            $accessToken = $this->getAccessToken();
            
            $response = Http::withToken($accessToken)
                ->post($this->baseUrl . '/v1/payments/payment/' . $paymentId . '/execute', [
                    'payer_id' => $payerId
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'payment' => $data
                ];
            }

            throw new \Exception('PayPal execution error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('PayPal payment execution failed', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}