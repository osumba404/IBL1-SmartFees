<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentInstallment;
use App\Services\NotificationService;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders';
    protected $description = 'Send payment reminders for upcoming installments';

    public function handle()
    {
        $this->info('Sending payment reminders...');

        $notificationService = new NotificationService();
        
        // Get installments due in 3 days
        $upcomingInstallments = PaymentInstallment::where('status', 'pending')
            ->whereBetween('due_date', [now()->addDays(2), now()->addDays(4)])
            ->with(['paymentPlan.enrollment.student'])
            ->get();

        $reminderCount = 0;
        foreach ($upcomingInstallments as $installment) {
            $student = $installment->paymentPlan->enrollment->student;
            $notificationService->sendPaymentReminder($student, $installment);
            $reminderCount++;
        }

        $this->info("Sent {$reminderCount} payment reminders");
    }
}