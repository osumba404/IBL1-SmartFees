<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\PaymentNotification;
use App\Services\NotificationService;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders to students with outstanding balances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending payment reminders...');
        
        $notificationService = new NotificationService();
        $remindersSent = 0;
        
        // Get students with outstanding balances
        $enrollments = StudentEnrollment::with(['student', 'course'])
            ->whereRaw('total_fees_due > fees_paid')
            ->where('status', 'active')
            ->get();
        
        foreach ($enrollments as $enrollment) {
            $outstandingAmount = $enrollment->total_fees_due - $enrollment->fees_paid;
            
            if ($outstandingAmount > 0) {
                // Send email reminder
                $notificationService->sendPaymentReminder(
                    $enrollment->student,
                    $outstandingAmount
                );
                
                // Create in-app notification
                PaymentNotification::create([
                    'student_id' => $enrollment->student->id,
                    'title' => 'Payment Reminder',
                    'message' => "You have an outstanding balance of KES " . number_format($outstandingAmount, 2) . " for {$enrollment->course->name}. Please make your payment to avoid late fees.",
                    'notification_type' => 'payment_reminder',
                ]);
                
                $remindersSent++;
                $this->info("Reminder sent to {$enrollment->student->email}");
            }
        }
        
        $this->info("Payment reminders sent to {$remindersSent} students.");
        
        return 0;
    }
}
