<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('enrollments')->get();
        
        if ($students->isEmpty()) {
            $this->command->info('No students found. Please run StudentSeeder first.');
            return;
        }

        $paymentMethods = ['mpesa', 'stripe', 'bank_transfer', 'cash'];
        $statuses = ['completed', 'pending', 'failed'];
        
        foreach ($students->take(10) as $student) {
            $enrollment = $student->enrollments->first();
            if (!$enrollment) continue;

            // Create 2-3 payments per student
            for ($i = 0; $i < rand(2, 3); $i++) {
                $amount = rand(5000, 50000);
                $method = $paymentMethods[array_rand($paymentMethods)];
                $status = $statuses[array_rand($statuses)];
                
                $transactionId = strtoupper(uniqid('TXN'));
                
                Payment::create([
                    'student_id' => $student->id,
                    'student_enrollment_id' => $enrollment->id,
                    'payment_reference' => 'PAY' . date('Y') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'currency' => 'KES',
                    'payment_method' => $method,
                    'payment_type' => 'tuition',
                    'status' => $status,
                    'is_verified' => $status === 'completed',
                    'payment_date' => Carbon::now()->subDays(rand(0, 30)),
                    'processed_at' => $status === 'completed' ? Carbon::now()->subDays(rand(0, 30)) : null,
                    'verified_at' => $status === 'completed' ? Carbon::now()->subDays(rand(0, 30)) : null,
                    'outstanding_balance_before' => $enrollment->total_fees_due ?? 0,
                    'outstanding_balance_after' => max(0, ($enrollment->total_fees_due ?? 0) - $amount),
                ]);
            }
        }

        $this->command->info('Sample payments created successfully!');
    }
}