<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send payment confirmation notification
     */
    public function sendPaymentConfirmation(Payment $payment): void
    {
        $student = $payment->student;
        
        // Send email notification
        if (config('services.notifications.email_enabled', true)) {
            $this->sendPaymentEmail($student, $payment);
        }
        
        // Send SMS notification
        if (config('services.notifications.sms_enabled', false)) {
            $this->sendPaymentSMS($student, $payment);
        }
    }

    /**
     * Send password reset notification
     */
    public function sendPasswordResetNotification(string $email, string $token): void
    {
        if (config('services.notifications.email_enabled', true)) {
            $resetUrl = url("/student/reset-password/{$token}?email=" . urlencode($email));
            
            Mail::send('emails.password-reset', [
                'resetUrl' => $resetUrl,
                'email' => $email
            ], function ($message) use ($email) {
                $message->to($email)
                        ->subject('Reset Your Password - Edulink SmartFees');
            });
        }
    }

    /**
     * Send enrollment confirmation
     */
    public function sendEnrollmentConfirmation(Student $student, $enrollment): void
    {
        if (config('services.notifications.email_enabled', true)) {
            Mail::send('emails.enrollment-confirmation', [
                'student' => $student,
                'enrollment' => $enrollment
            ], function ($message) use ($student) {
                $message->to($student->email)
                        ->subject('Enrollment Confirmation - Edulink SmartFees');
            });
        }
    }

    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(Student $student, $amount): void
    {
        // Email reminder
        if (config('services.notifications.email_enabled', true)) {
            Mail::send('emails.payment-reminder', [
                'student' => $student,
                'amount' => $amount
            ], function ($message) use ($student) {
                $message->to($student->email)
                        ->subject('Payment Reminder - Edulink SmartFees');
            });
        }

        // SMS reminder
        if (config('services.notifications.sms_enabled', false)) {
            $message = "Dear {$student->first_name}, you have an outstanding balance of KES " . number_format($amount, 2) . ". Please make payment to avoid late fees. - Edulink";
            $this->sendSMS($student->phone, $message);
        }
    }

    /**
     * Send payment confirmation email
     */
    private function sendPaymentEmail(Student $student, Payment $payment): void
    {
        try {
            Mail::send('emails.payment-confirmation', [
                'student' => $student,
                'payment' => $payment
            ], function ($message) use ($student) {
                $message->to($student->email)
                        ->subject('Payment Confirmation - Edulink SmartFees');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Send payment confirmation SMS
     */
    private function sendPaymentSMS(Student $student, Payment $payment): void
    {
        $message = "Payment confirmed! KES " . number_format($payment->amount, 2) . " received via " . ucfirst($payment->payment_method) . ". Ref: " . $payment->payment_reference . " - Edulink";
        $this->sendSMS($student->phone, $message);
    }

    /**
     * Send SMS using Africa's Talking
     */
    private function sendSMS(string $phone, string $message): void
    {
        if (!config('services.notifications.sms_enabled', false)) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'apiKey' => config('services.sms.api_key'),
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json'
            ])->post('https://api.africastalking.com/version1/messaging', [
                'username' => config('services.sms.username'),
                'to' => $this->formatPhoneNumber($phone),
                'message' => $message,
                'from' => config('services.sms.sender_id', 'EDULINK')
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully to ' . $phone);
            } else {
                Log::error('Failed to send SMS: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('SMS sending error: ' . $e->getMessage());
        }
    }

    /**
     * Format phone number for SMS
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add Kenya country code if not present
        if (strlen($phone) === 9 && substr($phone, 0, 1) === '7') {
            $phone = '254' . $phone;
        } elseif (strlen($phone) === 10 && substr($phone, 0, 2) === '07') {
            $phone = '254' . substr($phone, 1);
        }
        
        return '+' . $phone;
    }
}