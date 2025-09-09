<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Admin Model for Edulink International College Nairobi
 * 
 * Handles administrative user authentication and role-based access control
 * Manages permissions for different administrative functions
 */
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'admin_id',
        'email',
        'password',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'employee_id',
        'role',
        'permissions',
        'status',
        'department',
        'position',
        'hire_date',
        'can_manage_students',
        'can_manage_courses',
        'can_manage_fees',
        'can_view_payments',
        'can_process_payments',
        'can_generate_reports',
        'can_manage_admins',
        'force_password_change',
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
            'password' => 'hashed',
            'hire_date' => 'date',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'permissions' => 'array',
            'can_manage_students' => 'boolean',
            'can_manage_courses' => 'boolean',
            'can_manage_fees' => 'boolean',
            'can_view_payments' => 'boolean',
            'can_process_payments' => 'boolean',
            'can_generate_reports' => 'boolean',
            'can_manage_admins' => 'boolean',
            'force_password_change' => 'boolean',
        ];
    }

    /**
     * Get the admin's full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    /**
     * Check if admin is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if admin has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->$permission ?? false;
    }

    /**
     * Check if admin can manage students.
     */
    public function canManageStudents(): bool
    {
        return $this->hasPermission('can_manage_students');
    }

    /**
     * Check if admin can manage courses.
     */
    public function canManageCourses(): bool
    {
        return $this->hasPermission('can_manage_courses');
    }

    /**
     * Check if admin can manage fees.
     */
    public function canManageFees(): bool
    {
        return $this->hasPermission('can_manage_fees');
    }

    /**
     * Check if admin can view payments.
     */
    public function canViewPayments(): bool
    {
        return $this->hasPermission('can_view_payments');
    }

    /**
     * Check if admin can process payments.
     */
    public function canProcessPayments(): bool
    {
        return $this->hasPermission('can_process_payments');
    }

    /**
     * Check if admin can generate reports.
     */
    public function canGenerateReports(): bool
    {
        return $this->hasPermission('can_generate_reports');
    }

    /**
     * Check if admin can manage other admins.
     */
    public function canManageAdmins(): bool
    {
        return $this->hasPermission('can_manage_admins');
    }

    /**
     * Check if admin account is locked.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Lock admin account.
     */
    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Unlock admin account.
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Increment failed login attempts.
     */
    public function incrementFailedLogins(): void
    {
        $this->increment('failed_login_attempts');
        
        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount();
        }
    }

    /**
     * Reset failed login attempts.
     */
    public function resetFailedLogins(): void
    {
        $this->update(['failed_login_attempts' => 0]);
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
        
        $this->resetFailedLogins();
    }

    /**
     * Get payments verified by this admin.
     */
    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    /**
     * Generate unique admin ID.
     */
    public static function generateAdminId(): string
    {
        $lastAdmin = self::orderBy('admin_id', 'desc')->first();
        
        if ($lastAdmin) {
            $lastNumber = (int) substr($lastAdmin->admin_id, 3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "ADM{$newNumber}";
    }

    /**
     * Set default permissions based on role.
     */
    public function setDefaultPermissions(): void
    {
        $permissions = match($this->role) {
            'super_admin' => [
                'can_manage_students' => true,
                'can_manage_courses' => true,
                'can_manage_fees' => true,
                'can_view_payments' => true,
                'can_process_payments' => true,
                'can_generate_reports' => true,
                'can_manage_admins' => true,
            ],
            'finance_officer' => [
                'can_manage_students' => false,
                'can_manage_courses' => false,
                'can_manage_fees' => true,
                'can_view_payments' => true,
                'can_process_payments' => true,
                'can_generate_reports' => true,
                'can_manage_admins' => false,
            ],
            'registrar' => [
                'can_manage_students' => true,
                'can_manage_courses' => true,
                'can_manage_fees' => false,
                'can_view_payments' => true,
                'can_process_payments' => false,
                'can_generate_reports' => true,
                'can_manage_admins' => false,
            ],
            'academic_officer' => [
                'can_manage_students' => true,
                'can_manage_courses' => true,
                'can_manage_fees' => false,
                'can_view_payments' => false,
                'can_process_payments' => false,
                'can_generate_reports' => false,
                'can_manage_admins' => false,
            ],
            default => [
                'can_manage_students' => false,
                'can_manage_courses' => false,
                'can_manage_fees' => false,
                'can_view_payments' => false,
                'can_process_payments' => false,
                'can_generate_reports' => false,
                'can_manage_admins' => false,
            ],
        };

        $this->update($permissions);
    }
}
