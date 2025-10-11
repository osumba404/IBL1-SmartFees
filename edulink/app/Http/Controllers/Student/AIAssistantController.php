<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIAssistantController extends Controller
{
    private $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    public function getAssistance(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:500'
        ]);

        $student = Auth::guard('student')->user();
        
        // Get student context for personalized responses
        $studentContext = [
            'student_id' => $student->student_id,
            'name' => $student->first_name . ' ' . $student->last_name,
            'outstanding_balance' => $student->getFinancialSummary()['outstanding_balance'] ?? 0,
            'recent_payments' => $student->payments()->latest()->take(3)->pluck('amount', 'created_at')->toArray(),
        ];

        $query = $request->input('query');
        $response = $this->groqService->generateStudentResponse($query, $studentContext);

        return response()->json([
            'response' => $response,
            'suggested_actions' => $this->getSuggestedActions($query)
        ]);
    }

    public function getPaymentInsights()
    {
        $student = Auth::guard('student')->user();
        $financialSummary = $student->getFinancialSummary();
        
        $recommendations = [];
        
        if ($financialSummary['outstanding_balance'] > 0) {
            $recommendations[] = "You have an outstanding balance of KES " . number_format($financialSummary['outstanding_balance'], 2);
        }
        
        $recentPayments = $student->payments()->latest()->take(5)->get();
        $mpesaCount = $recentPayments->where('payment_method', 'mpesa')->count();
        
        if ($mpesaCount >= 3) {
            $recommendations[] = "You frequently use M-Pesa. Consider saving your payment details for faster checkout";
        }
        
        if ($financialSummary['has_overdue_payments']) {
            $recommendations[] = "You have overdue payments. Pay now to avoid late fees";
        }

        return response()->json([
            'recommendations' => $recommendations
        ]);
    }

    private function getSuggestedActions($query)
    {
        if (!is_string($query)) {
            return [];
        }
        
        $query = strtolower($query);
        $actions = [];

        if (str_contains($query, 'payment') || str_contains($query, 'pay')) {
            $actions[] = 'Make Payment';
        }
        
        if (str_contains($query, 'balance') || str_contains($query, 'owe')) {
            $actions[] = 'View Balance';
        }
        
        if (str_contains($query, 'receipt') || str_contains($query, 'statement')) {
            $actions[] = 'Download Receipt';
        }

        return $actions;
    }
}