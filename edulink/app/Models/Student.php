<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Student Model for Edulink International College Nairobi
 * 
 * Handles student authentication and profile management
 * Provides relationships to enrollments, payments, and notifications
 */
class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'date_of_birth',
        'gender',
        'national_id',
        'passport_number',
        'address',
        'city',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'status',
        'enrollment_date',
        'expected_graduation_date',
        'total_fees_owed',
        'total_fees_paid',
        'has_outstanding_fees',
        'last_payment_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => config('auth.password_cast', 'hashed'),
            'date_of_birth' => 'date',
            'enrollment_date' => 'date',
            'expected_graduation_date' => 'date',
            'last_payment_date' => 'date',
            'total_fees_owed' => 'decimal:2',
            'total_fees_paid' => 'decimal:2',
            'has_outstanding_fees' => 'boolean',
        ];
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    /**
     * Get the student's outstanding balance.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        return $this->total_fees_owed - $this->total_fees_paid;
    }

    /**
     * Check if student has overdue payments.
     */
    public function hasOverduePayments(): bool
    {
        return $this->enrollments()
            ->where('next_payment_due', '<', now())
            ->where('outstanding_balance', '>', 0)
            ->exists();
    }

    /**
     * Get all enrollments for this student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    /**
     * Get all payments made by this student.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all notifications for this student.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(PaymentNotification::class);
    }

    /**
     * Get all courses through enrollments.
     */
    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Course::class, StudentEnrollment::class);
    }

    /**
     * Get active enrollments.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'enrolled');
    }

    /**
     * Get completed payments.
     */
    public function completedPayments(): HasMany
    {
        return $this->payments()->where('status', 'completed');
    }

    /**
     * Generate unique student ID.
     */
    public static function generateStudentId(): string
    {
        $year = date('Y');
        $lastStudent = self::where('student_id', 'like', "EDU{$year}%")
            ->orderBy('student_id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "EDU{$year}{$newNumber}";
    }

    /**
     * Update financial summary for the student.
     */
    public function updateFinancialSummary(): void
    {
        $totalOwed = $this->enrollments()->sum('total_fees_due');
        $totalPaid = $this->completedPayments()->sum('amount');
        
        $this->update([
            'total_fees_owed' => $totalOwed,
            'total_fees_paid' => $totalPaid,
            'has_outstanding_fees' => $totalOwed > $totalPaid,
            'last_payment_date' => $this->completedPayments()->latest('payment_date')->first()?->payment_date,
        ]);
    }

    /**
     * Get financial summary for the student.
     */
    public function getFinancialSummary(): array
    {
        $totalOwed = $this->total_fees_owed ?? 0;
        $totalPaid = $this->total_fees_paid ?? 0;
        $outstandingBalance = $totalOwed - $totalPaid;
        
        $recentPayments = $this->completedPayments()
            ->latest('payment_date')
            ->limit(5)
            ->get();
            
        $pendingPayments = $this->payments()
            ->where('status', 'pending')
            ->sum('amount');
            
        return [
            'total_fees_owed' => $totalOwed,
            'total_fees_paid' => $totalPaid,
            'outstanding_balance' => $outstandingBalance,
            'pending_payments' => $pendingPayments,
            'payment_status' => $outstandingBalance > 0 ? 'outstanding' : 'up_to_date',
            'last_payment_date' => $this->last_payment_date,
            'recent_payments' => $recentPayments,
            'has_overdue_payments' => $this->hasOverduePayments(),
        ];
    }
}
