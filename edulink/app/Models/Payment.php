<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment Model for Edulink International College Nairobi
 * 
 * Tracks all student payments including M-Pesa and Stripe transactions
 * Handles payment verification, refunds, and gateway integration
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'student_enrollment_id',
        'payment_reference',
        'transaction_id',
        'receipt_number',
        'amount',
        'currency',
        'payment_method',
        'payment_type',
        'gateway_transaction_id',
        'gateway_reference',
        'gateway_response',
        'mpesa_receipt_number',
        'mpesa_transaction_date',
        'mpesa_phone_number',
        'mpesa_transaction_cost',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'stripe_customer_id',
        'stripe_metadata',
        'bank_name',
        'bank_reference',
        'bank_transaction_date',
        'status',
        'is_verified',
        'verified_at',
        'verified_by',
        'payment_date',
        'processed_at',
        'value_date',
        'fee_breakdown',
        'outstanding_balance_before',
        'outstanding_balance_after',
        'is_refunded',
        'refund_amount',
        'refunded_at',
        'refund_reason',
        'refund_reference',
        'is_late_payment',
        'late_payment_penalty',
        'days_late',
        'payment_notes',
        'admin_notes',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'mpesa_transaction_cost' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'late_payment_penalty' => 'decimal:2',
            'outstanding_balance_before' => 'decimal:2',
            'outstanding_balance_after' => 'decimal:2',
            'payment_date' => 'datetime',
            'processed_at' => 'datetime',
            'verified_at' => 'datetime',
            'refunded_at' => 'datetime',
            'value_date' => 'date',
            'bank_transaction_date' => 'date',
            'gateway_response' => 'array',
            'stripe_metadata' => 'array',
            'fee_breakdown' => 'array',
            'is_verified' => 'boolean',
            'is_refunded' => 'boolean',
            'is_late_payment' => 'boolean',
        ];
    }

    /**
     * Get the student for this payment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the enrollment for this payment.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    /**
     * Get the admin who verified this payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is from M-Pesa.
     */
    public function isMpesaPayment(): bool
    {
        return $this->payment_method === 'mpesa';
    }

    /**
     * Check if payment is from Stripe.
     */
    public function isStripePayment(): bool
    {
        return $this->payment_method === 'stripe';
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        // Update enrollment payment status
        if ($this->enrollment) {
            $this->enrollment->updatePaymentStatus($this->amount);
        }

        // Update student financial summary
        if ($this->student) {
            $this->student->updateFinancialSummary();
        }
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'processed_at' => now(),
            'admin_notes' => $reason,
        ]);
    }

    /**
     * Verify payment.
     */
    public function verify(Admin $admin): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        if ($this->status === 'pending') {
            $this->markAsCompleted();
        }
    }

    /**
     * Process refund.
     */
    public function processRefund(float $amount, string $reason, string $reference = null): void
    {
        $this->update([
            'is_refunded' => true,
            'refund_amount' => $amount,
            'refund_reason' => $reason,
            'refund_reference' => $reference,
            'refunded_at' => now(),
            'status' => 'refunded',
        ]);

        // Update enrollment payment status (subtract refunded amount)
        if ($this->enrollment) {
            $this->enrollment->updatePaymentStatus(-$amount);
        }

        // Update student financial summary
        if ($this->student) {
            $this->student->updateFinancialSummary();
        }
    }

    /**
     * Generate unique payment reference.
     */
    public static function generatePaymentReference(): string
    {
        $year = date('Y');
        $lastPayment = self::where('payment_reference', 'like', "PAY{$year}%")
            ->orderBy('payment_reference', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_reference, -6);
            $newNumber = str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "PAY{$year}{$newNumber}";
    }

    /**
     * Get payment method display name.
     */
    public function getPaymentMethodDisplayAttribute(): string
    {
        return match($this->payment_method) {
            'mpesa' => 'M-Pesa',
            'stripe' => 'Credit/Debit Card',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            default => ucfirst($this->payment_method),
        };
    }

    /**
     * Get payment type display name.
     */
    public function getPaymentTypeDisplayAttribute(): string
    {
        return match($this->payment_type) {
            'tuition' => 'Tuition Fee',
            'registration' => 'Registration Fee',
            'examination' => 'Examination Fee',
            'library' => 'Library Fee',
            'lab' => 'Laboratory Fee',
            'activity' => 'Activity Fee',
            'accommodation' => 'Accommodation Fee',
            default => ucfirst($this->payment_type),
        };
    }

    /**
     * Get status badge color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'processing' => 'info',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'refunded' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Scope for completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified payments.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for unverified payments.
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope for M-Pesa payments.
     */
    public function scopeMpesa($query)
    {
        return $query->where('payment_method', 'mpesa');
    }

    /**
     * Scope for Stripe payments.
     */
    public function scopeStripe($query)
    {
        return $query->where('payment_method', 'stripe');
    }

    /**
     * Scope for payments within date range.
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope for late payments.
     */
    public function scopeLatePayments($query)
    {
        return $query->where('is_late_payment', true);
    }
}
