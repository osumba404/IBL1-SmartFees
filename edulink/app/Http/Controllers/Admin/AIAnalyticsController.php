<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AIAnalyticsController extends Controller
{
    protected $aiService;

    public function __construct(AIAnalyticsService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * AI Analytics Dashboard
     */
    public function index(): View
    {
        $analytics = $this->aiService->generatePredictiveAnalytics();
        $paymentBehavior = $this->aiService->analyzePaymentBehavior();
        
        return view('admin.ai-analytics.index', compact('analytics', 'paymentBehavior'));
    }

    /**
     * Fraud Detection Dashboard
     */
    public function fraudDetection(): View
    {
        $suspiciousPayments = \App\Models\Payment::where('created_at', '>=', now()->subDays(7))
            ->get()
            ->map(function($payment) {
                $fraudAnalysis = $this->aiService->detectFraud($payment);
                $payment->fraud_analysis = $fraudAnalysis;
                return $payment;
            })
            ->where('fraud_analysis.risk_score', '>', 30)
            ->sortByDesc('fraud_analysis.risk_score');

        return view('admin.ai-analytics.fraud-detection', compact('suspiciousPayments'));
    }

    /**
     * Payment Behavior Analysis
     */
    public function paymentBehavior(Request $request): View
    {
        $studentId = $request->get('student_id');
        $analysis = $this->aiService->analyzePaymentBehavior($studentId);
        
        return view('admin.ai-analytics.payment-behavior', compact('analysis', 'studentId'));
    }

    /**
     * Automated Support Dashboard
     */
    public function supportDashboard(): View
    {
        // Mock support queries for demonstration
        $queries = [
            ['query' => 'My payment failed', 'student' => 'John Doe', 'time' => now()->subMinutes(5)],
            ['query' => 'Need payment receipt', 'student' => 'Jane Smith', 'time' => now()->subMinutes(15)],
            ['query' => 'Payment is pending', 'student' => 'Mike Johnson', 'time' => now()->subMinutes(30)]
        ];

        $responses = collect($queries)->map(function($query) {
            $response = $this->aiService->generateSupportResponse($query['query']);
            $query['ai_response'] = $response;
            return $query;
        });

        return view('admin.ai-analytics.support-dashboard', compact('responses'));
    }

    /**
     * Generate AI Response (AJAX)
     */
    public function generateResponse(Request $request)
    {
        $query = $request->input('query');
        $context = $request->input('context', []);
        
        $response = $this->aiService->generateSupportResponse($query, $context);
        
        return response()->json($response);
    }
}