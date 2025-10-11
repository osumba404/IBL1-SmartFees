<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_plan_id',
        'installment_number',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'late_fee',
        'payment_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date'
    ];

    public function paymentPlan()
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->late_fee;
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->status !== 'overdue') {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'pending' && now()->isAfter($this->due_date);
    }

    public function markAsOverdue()
    {
        if ($this->status === 'pending' && now()->isAfter($this->due_date)) {
            $this->update(['status' => 'overdue']);
            return true;
        }
        return false;
    }

    public function markAsPaid($paymentId = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_id' => $paymentId
        ]);

        // Update payment plan
        $this->paymentPlan->increment('completed_installments');
        $this->paymentPlan->increment('paid_amount', $this->total_amount);
        $this->paymentPlan->updateStatus();
    }
}