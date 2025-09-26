<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Semester Model for Edulink International College Nairobi
 * 
 * Manages academic periods and their fee schedules
 * Handles semester dates, deadlines, and grace periods
 */
class Semester extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'semester_code',
        'name',
        'description',
        'academic_year',
        'period',
        'start_date',
        'end_date',
        'registration_start_date',
        'registration_end_date',
        'fee_payment_deadline',
        'late_payment_deadline',
        'late_payment_penalty_percentage',
        'late_payment_penalty_fixed',
        'grace_period_days',
        'allows_grace_period',
        'grace_period_conditions',
        'status',
        'is_current_semester',
        'base_tuition_fee',
        'activity_fee',
        'technology_fee',
        'student_services_fee',
        'max_credits_per_student',
        'min_credits_per_student',
        'allows_late_enrollment',
        'late_enrollment_deadline',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_start_date' => 'date',
            'registration_end_date' => 'date',
            'fee_payment_deadline' => 'date',
            'late_payment_deadline' => 'date',
            'late_enrollment_deadline' => 'date',
            'late_payment_penalty_percentage' => 'decimal:2',
            'late_payment_penalty_fixed' => 'decimal:2',
            'base_tuition_fee' => 'decimal:2',
            'activity_fee' => 'decimal:2',
            'technology_fee' => 'decimal:2',
            'student_services_fee' => 'decimal:2',
            'allows_grace_period' => 'boolean',
            'is_current_semester' => 'boolean',
            'allows_late_enrollment' => 'boolean',
        ];
    }

    /**
     * Get the semester's full name with year.
     */
    public function getFullNameAttribute(): string
    {
        return $this->name . ' - ' . $this->academic_year;
    }

    /**
     * Check if semester is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if registration is currently open.
     */
    public function isRegistrationOpen(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $now = now()->toDateString();
        return $now >= $this->registration_start_date && $now <= $this->registration_end_date;
    }

    /**
     * Check if late enrollment is allowed and still open.
     */
    public function isLateEnrollmentOpen(): bool
    {
        if (!$this->allows_late_enrollment || !$this->late_enrollment_deadline) {
            return false;
        }

        return now()->toDateString() <= $this->late_enrollment_deadline;
    }

    /**
     * Check if fee payment deadline has passed.
     */
    public function isFeePaymentOverdue(): bool
    {
        return now()->toDateString() > $this->fee_payment_deadline;
    }

    /**
     * Check if we're in grace period.
     */
    public function isInGracePeriod(): bool
    {
        if (!$this->allows_grace_period) {
            return false;
        }

        $gracePeriodEnd = $this->fee_payment_deadline->addDays($this->grace_period_days);
        return now()->toDateString() <= $gracePeriodEnd;
    }

    /**
     * Calculate late payment penalty for given amount.
     */
    public function calculateLatePaymentPenalty(float $amount): float
    {
        $percentagePenalty = ($amount * $this->late_payment_penalty_percentage) / 100;
        return $percentagePenalty + $this->late_payment_penalty_fixed;
    }

    /**
     * Get days remaining until fee payment deadline.
     */
    public function getDaysUntilFeeDeadline(): int
    {
        return now()->diffInDays($this->fee_payment_deadline, false);
    }

    /**
     * Get days past fee payment deadline.
     */
    public function getDaysPastFeeDeadline(): int
    {
        if (!$this->isFeePaymentOverdue()) {
            return 0;
        }

        return $this->fee_payment_deadline->diffInDays(now());
    }

    /**
     * Get all enrollments for this semester.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get active enrollments for this semester.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'enrolled');
    }

    /**
     * Get fee structures for this semester.
     */
    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    /**
     * Get active fee structures for this semester.
     */
    public function activeFeeStructures(): HasMany
    {
        return $this->feeStructures()->where('status', 'active');
    }

    /**
     * Set as current semester.
     */
    public function setAsCurrent(): void
    {
        // First, unset all other semesters as current
        self::where('is_current_semester', true)->update(['is_current_semester' => false]);
        
        // Set this semester as current
        $this->update(['is_current_semester' => true]);
    }

    /**
     * Get the active semester for enrollment (current, upcoming, or last active).
     */
    public static function current(): ?self
    {
        // 1. Try to find the explicitly set current and active semester
        $current = self::where('is_current_semester', true)->where('status', 'active')->first();
        if ($current) {
            return $current;
        }

        // 2. If no current, find the next upcoming active semester
        $upcoming = self::where('status', 'active')
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->first();
        if ($upcoming) {
            return $upcoming;
        }

        // 3. If none upcoming, fall back to the most recently started active semester
        return self::where('status', 'active')
            ->where('start_date', '<=', now())
            ->orderBy('start_date', 'desc')
            ->first();
    }

    /**
     * Get upcoming semesters.
     */
    public static function upcoming()
    {
        return self::where('status', 'upcoming')
            ->where('start_date', '>', now())
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Scope for active semesters.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for semesters by academic year.
     */
    public function scopeByAcademicYear($query, int $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope for semesters by period.
     */
    public function scopeByPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Get enrollment statistics for this semester.
     */
    public function getEnrollmentStats(): array
    {
        $enrollments = $this->enrollments();
        
        return [
            'total_enrollments' => $enrollments->count(),
            'active_enrollments' => $enrollments->where('status', 'enrolled')->count(),
            'completed_enrollments' => $enrollments->where('status', 'completed')->count(),
            'withdrawn_enrollments' => $enrollments->where('status', 'withdrawn')->count(),
            'deferred_enrollments' => $enrollments->where('status', 'deferred')->count(),
        ];
    }

    /**
     * Get payment statistics for this semester.
     */
    public function getPaymentStats(): array
    {
        $enrollments = $this->enrollments();
        
        return [
            'total_fees_due' => $enrollments->sum('total_fees_due'),
            'total_fees_paid' => $enrollments->sum('fees_paid'),
            'outstanding_balance' => $enrollments->sum('outstanding_balance'),
            'fully_paid_count' => $enrollments->where('fees_fully_paid', true)->count(),
            'outstanding_count' => $enrollments->where('outstanding_balance', '>', 0)->count(),
        ];
    }
}