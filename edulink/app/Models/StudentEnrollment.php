<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * StudentEnrollment Model for Edulink International College Nairobi
 * 
 * Links students to courses and semesters with enrollment details
 * Manages payment plans and academic progress tracking
 */
class StudentEnrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'course_id',
        'semester_id',
        'enrollment_number',
        'enrollment_date',
        'enrollment_type',
        'status',
        'status_change_date',
        'status_change_reason',
        'total_fees_due',
        'fees_paid',
        'outstanding_balance',
        'fees_fully_paid',
        'payment_plan',
        'installment_count',
        'installment_amount',
        'next_payment_due',
        'gpa',
        'credits_enrolled',
        'credits_completed',
        'is_deferred',
        'deferment_start_date',
        'deferment_end_date',
        'deferment_reason',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'enrollment_date' => 'date',
            'status_change_date' => 'date',
            'next_payment_due' => 'date',
            'deferment_start_date' => 'date',
            'deferment_end_date' => 'date',
            'total_fees_due' => 'decimal:2',
            'fees_paid' => 'decimal:2',
            'outstanding_balance' => 'decimal:2',
            'installment_amount' => 'decimal:2',
            'gpa' => 'decimal:2',
            'fees_fully_paid' => 'boolean',
            'is_deferred' => 'boolean',
        ];
    }

    /**
     * Get the student for this enrollment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for this enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the semester for this enrollment.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get all payments for this enrollment.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get completed payments for this enrollment.
     */
    public function completedPayments(): HasMany
    {
        return $this->payments()->where('status', 'completed');
    }

    /**
     * Check if enrollment is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'enrolled';
    }

    /**
     * Check if payment is overdue.
     */
    public function isPaymentOverdue(): bool
    {
        if ($this->fees_fully_paid || !$this->next_payment_due) {
            return false;
        }

        return now()->toDateString() > $this->next_payment_due;
    }

    /**
     * Get days until next payment due.
     */
    public function getDaysUntilPaymentDue(): int
    {
        if (!$this->next_payment_due) {
            return 0;
        }

        return now()->diffInDays($this->next_payment_due, false);
    }

    /**
     * Get days past payment due date.
     */
    public function getDaysPastDue(): int
    {
        if (!$this->isPaymentOverdue()) {
            return 0;
        }

        return $this->next_payment_due->diffInDays(now());
    }

    /**
     * Calculate remaining installments.
     */
    public function getRemainingInstallments(): int
    {
        if ($this->payment_plan !== 'installments' || !$this->installment_count) {
            return 0;
        }

        $paidInstallments = $this->completedPayments()->count();
        return max(0, $this->installment_count - $paidInstallments);
    }

    /**
     * Update payment status after payment.
     */
    public function updatePaymentStatus(float $paymentAmount): void
    {
        $this->fees_paid += $paymentAmount;
        $this->outstanding_balance = $this->total_fees_due - $this->fees_paid;
        $this->fees_fully_paid = $this->outstanding_balance <= 0;

        // Calculate next payment due date for installments
        if ($this->payment_plan === 'installments' && !$this->fees_fully_paid) {
            $remainingInstallments = $this->getRemainingInstallments();
            if ($remainingInstallments > 0) {
                $this->next_payment_due = now()->addMonth();
            }
        }

        $this->save();
    }

    /**
     * Defer enrollment.
     */
    public function defer(string $reason, ?\DateTime $startDate = null, ?\DateTime $endDate = null): void
    {
        $this->update([
            'status' => 'deferred',
            'is_deferred' => true,
            'deferment_reason' => $reason,
            'deferment_start_date' => $startDate ?? now(),
            'deferment_end_date' => $endDate,
            'status_change_date' => now(),
            'status_change_reason' => "Deferred: {$reason}",
        ]);
    }

    /**
     * Reactivate deferred enrollment.
     */
    public function reactivate(): void
    {
        $this->update([
            'status' => 'enrolled',
            'is_deferred' => false,
            'deferment_start_date' => null,
            'deferment_end_date' => null,
            'deferment_reason' => null,
            'status_change_date' => now(),
            'status_change_reason' => 'Reactivated from deferment',
        ]);
    }

    /**
     * Withdraw enrollment.
     */
    public function withdraw(string $reason): void
    {
        $this->update([
            'status' => 'withdrawn',
            'status_change_date' => now(),
            'status_change_reason' => "Withdrawn: {$reason}",
        ]);
    }

    /**
     * Complete enrollment.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'status_change_date' => now(),
            'status_change_reason' => 'Successfully completed course',
        ]);
    }

    /**
     * Generate a unique enrollment number
     * Format: ENR-YYYYMM-XXXX (e.g., ENR-202409-0001)
     * 
     * @return string
     */
    public static function generateEnrollmentNumber(): string
    {
        $prefix = 'ENR-' . now()->format('Ym') . '-';
        
        // Get the last enrollment number for this month
        $lastEnrollment = static::where('enrollment_number', 'like', $prefix . '%')
            ->orderBy('enrollment_number', 'desc')
            ->first();

        if ($lastEnrollment) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastEnrollment->enrollment_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // First enrollment of the month
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Set up payment plan.
     */
    public function setupPaymentPlan(string $planType, int $installments = null): void
    {
        if ($planType === 'full_payment') {
            $this->update([
                'payment_plan' => 'full_payment',
                'installment_count' => null,
                'installment_amount' => null,
                'next_payment_due' => $this->semester->fee_payment_deadline,
            ]);
        } elseif ($planType === 'installments' && $installments) {
            $installmentAmount = $this->total_fees_due / $installments;
            
            $this->update([
                'payment_plan' => 'installments',
                'installment_count' => $installments,
                'installment_amount' => $installmentAmount,
                'next_payment_due' => now()->addDays(30), // First installment due in 30 days
            ]);
        }
    }

    /**
     * Scope for active enrollments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    /**
     * Scope for overdue payments.
     */
    public function scopeOverduePayments($query)
    {
        return $query->where('next_payment_due', '<', now())
            ->where('outstanding_balance', '>', 0)
            ->where('fees_fully_paid', false);
    }

    /**
     * Scope for deferred enrollments.
     */
    public function scopeDeferred($query)
    {
        return $query->where('is_deferred', true);
    }

    /**
     * Scope for fully paid enrollments.
     */
    public function scopeFullyPaid($query)
    {
        return $query->where('fees_fully_paid', true);
    }
}
