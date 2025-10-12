<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentPlan extends Model
{
    protected $fillable = [
        'student_id',
        'student_enrollment_id',
        'plan_name',
        'total_amount',
        'total_installments',
        'status'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'student_enrollment_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PaymentInstallment::class);
    }
}