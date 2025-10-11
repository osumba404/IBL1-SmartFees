<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_enrollment_id',
        'plan_name',
        'total_amount',
        'paid_amount',
        'total_installments',
        'completed_installments',
        'status',
        'late_fee_rate',
        'late_fee_amount',
        'grace_period_days'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee_rate' => 'decimal:2',
        'late_fee_amount' => 'decimal:2'
    ];

    public function enrollment()
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function installments()
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function getProgressPercentageAttribute()
    {
        return $this->total_amount > 0 ? ($this->paid_amount / $this->total_amount) * 100 : 0;
    }

    public function getNextInstallmentAttribute()
    {
        return $this->installments()->where('status', 'pending')->orderBy('due_date')->first();
    }

    public function getOverdueInstallmentsAttribute()
    {
        return $this->installments()->where('status', 'overdue')->get();
    }

    public function calculateLateFees()
    {
        $overdueInstallments = $this->installments()->where('status', 'overdue')->get();
        $totalLateFees = 0;

        foreach ($overdueInstallments as $installment) {
            $daysOverdue = now()->diffInDays($installment->due_date);
            if ($daysOverdue > $this->grace_period_days) {
                $lateFee = ($installment->amount * $this->late_fee_rate) / 100;
                $installment->update(['late_fee' => $lateFee]);
                $totalLateFees += $lateFee;
            }
        }

        $this->update(['late_fee_amount' => $totalLateFees]);
        return $totalLateFees;
    }

    public function updateStatus()
    {
        if ($this->completed_installments >= $this->total_installments) {
            $this->update(['status' => 'completed']);
        } elseif ($this->installments()->where('status', 'overdue')->exists()) {
            $this->update(['status' => 'overdue']);
        } else {
            $this->update(['status' => 'active']);
        }
    }
}