<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Course Model for Edulink International College Nairobi
 * 
 * Manages academic programs and courses offered by the college
 * Handles course details, fee structures, and enrollment capacity
 */
class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'course_code',
        'name',
        'description',
        'level',
        'type',
        'duration_months',
        'credit_hours',
        'department',
        'faculty',
        'total_fee',
        'registration_fee',
        'examination_fee',
        'library_fee',
        'lab_fee',
        'allows_installments',
        'max_installments',
        'minimum_deposit_percentage',
        'status',
        'available_from',
        'available_until',
        'max_students',
        'current_enrollment',
        'prerequisites',
        'admission_requirements',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'available_from' => 'date',
            'available_until' => 'date',
            'total_fee' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'examination_fee' => 'decimal:2',
            'library_fee' => 'decimal:2',
            'lab_fee' => 'decimal:2',
            'minimum_deposit_percentage' => 'decimal:2',
            'allows_installments' => 'boolean',
            'prerequisites' => 'array',
        ];
    }

    /**
     * Get the course's full title with level.
     */
    public function getFullTitleAttribute(): string
    {
        return $this->name . ' (' . ucfirst($this->level) . ')';
    }

    /**
     * Check if course is currently available for enrollment.
     */
    public function isAvailable(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $now = now()->toDateString();
        
        if ($this->available_from && $this->available_from > $now) {
            return false;
        }

        if ($this->available_until && $this->available_until < $now) {
            return false;
        }

        return true;
    }

    /**
     * Check if course has available slots.
     */
    public function hasAvailableSlots(): bool
    {
        if (!$this->max_students) {
            return true;
        }

        return $this->current_enrollment < $this->max_students;
    }

    /**
     * Get remaining enrollment slots.
     */
    public function getRemainingSlots(): int
    {
        if (!$this->max_students) {
            return PHP_INT_MAX;
        }

        return max(0, $this->max_students - $this->current_enrollment);
    }

    /**
     * Calculate minimum deposit amount.
     */
    public function getMinimumDepositAmount(): float
    {
        return ($this->total_fee * $this->minimum_deposit_percentage) / 100;
    }

    /**
     * Get all enrollments for this course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get active enrollments for this course.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'enrolled');
    }

    /**
     * Get all students through enrollments.
     */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, StudentEnrollment::class);
    }

    /**
     * Get fee structures for this course.
     */
    public function feeStructures(): HasMany
    {
        return $this->hasMany(FeeStructure::class);
    }

    /**
     * Get active fee structures.
     */
    public function activeFeeStructures(): HasMany
    {
        return $this->feeStructures()->where('status', 'active');
    }

    /**
     * Get fee structure for specific semester.
     */
    public function getFeeStructureForSemester(int $semesterId): ?FeeStructure
    {
        return $this->feeStructures()
            ->where('semester_id', $semesterId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Update enrollment count.
     */
    public function updateEnrollmentCount(): void
    {
        $count = $this->activeEnrollments()->count();
        $this->update(['current_enrollment' => $count]);
    }

    /**
     * Check if student meets prerequisites.
     */
    public function studentMeetsPrerequisites(Student $student): bool
    {
        if (!$this->prerequisites || empty($this->prerequisites)) {
            return true;
        }

        $completedCourses = $student->enrollments()
            ->where('status', 'completed')
            ->pluck('course_id')
            ->toArray();

        foreach ($this->prerequisites as $prerequisiteId) {
            if (!in_array($prerequisiteId, $completedCourses)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get prerequisite courses.
     */
    public function getPrerequisiteCourses()
    {
        if (!$this->prerequisites || empty($this->prerequisites)) {
            return collect();
        }

        return self::whereIn('id', $this->prerequisites)->get();
    }

    /**
     * Scope for active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for courses by level.
     */
    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for courses by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for courses by department.
     */
    public function scopeByDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }
}
