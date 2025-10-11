<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentInstallment;
use App\Models\PaymentPlan;
use App\Services\NotificationService;

class ProcessLateFees extends Command
{
    protected $signature = 'payments:process-late-fees';
    protected $description = 'Process late fees for overdue installments';

    public function handle()
    {
        $this->info('Processing late fees...');

        // Mark overdue installments
        $overdueCount = PaymentInstallment::where('status', 'pending')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $this->info("Marked {$overdueCount} installments as overdue");

        // Calculate late fees
        $paymentPlans = PaymentPlan::whereHas('installments', function($query) {
            $query->where('status', 'overdue');
        })->get();

        $totalLateFees = 0;
        foreach ($paymentPlans as $plan) {
            $lateFees = $plan->calculateLateFees();
            $totalLateFees += $lateFees;
            $plan->updateStatus();
        }

        $this->info("Calculated late fees: KES " . number_format($totalLateFees, 2));

        // Send notifications
        $notificationService = new NotificationService();
        foreach ($paymentPlans as $plan) {
            if ($plan->status === 'overdue') {
                $notificationService->sendPaymentReminder(
                    $plan->enrollment->student,
                    $plan->next_installment
                );
            }
        }

        $this->info('Late fee processing completed');
    }
}