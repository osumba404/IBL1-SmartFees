<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentInstallment;
use App\Models\PaymentNotification;
use App\Services\NotificationService;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders';
    protected $description = 'Send payment reminders for due installments';

    public function handle()
    {
        $this->info('Checking for payment reminders...');
        
        $notificationService = new NotificationService();
        $today = Carbon::today();
        $reminderDays = [7, 3, 1, 0]; // Days before due date to send reminders
        
        foreach ($reminderDays as $days) {
            $targetDate = $today->copy()->addDays($days);
            
            $installments = PaymentInstallment::where('status', 'pending')
                ->whereDate('due_date', $targetDate)
                ->with(['paymentPlan.student', 'paymentPlan.enrollment.course'])
                ->get();
            
            foreach ($installments as $installment) {
                $student = $installment->paymentPlan->student;
                $course = $installment->paymentPlan->enrollment->course;
                
                if ($days == 0) {
                    $message = "Payment Due Today! Your installment of KES " . number_format($installment->amount, 2) . " for {$course->name} is due today.";
                    $title = "Payment Due Today";
                } else {
                    $message = "Payment Reminder: Your installment of KES " . number_format($installment->amount, 2) . " for {$course->name} is due in {$days} day(s).";
                    $title = "Payment Due in {$days} Day(s)";
                }
                
                // Send email notification
                $notificationService->sendPaymentReminder($student, $installment, $days);
                
                // Create in-app notification
                PaymentNotification::create([
                    'student_id' => $student->id,
                    'title' => $title,
                    'message' => $message,
                    'notification_type' => 'payment_reminder',
                ]);
                
                $this->info("Reminder sent to {$student->email} for installment due " . ($days == 0 ? 'today' : "in {$days} days"));
            }
        }
        
        // Check for overdue payments
        $overdueInstallments = PaymentInstallment::where('status', 'pending')
            ->where('due_date', '<', $today)
            ->with(['paymentPlan.student', 'paymentPlan.enrollment.course'])
            ->get();
            
        foreach ($overdueInstallments as $installment) {
            $student = $installment->paymentPlan->student;
            $course = $installment->paymentPlan->enrollment->course;
            $daysOverdue = $today->diffInDays(Carbon::parse($installment->due_date));
            
            // Update status to overdue
            $installment->update(['status' => 'overdue']);
            
            $message = "Overdue Payment: Your installment of KES " . number_format($installment->amount, 2) . " for {$course->name} is {$daysOverdue} day(s) overdue.";
            
            // Send overdue notification
            $notificationService->sendOverdueNotification($student, $installment, $daysOverdue);
            
            // Create in-app notification
            PaymentNotification::create([
                'student_id' => $student->id,
                'title' => 'Overdue Payment',
                'message' => $message,
                'notification_type' => 'payment_overdue',
            ]);
            
            $this->info("Overdue notice sent to {$student->email} for installment {$daysOverdue} days overdue");
        }
        
        $this->info('Payment reminders completed.');
    }
}