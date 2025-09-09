<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * FeeStructure Model for Edulink International College Nairobi
 * 
 * Manages detailed fee breakdown for courses and semesters
 * Handles fee calculations, discounts, and payment terms
 */
class FeeStructure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'course_id',
        'semester_id',
        'fee_structure_code',
        'name',
        'description',
        'tuition_fee',
        'lab_fee',
        'library_fee',
        'examination_fee',
        'registration_fee',
        'activity_fee',
        'technology_fee',
        'student_services_fee',
        'graduation_fee',
        'id_card_fee',
        'medical_insurance_fee',
        'accident_insurance_fee',
        'accommodation_fee',
        'meal_plan_fee',
        'subtotal',
        'discount_amount',
        'total_amount',
        'allows_installments',
        'max_installments',
        'minimum_deposit_percentage',
        'minimum_deposit_amount',
        'late_payment_penalty_rate',
        'late_payment_fixed_penalty',
        'grace_period_days',
        'effective_from',
        'effective_until',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'tuition_fee' => 'decimal:2',
            'lab_fee' => 'decimal:2',
            'library_fee' => 'decimal:2',
            'examination_fee' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'activity_fee' => 'decimal:2',
            'technology_fee' => 'decimal:2',
            'student_services_fee' => 'decimal:2',
            'graduation_fee' => 'decimal:2',
            'id_card_fee' => 'decimal:2',
            'medical_insurance_fee' => 'decimal:2',
            'accident_insurance_fee' => 'decimal:2',
            'accommodation_fee' => 'decimal:2',
            'meal_plan_fee' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'minimum_deposit_percentage' => 'decimal:2',
            'minimum_deposit_amount' => 'decimal:2',
            'late_payment_penalty_rate' => 'decimal:2',
            'late_payment_fixed_penalty' => 'decimal:2',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'allows_installments' => 'boolean',
        ];
    }

    /**
     * Get the course for this fee structure.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the semester for this fee structure.
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Check if fee structure is currently active.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now()->toDateString();
        
        if ($this->effective_from && $this->effective_from > $now) {
            return false;
        }

        if ($this->effective_until && $this->effective_until < $now) {
            return false;
        }

        return true;
    }

    /**
     * Calculate subtotal of all fees.
     */
    public function calculateSubtotal(): float
    {
        return $this->tuition_fee + 
               $this->lab_fee + 
               $this->library_fee + 
               $this->examination_fee + 
               $this->registration_fee + 
               $this->activity_fee + 
               $this->technology_fee + 
               $this->student_services_fee + 
               $this->graduation_fee + 
               $this->id_card_fee + 
               $this->medical_insurance_fee + 
               $this->accident_insurance_fee + 
               $this->accommodation_fee + 
               $this->meal_plan_fee;
    }

    /**
     * Calculate total amount after discount.
     */
    public function calculateTotal(): float
    {
        return max(0, $this->calculateSubtotal() - $this->discount_amount);
    }

    /**
     * Get minimum deposit amount.
     */
    public function getMinimumDepositAmount(): float
    {
        if ($this->minimum_deposit_amount) {
            return $this->minimum_deposit_amount;
        }

        return ($this->total_amount * $this->minimum_deposit_percentage) / 100;
    }

    /**
     * Calculate installment amount.
     */
    public function calculateInstallmentAmount(int $installments): float
    {
        if (!$this->allows_installments || $installments <= 0) {
            return $this->total_amount;
        }

        $remainingAmount = $this->total_amount - $this->getMinimumDepositAmount();
        return $remainingAmount / ($installments - 1); // -1 because first payment is deposit
    }

    /**
     * Get fee breakdown as array.
     */
    public function getFeeBreakdown(): array
    {
        return [
            'tuition_fee' => $this->tuition_fee,
            'lab_fee' => $this->lab_fee,
            'library_fee' => $this->library_fee,
            'examination_fee' => $this->examination_fee,
            'registration_fee' => $this->registration_fee,
            'activity_fee' => $this->activity_fee,
            'technology_fee' => $this->technology_fee,
            'student_services_fee' => $this->student_services_fee,
            'graduation_fee' => $this->graduation_fee,
            'id_card_fee' => $this->id_card_fee,
            'medical_insurance_fee' => $this->medical_insurance_fee,
            'accident_insurance_fee' => $this->accident_insurance_fee,
            'accommodation_fee' => $this->accommodation_fee,
            'meal_plan_fee' => $this->meal_plan_fee,
        ];
    }

    /**
     * Get non-zero fees only.
     */
    public function getNonZeroFees(): array
    {
        return array_filter($this->getFeeBreakdown(), function($amount) {
            return $amount > 0;
        });
    }

    /**
     * Calculate late payment penalty.
     */
    public function calculateLatePaymentPenalty(float $amount): float
    {
        $percentagePenalty = ($amount * $this->late_payment_penalty_rate) / 100;
        return $percentagePenalty + $this->late_payment_fixed_penalty;
    }

    /**
     * Update calculated fields.
     */
    public function updateCalculatedFields(): void
    {
        $this->update([
            'subtotal' => $this->calculateSubtotal(),
            'total_amount' => $this->calculateTotal(),
        ]);
    }

    /**
     * Generate unique fee structure code.
     */
    public static function generateFeeStructureCode(Course $course, Semester $semester): string
    {
        return "FS{$semester->academic_year}-{$course->course_code}-{$semester->period}";
    }

    /**
     * Scope for active fee structures.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', now());
            });
    }

    /**
     * Scope for fee structures by course.
     */
    public function scopeByCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope for fee structures by semester.
     */
    public function scopeBySemester($query, int $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($feeStructure) {
            // Auto-calculate subtotal and total when saving
            $feeStructure->subtotal = $feeStructure->calculateSubtotal();
            $feeStructure->total_amount = $feeStructure->calculateTotal();
        });
    }
}
