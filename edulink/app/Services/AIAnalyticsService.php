<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AIAnalyticsService
{
    /**
     * Analyze payment behavior patterns
     */
    public function analyzePaymentBehavior($studentId = null)
    {
        $query = Payment::with('student');
        
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        
        $payments = $query->where('created_at', '>=', now()->subMonths(6))->get();
        
        return [
            'payment_frequency' => $this->calculatePaymentFrequency($payments),
            'preferred_methods' => $this->getPreferredPaymentMethods($payments),
            'payment_timing' => $this->analyzePaymentTiming($payments),
            'amount_patterns' => $this->analyzeAmountPatterns($payments),
            'risk_score' => $this->calculateRiskScore($payments)
        ];
    }

    /**
     * Detect fraudulent payment patterns
     */
    public function detectFraud($payment)
    {
        $riskFactors = [];
        $riskScore = 0;

        // Check for unusual amount patterns
        $avgAmount = Payment::where('student_id', $payment->student_id)
            ->where('status', 'completed')
            ->avg('amount');
        
        if ($payment->amount > $avgAmount * 3) {
            $riskFactors[] = 'Unusually high amount';
            $riskScore += 30;
        }

        // Check for rapid successive payments
        $recentPayments = Payment::where('student_id', $payment->student_id)
            ->where('created_at', '>=', now()->subHours(1))
            ->count();
        
        if ($recentPayments > 3) {
            $riskFactors[] = 'Multiple payments in short time';
            $riskScore += 40;
        }

        // Check for failed payment attempts
        $failedAttempts = Payment::where('student_id', $payment->student_id)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        
        if ($failedAttempts > 5) {
            $riskFactors[] = 'Multiple failed attempts';
            $riskScore += 50;
        }

        return [
            'risk_score' => min($riskScore, 100),
            'risk_level' => $this->getRiskLevel($riskScore),
            'risk_factors' => $riskFactors,
            'requires_review' => $riskScore > 60
        ];
    }

    /**
     * Generate predictive analytics
     */
    public function generatePredictiveAnalytics()
    {
        return [
            'payment_predictions' => $this->predictPaymentTrends(),
            'enrollment_forecast' => $this->forecastEnrollments(),
            'revenue_projection' => $this->projectRevenue(),
            'at_risk_students' => $this->identifyAtRiskStudents()
        ];
    }

    /**
     * Generate automated support responses
     */
    public function generateSupportResponse($query, $context = [])
    {
        $responses = [
            'payment_failed' => "I understand your payment failed. This can happen due to insufficient funds, network issues, or card restrictions. Please try again or use a different payment method. If the issue persists, contact your bank or try M-Pesa.",
            'payment_pending' => "Your payment is currently being processed. M-Pesa payments usually complete within 2-3 minutes, while card payments may take up to 10 minutes. You'll receive a confirmation email once processed.",
            'receipt_request' => "You can download your payment receipt from your dashboard under 'Payment History'. If you need a specific receipt, please provide the transaction date and amount.",
            'installment_query' => "Yes, we offer installment payment plans. You can pay in up to 4 installments with a minimum 25% deposit. Contact the finance office to set up a payment plan.",
            'refund_request' => "Refund requests are processed within 5-7 business days. Please provide your transaction reference and reason for refund. Refunds are credited back to the original payment method."
        ];

        $queryLower = strtolower($query);
        
        foreach ($responses as $key => $response) {
            if ($this->matchesQuery($queryLower, $key)) {
                return [
                    'response' => $response,
                    'confidence' => 0.85,
                    'suggested_actions' => $this->getSuggestedActions($key),
                    'escalate' => false
                ];
            }
        }

        return [
            'response' => "I'd be happy to help you with your query. For specific account or payment issues, please contact our support team at support@edulink.ac.ke or call +254700000000.",
            'confidence' => 0.6,
            'suggested_actions' => ['Contact support', 'Check FAQ'],
            'escalate' => true
        ];
    }

    private function calculatePaymentFrequency($payments)
    {
        if ($payments->isEmpty()) return 0;
        
        $daysBetween = $payments->first()->created_at->diffInDays($payments->last()->created_at);
        return $daysBetween > 0 ? $payments->count() / $daysBetween : 0;
    }

    private function getPreferredPaymentMethods($payments)
    {
        return $payments->groupBy('payment_method')
            ->map->count()
            ->sortDesc()
            ->take(3);
    }

    private function analyzePaymentTiming($payments)
    {
        return $payments->groupBy(function($payment) {
            return $payment->created_at->format('H');
        })->map->count()->sortDesc();
    }

    private function analyzeAmountPatterns($payments)
    {
        $amounts = $payments->pluck('amount');
        $mode = $amounts->mode();
        return [
            'average' => $amounts->avg() ?? 0,
            'median' => $amounts->median() ?? 0,
            'most_common' => is_array($mode) && count($mode) > 0 ? $mode[0] : 0
        ];
    }

    private function calculateRiskScore($payments)
    {
        $failedRate = $payments->where('status', 'failed')->count() / max($payments->count(), 1);
        return min($failedRate * 100, 100);
    }

    private function getRiskLevel($score)
    {
        if ($score >= 80) return 'High';
        if ($score >= 60) return 'Medium';
        if ($score >= 30) return 'Low';
        return 'Minimal';
    }

    private function predictPaymentTrends()
    {
        $monthlyPayments = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->where('created_at', '>=', now()->subYear())
            ->where('status', 'completed')
            ->groupBy('month')
            ->get();

        $trend = $monthlyPayments->count() > 1 ? 
            ($monthlyPayments->last()->total - $monthlyPayments->first()->total) / $monthlyPayments->count() : 0;

        return [
            'trend' => $trend > 0 ? 'increasing' : ($trend < 0 ? 'decreasing' : 'stable'),
            'next_month_prediction' => $monthlyPayments->avg('total') + $trend
        ];
    }

    private function forecastEnrollments()
    {
        $enrollments = DB::table('student_enrollments')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->get();

        return [
            'trend' => 'stable',
            'next_month_forecast' => $enrollments->avg('count')
        ];
    }

    private function projectRevenue()
    {
        $monthlyRevenue = Payment::selectRaw('SUM(amount) as revenue')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(3))
            ->first();

        return [
            'next_quarter' => $monthlyRevenue->revenue * 3,
            'confidence' => 0.75
        ];
    }

    private function identifyAtRiskStudents()
    {
        return Student::whereHas('enrollments', function($query) {
            $query->where('status', 'active');
        })->whereDoesntHave('payments', function($query) {
            $query->where('created_at', '>=', now()->subMonths(2))
                  ->where('status', 'completed');
        })->limit(10)->get();
    }

    private function matchesQuery($query, $type)
    {
        $keywords = [
            'payment_failed' => ['failed', 'error', 'declined', 'rejected'],
            'payment_pending' => ['pending', 'processing', 'waiting'],
            'receipt_request' => ['receipt', 'invoice', 'proof'],
            'installment_query' => ['installment', 'payment plan', 'partial'],
            'refund_request' => ['refund', 'cancel', 'return']
        ];

        foreach ($keywords[$type] ?? [] as $keyword) {
            if (str_contains($query, $keyword)) {
                return true;
            }
        }
        return false;
    }

    private function getSuggestedActions($type)
    {
        $actions = [
            'payment_failed' => ['Try different payment method', 'Check account balance', 'Contact bank'],
            'payment_pending' => ['Wait for confirmation', 'Check email', 'Refresh page'],
            'receipt_request' => ['Visit dashboard', 'Check email', 'Download from history'],
            'installment_query' => ['Contact finance office', 'Submit application', 'Provide documents'],
            'refund_request' => ['Submit refund form', 'Provide transaction details', 'Wait for processing']
        ];

        return $actions[$type] ?? ['Contact support'];
    }
}