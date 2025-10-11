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

    /**
     * Generate AI-powered recommendations
     */
    public function generateRecommendations()
    {
        $recommendations = [];
        
        // Analyze payment data for recommendations
        $totalPayments = Payment::count();
        $failedPayments = Payment::where('status', 'failed')->count();
        $overdueStudents = Student::whereHas('enrollments', function($query) {
            $query->where('status', 'active');
        })->whereDoesntHave('payments', function($query) {
            $query->where('created_at', '>=', now()->subMonths(1))
                  ->where('status', 'completed');
        })->count();
        
        $mpesaPayments = Payment::where('payment_method', 'mpesa')->where('status', 'completed')->count();
        $stripePayments = Payment::where('payment_method', 'stripe')->where('status', 'completed')->count();
        
        // Generate dynamic recommendations based on data
        if ($failedPayments > 0 && $totalPayments > 0) {
            $failureRate = ($failedPayments / $totalPayments) * 100;
            if ($failureRate > 10) {
                $recommendations[] = [
                    'type' => 'warning',
                    'icon' => 'bi-exclamation-triangle',
                    'title' => 'High Payment Failure Rate',
                    'message' => "Payment failure rate is " . number_format($failureRate, 1) . "%. Consider reviewing payment gateway configurations and providing clearer payment instructions."
                ];
            }
        }
        
        if ($overdueStudents > 0) {
            $recommendations[] = [
                'type' => 'info',
                'icon' => 'bi-clock',
                'title' => 'Overdue Payment Management',
                'message' => "{$overdueStudents} students haven't made payments recently. Implement automated reminder system to improve collection rates."
            ];
        }
        
        if ($mpesaPayments > $stripePayments && $mpesaPayments > 0) {
            $mpesaTotal = Payment::where('payment_method', 'mpesa')->count();
            $mpesaSuccessRate = $mpesaTotal > 0 ? ($mpesaPayments / $mpesaTotal) * 100 : 0;
            if ($mpesaSuccessRate > 90) {
                $recommendations[] = [
                    'type' => 'success',
                    'icon' => 'bi-graph-up',
                    'title' => 'M-Pesa Performance Excellence',
                    'message' => "M-Pesa has a " . number_format($mpesaSuccessRate, 1) . "% success rate. Promote this payment method to reduce transaction failures."
                ];
            }
        }
        
        // Add default recommendations if no data-driven ones
        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'info',
                'icon' => 'bi-lightbulb',
                'title' => 'Payment Optimization',
                'message' => 'Consider implementing payment plans and automated reminders to improve collection efficiency.'
            ];
            
            $recommendations[] = [
                'type' => 'success',
                'icon' => 'bi-shield-check',
                'title' => 'System Performance',
                'message' => 'Payment system is operating normally. Monitor trends for continuous improvement.'
            ];
        }
        
        return $recommendations;
    }

    /**
     * Get detailed metrics for interactive dashboard
     */
    public function getDetailedMetrics()
    {
        return [
            'payment_trends' => $this->getPaymentTrends(30),
            'revenue_forecast' => $this->getRevenueForecast(90),
            'risk_analysis' => $this->getRiskAnalysis(),
            'payment_methods' => $this->getPaymentMethodAnalysis(30)
        ];
    }

    /**
     * Get payment trends with detailed breakdown
     */
    public function getPaymentTrends($days = 30)
    {
        $payments = Payment::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total, payment_method, status')
            ->groupBy('date', 'payment_method', 'status')
            ->orderBy('date')
            ->get();

        $dailyTotals = $payments->groupBy('date')->map(function($dayPayments) {
            return [
                'total_amount' => $dayPayments->sum('total'),
                'total_count' => $dayPayments->sum('count'),
                'success_rate' => $dayPayments->where('status', 'completed')->sum('count') / max($dayPayments->sum('count'), 1) * 100
            ];
        });

        return [
            'daily_data' => $dailyTotals,
            'trend_direction' => $this->calculateTrendDirection($dailyTotals),
            'growth_rate' => $this->calculateGrowthRate($dailyTotals),
            'peak_days' => $dailyTotals->sortByDesc('total_amount')->take(3)->keys()
        ];
    }

    /**
     * Get revenue forecast with confidence intervals
     */
    public function getRevenueForecast($days = 90)
    {
        $historicalData = Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $avgDaily = $historicalData->avg('revenue');
        $trend = $this->calculateTrendDirection($historicalData->pluck('revenue', 'date'));
        
        return [
            'next_week' => $avgDaily * 7 * ($trend === 'increasing' ? 1.1 : ($trend === 'decreasing' ? 0.9 : 1)),
            'next_month' => $avgDaily * 30 * ($trend === 'increasing' ? 1.15 : ($trend === 'decreasing' ? 0.85 : 1)),
            'next_quarter' => $avgDaily * 90 * ($trend === 'increasing' ? 1.2 : ($trend === 'decreasing' ? 0.8 : 1)),
            'confidence' => $historicalData->count() > 30 ? 'high' : ($historicalData->count() > 10 ? 'medium' : 'low'),
            'factors' => $this->getRevenueForecastFactors()
        ];
    }

    /**
     * Get comprehensive risk analysis
     */
    public function getRiskAnalysis()
    {
        $students = Student::with(['payments' => function($query) {
            $query->where('created_at', '>=', now()->subMonths(3));
        }])->get();

        $riskCategories = [
            'high_risk' => 0,
            'medium_risk' => 0,
            'low_risk' => 0,
            'no_risk' => 0
        ];

        $riskFactors = [];

        foreach ($students as $student) {
            $riskScore = $this->calculateStudentRiskScore($student);
            
            if ($riskScore >= 70) {
                $riskCategories['high_risk']++;
                $riskFactors[] = $this->getStudentRiskFactors($student);
            } elseif ($riskScore >= 40) {
                $riskCategories['medium_risk']++;
            } elseif ($riskScore >= 20) {
                $riskCategories['low_risk']++;
            } else {
                $riskCategories['no_risk']++;
            }
        }

        return [
            'categories' => $riskCategories,
            'total_students' => $students->count(),
            'avg_risk_score' => $students->avg(function($student) {
                return $this->calculateStudentRiskScore($student);
            }),
            'top_risk_factors' => collect($riskFactors)->flatten()->countBy()->sortDesc()->take(5)
        ];
    }

    /**
     * Get payment method analysis with performance metrics
     */
    public function getPaymentMethodAnalysis($days = 30)
    {
        $payments = Payment::where('created_at', '>=', now()->subDays($days))->get();
        
        $methodStats = $payments->groupBy('payment_method')->map(function($methodPayments, $method) {
            $total = $methodPayments->count();
            $completed = $methodPayments->where('status', 'completed')->count();
            $failed = $methodPayments->where('status', 'failed')->count();
            
            return [
                'total_transactions' => $total,
                'success_rate' => $total > 0 ? ($completed / $total) * 100 : 0,
                'failure_rate' => $total > 0 ? ($failed / $total) * 100 : 0,
                'total_amount' => $methodPayments->where('status', 'completed')->sum('amount'),
                'avg_amount' => $methodPayments->where('status', 'completed')->avg('amount') ?? 0,
                'avg_processing_time' => $this->calculateAvgProcessingTime($methodPayments)
            ];
        });

        return [
            'method_stats' => $methodStats,
            'recommended_method' => $methodStats->sortByDesc('success_rate')->keys()->first(),
            'fastest_method' => $methodStats->sortBy('avg_processing_time')->keys()->first()
        ];
    }

    private function calculateTrendDirection($data)
    {
        if ($data->count() < 2) return 'stable';
        
        $values = $data->values();
        $first = is_array($values->first()) ? $values->first()['total_amount'] ?? $values->first() : $values->first();
        $last = is_array($values->last()) ? $values->last()['total_amount'] ?? $values->last() : $values->last();
        
        $change = ($last - $first) / max($first, 1) * 100;
        
        if ($change > 5) return 'increasing';
        if ($change < -5) return 'decreasing';
        return 'stable';
    }

    private function calculateGrowthRate($data)
    {
        if ($data->count() < 2) return 0;
        
        $values = $data->values();
        $first = is_array($values->first()) ? $values->first()['total_amount'] ?? 0 : $values->first();
        $last = is_array($values->last()) ? $values->last()['total_amount'] ?? 0 : $values->last();
        
        return $first > 0 ? (($last - $first) / $first) * 100 : 0;
    }

    private function getRevenueForecastFactors()
    {
        return [
            'seasonal_trends' => 'Academic calendar affects payment patterns',
            'enrollment_growth' => 'New student registrations impact revenue',
            'payment_method_adoption' => 'M-Pesa adoption increases success rates',
            'economic_factors' => 'Local economic conditions may affect payments'
        ];
    }

    private function calculateStudentRiskScore($student)
    {
        $score = 0;
        
        // No recent payments
        if ($student->payments->where('created_at', '>=', now()->subMonth())->isEmpty()) {
            $score += 30;
        }
        
        // High failure rate
        $failureRate = $student->payments->where('status', 'failed')->count() / max($student->payments->count(), 1);
        $score += $failureRate * 40;
        
        // Overdue payments
        if ($student->payments->where('status', 'pending')->where('created_at', '<=', now()->subDays(7))->count() > 0) {
            $score += 20;
        }
        
        return min($score, 100);
    }

    private function getStudentRiskFactors($student)
    {
        $factors = [];
        
        if ($student->payments->where('created_at', '>=', now()->subMonth())->isEmpty()) {
            $factors[] = 'No recent payments';
        }
        
        if ($student->payments->where('status', 'failed')->count() > 2) {
            $factors[] = 'Multiple failed payments';
        }
        
        return $factors;
    }

    private function calculateAvgProcessingTime($payments)
    {
        // Simulate processing time calculation
        $method = $payments->first()->payment_method ?? 'unknown';
        
        $avgTimes = [
            'mpesa' => 2.5,
            'stripe' => 8.3,
            'paypal' => 12.1,
            'bank_transfer' => 1440, // 24 hours
            'cash' => 0
        ];
        
        return $avgTimes[$method] ?? 10;
    }
}