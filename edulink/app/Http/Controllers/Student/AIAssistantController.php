<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\AIAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AIAssistantController extends Controller
{
    protected $aiService;

    public function __construct(AIAnalyticsService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get AI assistance for student queries
     */
    public function getAssistance(Request $request)
    {
        $query = $request->input('query');
        $student = Auth::guard('student')->user();
        
        $context = [
            'student_id' => $student->id,
            'has_pending_payments' => $student->payments()->where('status', 'pending')->exists(),
            'last_payment' => $student->payments()->latest()->first()
        ];
        
        $response = $this->aiService->generateSupportResponse($query, $context);
        
        return response()->json($response);
    }

    /**
     * Get personalized payment insights
     */
    public function getPaymentInsights()
    {
        $student = Auth::guard('student')->user();
        $insights = $this->aiService->analyzePaymentBehavior($student->id);
        
        return response()->json([
            'insights' => $insights,
            'recommendations' => $this->generateRecommendations($insights)
        ]);
    }

    private function generateRecommendations($insights)
    {
        $recommendations = [];
        
        if ($insights['risk_score'] > 50) {
            $recommendations[] = 'Consider setting up automatic payment reminders';
        }
        
        if (isset($insights['preferred_methods']['mpesa']) && $insights['preferred_methods']['mpesa'] > 5) {
            $recommendations[] = 'You frequently use M-Pesa. Consider saving your payment details for faster checkout';
        }
        
        return $recommendations;
    }
}