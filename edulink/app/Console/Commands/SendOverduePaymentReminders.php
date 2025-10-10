<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\PaymentNotification;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendOverduePaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:send-overdue-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders to students with overdue balances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for students with overdue payments...');
        
        $notificationService = new NotificationService();
        $remindersSent = 0;
        
        // Get students with outstanding balances
        $students = Student::whereHas('enrollments', function($query) {
            $query->whereRaw('total_fees_due > fees_paid')
                  ->where('status', 'active');
        })->with('enrollments')->get();
        
        foreach ($students as $student) {
            $totalOwed = $student->enrollments->sum('total_fees_due');
            $totalPaid = $student->enrollments->sum('fees_paid');
            $outstandingBalance = $totalOwed - $totalPaid;
            
            if ($outstandingBalance > 0) {
                // Send email reminder
                $notificationService->sendPaymentReminder($student, $outstandingBalance);
                
                // Create in-app notification
                PaymentNotification::create([
                    'student_id' => $student->id,
                    'title' => 'Overdue Payment Notice',
                    'message' => "Your payment of KES " . number_format($outstandingBalance, 2) . " is overdue. Please make your payment immediately to avoid additional charges.",
                    'notification_type' => 'payment_overdue',
                ]);
                
                $remindersSent++;
                $this->info("Reminder sent to: {$student->first_name} {$student->last_name} (KES " . number_format($outstandingBalance, 2) . ")");
            }
        }
        
        $this->info("Payment reminders sent: {$remindersSent}");
        return 0;
    }
}
