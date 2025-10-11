<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    private $apiKey;
    private $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
    }

    public function chat($messages, $model = 'llama-3.1-8b-instant')
    {
        try {
            if (!$this->apiKey) {
                Log::error('Groq API key not configured');
                return null;
            }

            $payload = [
                'model' => $model,
                'messages' => $messages,
                'max_tokens' => 1024,
                'temperature' => 0.7,
            ];

            Log::info('Groq API Payload:', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl . '/chat/completions', $payload);

            Log::info('Groq API Status:', ['status' => $response->status()]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Groq API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Groq Service Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function generateStudentResponse($query, $studentContext = [])
    {
        try {
            $systemPrompt = "You are the official AI assistant for Edulink International College Nairobi's SmartFees student portal system.

STRICT INSTRUCTIONS:
- ONLY respond to questions about: payments, fees, enrollment, course information, student account issues, and college services
- DO NOT respond to: personal advice, general knowledge, non-college topics, inappropriate content, or requests outside the fee management system
- Keep responses under 150 words and professional
- Always mention contacting support@edulink.ac.ke for complex issues
- Use student's actual data when available
- If asked about non-college topics, politely redirect to fee/enrollment questions

COLLEGE INFO:
- Name: Edulink International College Nairobi
- Currency: KES (Kenyan Shillings)
- Payment methods: M-Pesa, Stripe, Bank Transfer, Cash
- Support: support@edulink.ac.ke, +254 700 000 000
- Office hours: Monday-Friday 8AM-5PM EAT";

            $contextInfo = '';
            if (!empty($studentContext)) {
                $contextInfo = "\n\nSTUDENT DATA:\n" . 
                    "- Student ID: " . ($studentContext['student_id'] ?? 'N/A') . "\n" .
                    "- Name: " . ($studentContext['name'] ?? 'N/A') . "\n" .
                    "- Outstanding Balance: KES " . number_format($studentContext['outstanding_balance'] ?? 0, 2) . "\n" .
                    "- Recent Payments: " . (empty($studentContext['recent_payments']) ? 'None' : count($studentContext['recent_payments']) . ' payments');
            }

            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt . $contextInfo
                ],
                [
                    'role' => 'user',
                    'content' => (string) $query
                ]
            ];

            Log::info('Groq API Request:', ['messages' => $messages]);
            $response = $this->chat($messages);
            Log::info('Groq API Response:', ['response' => $response]);
            
            if ($response && isset($response['choices'][0]['message']['content'])) {
                return $response['choices'][0]['message']['content'];
            }

            return "I'm currently unavailable. Please contact support@edulink.ac.ke or call +254 700 000 000 for assistance with your fee management needs.";
        } catch (\Exception $e) {
            Log::error('Groq Service Error: ' . $e->getMessage());
            return "I'm currently unavailable. Please contact support@edulink.ac.ke or call +254 700 000 000 for assistance with your fee management needs.";
        }
    }
}