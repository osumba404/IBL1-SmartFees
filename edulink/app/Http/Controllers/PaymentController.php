<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class PaymentController extends Controller
{
    public function create()
    {
        $student = auth('student')->user();
        $enrollment = $student->enrollments()->with('course')->first();
        
        // Check if there's payment data from the student portal
        $paymentData = session('payment_data');
        $existingPayment = null;
        
        if ($paymentData && isset($paymentData['payment_id'])) {
            $existingPayment = Payment::find($paymentData['payment_id']);
        }
        
        return view('payment.create', compact('student', 'enrollment', 'paymentData', 'existingPayment'));
    }
    
    public function process(Request $request)
    {
        try {
            // Check if there's an existing payment record to update
            $existingPayment = null;
            if ($request->payment_id) {
                $existingPayment = Payment::find($request->payment_id);
            }
            
            if ($request->payment_method === 'mpesa') {
                return $this->processMpesa($request, $existingPayment);
            } elseif ($request->payment_method === 'paypal') {
                return $this->processPaypal($request, $existingPayment);
            } elseif ($request->payment_method === 'stripe') {
                return $this->processStripe($request, $existingPayment);
            }
            
            return response()->json(['success' => false, 'message' => 'Payment method not supported']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Payment processing failed']);
        }
    }
    
    private function processMpesa(Request $request, $existingPayment = null)
    {
        try {
            // Validate phone number first
            if (empty($request->phone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number is required'
                ]);
            }
            
            // Use existing payment or create new one
            if ($existingPayment && $existingPayment->status === 'pending') {
                $payment = $existingPayment;
                $payment->mpesa_phone_number = $this->formatPhone($request->phone);
                $payment->payment_method = 'mpesa';
                $payment->save();
            } else {
                // Create payment record with enrollment
                $student = auth('student')->user();
                $enrollment = $student->enrollments()->with('course')->first();
                
                $payment = new Payment();
                $payment->student_id = $student->id;
                $payment->student_enrollment_id = $enrollment ? $enrollment->id : null;
                $payment->amount = $request->amount ?? 1000;
                $payment->currency = 'KES';
                $payment->payment_method = 'mpesa';
                $payment->payment_type = 'tuition';
                $payment->status = 'pending';
                $payment->payment_reference = Payment::generatePaymentReference();
                $payment->transaction_id = 'REQ_' . time();
                $payment->mpesa_phone_number = $this->formatPhone($request->phone);
                $payment->payment_date = now();
                $payment->save();
            }
            
            // Initiate STK Push
            $stkResult = $this->initiateSTKPush($request->phone, $request->amount, $payment->id);
            
            if ($stkResult['success']) {
                $payment->transaction_id = $stkResult['checkout_request_id'];
                $payment->save();
                
                return response()->json([
                    'success' => true,
                    'message' => $stkResult['message'],
                    'payment_id' => $payment->id,
                    'redirect_url' => route('payment.pending', $payment->id)
                ]);
            }
            
            $payment->status = 'failed';
            $payment->save();
            
            return response()->json([
                'success' => false,
                'message' => $stkResult['message']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    private function initiateSTKPush($phone, $amount, $paymentId)
    {
        try {
            Log::info('Starting STK Push', ['phone' => $phone, 'amount' => $amount]);
            
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
            
            Log::info('STK Push Payload', $payload);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', $payload);
            
            $result = $response->json();
            Log::info('STK Push Response', $result);
            
            if ($response->successful() && isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'message' => $result['CustomerMessage'] ?? 'STK Push sent successfully',
                    'checkout_request_id' => $result['CheckoutRequestID']
                ];
            }
            
            $errorMessage = $result['errorMessage'] ?? $result['ResponseDescription'] ?? 'STK Push failed';
            Log::error('STK Push Failed', ['error' => $errorMessage, 'response' => $result]);
            
            return [
                'success' => false,
                'message' => $errorMessage
            ];
            
        } catch (\Exception $e) {
            Log::error('STK Push Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage()
            ];
        }
    }
    
    private function getAccessToken()
    {
        $consumerKey = 'uQxYiAQaZ3KG9uNlSYLZhdGTFYvApzqt4lGqDyWnYnervPAv';
        $consumerSecret = 'KJWGYCvKvfLT1coq80uYLq9K4WwkmDKxcdF6F5vgvxg2X3DpkCu7bpaJ5GP60wg4';
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        
        Log::info('Getting M-Pesa Access Token');
        
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json'
        ])->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        
        $result = $response->json();
        Log::info('Access Token Response', $result);
        
        if ($response->successful() && isset($result['access_token'])) {
            return $result['access_token'];
        }
        
        Log::error('Failed to get access token', $result);
        throw new \Exception('Failed to get access token: ' . ($result['error_description'] ?? 'Unknown error'));
    }
    
    private function formatPhone($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Handle different phone number formats
        if (substr($phone, 0, 1) == '0') {
            // 0712345678 -> 254712345678
            $phone = '254' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) == '7' || substr($phone, 0, 1) == '1') {
            // 712345678 -> 254712345678
            $phone = '254' . $phone;
        } elseif (substr($phone, 0, 4) == '+254') {
            // +254712345678 -> 254712345678
            $phone = substr($phone, 1);
        }
        
        Log::info('Formatted phone number', ['original' => func_get_arg(0), 'formatted' => $phone]);
        
        return $phone;
    }
    
    public function pending($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return redirect()->route('payment.create')->with('error', 'Payment not found');
        }
        
        return view('payment.pending', compact('payment'));
    }
    
    public function status($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }
        
        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'transaction_id' => $payment->gateway_transaction_id ?? $payment->transaction_id,
            'amount' => $payment->amount
        ]);
    }
    
    public function callback(Request $request)
    {
        $data = $request->all();
        
        Log::info('M-Pesa Callback Received:', $data);
        
        try {
            if (!isset($data['Body']['stkCallback'])) {
                Log::error('Invalid callback structure');
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback']);
            }
            
            $callback = $data['Body']['stkCallback'];
            $checkoutRequestId = $callback['CheckoutRequestID'];
            $resultCode = $callback['ResultCode'];
            
            Log::info('Processing callback', ['checkout_id' => $checkoutRequestId, 'result_code' => $resultCode]);
            
            $payment = Payment::where('transaction_id', $checkoutRequestId)->first();
            
            if (!$payment) {
                Log::error('Payment not found', ['checkout_id' => $checkoutRequestId]);
                return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Payment not found']);
            }
            
            if ($resultCode == 0) {
                // Payment successful
                $mpesaReceiptNumber = null;
                $amount = null;
                $phoneNumber = null;
                
                if (isset($callback['CallbackMetadata']['Item'])) {
                    foreach ($callback['CallbackMetadata']['Item'] as $item) {
                        switch ($item['Name']) {
                            case 'MpesaReceiptNumber':
                                $mpesaReceiptNumber = $item['Value'];
                                break;
                            case 'Amount':
                                $amount = $item['Value'];
                                break;
                            case 'PhoneNumber':
                                $phoneNumber = $item['Value'];
                                break;
                        }
                    }
                }
                
                $payment->status = 'completed';
                $payment->gateway_transaction_id = $mpesaReceiptNumber;
                $payment->payment_date = now()->setTimezone(config('app.timezone'));
                $payment->payment_details = json_encode($data);
                $payment->save();
                
                // Update student enrollment balance
                if ($payment->student_id) {
                    $student = \App\Models\Student::find($payment->student_id);
                    if ($student) {
                        $enrollment = $student->enrollments()->first();
                        if ($enrollment) {
                            $enrollment->fees_paid += $payment->amount;
                            $enrollment->save();
                            Log::info('Updated enrollment balance', ['student_id' => $student->id, 'amount' => $payment->amount]);
                        }
                        
                        // Send payment confirmation notification
                        $notificationService = new NotificationService();
                        $notificationService->sendPaymentConfirmation($payment);
                        
                        // Create in-app notification
                        PaymentNotification::create([
                            'student_id' => $student->id,
                            'payment_id' => $payment->id,
                            'title' => 'Payment Successful',
                            'message' => "Your M-Pesa payment of KES " . number_format($payment->amount, 2) . " has been processed successfully. Receipt: {$mpesaReceiptNumber}",
                            'notification_type' => 'payment_success',
                        ]);
                    }
                }
                
                Log::info('Payment completed successfully', ['receipt' => $mpesaReceiptNumber, 'amount' => $amount]);
                
            } else {
                // Payment failed or cancelled
                $payment->status = 'failed';
                $payment->payment_details = json_encode($data);
                $payment->save();
                
                Log::info('Payment failed', ['result_code' => $resultCode, 'description' => $callback['ResultDesc'] ?? 'Unknown error']);
            }
            
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
            
        } catch (\Exception $e) {
            Log::error('Callback processing error: ' . $e->getMessage());
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Processing failed']);
        }
    }
    
    public function simulate($paymentId)
    {
        try {
            $payment = Payment::find($paymentId);
            
            if ($payment && $payment->status === 'pending') {
                $payment->status = 'completed';
                $payment->gateway_transaction_id = 'MPX' . time();
                $payment->payment_date = now()->setTimezone(config('app.timezone'));
                $payment->save();
                
                // Update student enrollment balance
                if ($payment->student_id) {
                    $student = \App\Models\Student::find($payment->student_id);
                    if ($student) {
                        $enrollment = $student->enrollments()->first();
                        if ($enrollment) {
                            $enrollment->fees_paid += $payment->amount;
                            $enrollment->save();
                            Log::info('Updated enrollment balance', ['student_id' => $student->id, 'amount' => $payment->amount]);
                        }
                        
                        // Send payment confirmation notification
                        $notificationService = new NotificationService();
                        $notificationService->sendPaymentConfirmation($payment);
                        Log::info('Payment confirmation email sent', ['student_email' => $student->email]);
                    }
                }
                
                return response()->json(['success' => true, 'message' => 'Payment completed successfully']);
            }
            
            return response()->json(['success' => false, 'message' => 'Payment not found or already processed']);
        } catch (\Exception $e) {
            Log::error('Payment simulation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment completion failed']);
        }
    }
    
    private function processPaypal(Request $request, $existingPayment = null)
    {
        try {
            Log::info('Processing PayPal payment', [
                'amount' => $request->amount,
                'email' => $request->paypal_email
            ]);
            
            $paypalService = new \App\Services\PaypalService();
            
            // Use existing payment or create new one
            if ($existingPayment && $existingPayment->status === 'pending') {
                $payment = $existingPayment;
                $payment->payment_method = 'paypal';
                $payment->save();
            } else {
                // Create payment record with enrollment
                $student = auth('student')->user();
                $enrollment = $student->enrollments()->with('course')->first();
                
                $payment = new Payment();
                $payment->student_id = $student->id;
                $payment->student_enrollment_id = $enrollment ? $enrollment->id : null;
                $payment->amount = $request->amount ?? 1000;
                $payment->currency = 'USD';
                $payment->payment_method = 'paypal';
                $payment->payment_type = 'tuition';
                $payment->status = 'pending';
                $payment->payment_reference = Payment::generatePaymentReference();
                $payment->transaction_id = 'PP_' . time();
                $payment->payment_date = now();
                $payment->save();
            }
            
            Log::info('Payment record created', ['payment_id' => $payment->id]);
            
            // Create PayPal payment
            $returnUrl = route('payment.paypal.return', ['payment_id' => $payment->id]);
            $cancelUrl = route('payment.paypal.cancel', ['payment_id' => $payment->id]);
            
            Log::info('PayPal URLs', [
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl
            ]);
            
            $result = $paypalService->createPayment(
                $request->amount,
                'USD',
                $returnUrl,
                $cancelUrl,
                'School Fees Payment'
            );
            
            Log::info('PayPal service result', $result);
            
            if ($result['success']) {
                $payment->transaction_id = $result['payment_id'];
                $payment->save();
                
                $response = [
                    'success' => true,
                    'redirect_url' => $result['approval_url']
                ];
                
                Log::info('Sending success response', $response);
                
                return response()->json($response);
            }
            
            $payment->status = 'failed';
            $payment->save();
            
            $errorResponse = [
                'success' => false,
                'message' => $result['error'] ?? 'PayPal payment failed'
            ];
            
            Log::error('PayPal payment failed', $errorResponse);
            
            return response()->json($errorResponse);
            
        } catch (\Exception $e) {
            Log::error('PayPal payment exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'PayPal payment failed: ' . $e->getMessage()
            ]);
        }
    }
    
    public function paypalReturn(Request $request)
    {
        try {
            $paymentId = $request->payment_id;
            $paypalPaymentId = $request->paymentId;
            $payerId = $request->PayerID;
            
            $payment = Payment::find($paymentId);
            if (!$payment) {
                return redirect()->route('payment.create')->with('error', 'Payment not found');
            }
            
            $paypalService = new \App\Services\PaypalService();
            $result = $paypalService->executePayment($paypalPaymentId, $payerId);
            
            if ($result['success']) {
                $payment->status = 'completed';
                $payment->gateway_transaction_id = $paypalPaymentId;
                $payment->payment_date = now()->setTimezone(config('app.timezone'));
                $payment->payment_details = json_encode($result['payment']);
                $payment->save();
                
                // Update student enrollment balance
                if ($payment->student_id) {
                    $student = \App\Models\Student::find($payment->student_id);
                    if ($student) {
                        $enrollment = $student->enrollments()->first();
                        if ($enrollment) {
                            $enrollment->fees_paid += $payment->amount;
                            $enrollment->save();
                        }
                        
                        // Send payment confirmation notification
                        $notificationService = new NotificationService();
                        $notificationService->sendPaymentConfirmation($payment);
                        
                        // Create in-app notification
                        PaymentNotification::create([
                            'student_id' => $student->id,
                            'payment_id' => $payment->id,
                            'title' => 'Payment Successful',
                            'message' => "Your PayPal payment of KES " . number_format($payment->amount, 2) . " has been processed successfully.",
                            'notification_type' => 'payment_success',
                        ]);
                    }
                }
                
                return redirect()->route('payment.success')->with('success', 'Payment completed successfully');
            }
            
            $payment->status = 'failed';
            $payment->save();
            
            return redirect()->route('payment.create')->with('error', 'Payment execution failed');
            
        } catch (\Exception $e) {
            Log::error('PayPal return processing error: ' . $e->getMessage());
            return redirect()->route('payment.create')->with('error', 'Payment processing failed');
        }
    }
    
    public function paypalCancel(Request $request)
    {
        $paymentId = $request->payment_id;
        $payment = Payment::find($paymentId);
        
        if ($payment) {
            $payment->status = 'cancelled';
            $payment->save();
        }
        
        return redirect()->route('payment.create')->with('error', 'Payment was cancelled');
    }
    
    private function processStripe(Request $request, $existingPayment = null)
    {
        try {
            Log::info('Processing Stripe payment', [
                'amount' => $request->amount,
                'card_number' => substr($request->card_number, -4)
            ]);
            
            $stripeService = new \App\Services\StripeService();
            
            // Use existing payment or create new one
            if ($existingPayment && $existingPayment->status === 'pending') {
                $payment = $existingPayment;
                $payment->payment_method = 'stripe';
                $payment->save();
            } else {
                // Create payment record with enrollment
                $student = auth('student')->user();
                $enrollment = $student->enrollments()->with('course')->first();
                
                $payment = new Payment();
                $payment->student_id = $student->id;
                $payment->student_enrollment_id = $enrollment ? $enrollment->id : null;
                $payment->amount = $request->amount ?? 1000;
                $payment->currency = 'USD';
                $payment->payment_method = 'stripe';
                $payment->payment_type = 'tuition';
                $payment->status = 'pending';
                $payment->payment_reference = Payment::generatePaymentReference();
                $payment->transaction_id = 'ST_' . time();
                $payment->payment_date = now();
                $payment->save();
            }
            
            // Create Stripe payment intent
            $result = $stripeService->createPaymentIntent(
                $request->amount,
                'usd',
                [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id
                ]
            );
            
            if ($result['success']) {
                $payment->stripe_payment_intent_id = $result['payment_intent_id'];
                $payment->save();
                
                return response()->json([
                    'success' => true,
                    'client_secret' => $result['client_secret'],
                    'payment_id' => $payment->id
                ]);
            }
            
            $payment->status = 'failed';
            $payment->save();
            
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Stripe payment failed'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stripe payment exception', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Stripe payment failed: ' . $e->getMessage()
            ]);
        }
    }
    
    public function stripeWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $sig_header = $request->header('stripe-signature');
            $endpoint_secret = config('services.stripe.webhook_secret');
            
            if ($endpoint_secret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            } else {
                $event = json_decode($payload, true);
            }
            
            if ($event['type'] === 'payment_intent.succeeded') {
                $paymentIntent = $event['data']['object'];
                $paymentId = $paymentIntent['metadata']['payment_id'] ?? null;
                
                if ($paymentId) {
                    $payment = Payment::find($paymentId);
                    if ($payment && $payment->status === 'pending') {
                        $payment->status = 'completed';
                        $payment->gateway_transaction_id = $paymentIntent['id'];
                        $payment->payment_date = now()->setTimezone(config('app.timezone'));
                        $payment->payment_details = json_encode($paymentIntent);
                        $payment->save();
                        
                        // Update student enrollment balance
                        if ($payment->student_id) {
                            $student = \App\Models\Student::find($payment->student_id);
                            if ($student) {
                                $enrollment = $student->enrollments()->first();
                                if ($enrollment) {
                                    $enrollment->fees_paid += $payment->amount;
                                    $enrollment->save();
                                }
                                
                                // Send payment confirmation notification
                                $notificationService = new NotificationService();
                                $notificationService->sendPaymentConfirmation($payment);
                                
                                // Create in-app notification
                                PaymentNotification::create([
                                    'student_id' => $student->id,
                                    'payment_id' => $payment->id,
                                    'title' => 'Payment Successful',
                                    'message' => "Your card payment of KES " . number_format($payment->amount, 2) . " has been processed successfully.",
                                    'notification_type' => 'payment_success',
                                ]);
                            }
                        }
                    }
                }
            }
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook failed'], 400);
        }
    }
    
    public function processPayment(Payment $payment)
    {
        $student = auth('student')->user();
        
        // Ensure payment belongs to authenticated student
        if ($payment->student_id !== $student->id) {
            abort(403, 'Unauthorized access to payment');
        }
        
        // Load enrollment and course data
        $payment->load(['enrollment.course', 'student']);
        
        // Set session data for payment form
        session([
            'payment_data' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'description' => $payment->description ?? 'Course enrollment payment'
            ]
        ]);
        
        return view('payment.create', [
            'student' => $student,
            'enrollment' => $payment->enrollment,
            'existingPayment' => $payment,
            'paymentData' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'description' => $payment->description ?? 'Course enrollment payment'
            ]
        ]);
    }
    
    public function success()
    {
        return view('payment.success');
    }
}