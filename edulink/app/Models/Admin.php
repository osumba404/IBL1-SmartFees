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
        'name',
        'email',
        'password',
        'phone',
        'employee_id',
        'role',
        'is_active',
        'is_super_admin',
        'department',
        'position',
        'hire_date',
        'can_manage_students',
        'can_manage_courses',
        'can_manage_payments',
        'can_view_reports',
        'can_approve_students',
        'can_manage_fees',
        'force_password_change',
        'email_verified_at',
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
            'hire_date' => 'date',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'is_active' => 'boolean',
            'is_super_admin' => 'boolean',
            'can_manage_students' => 'boolean',
            'can_manage_courses' => 'boolean',
            'can_manage_payments' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_approve_students' => 'boolean',
            'can_manage_fees' => 'boolean',
            'force_password_change' => 'boolean',
        ];
    }



    /**
     * Check if admin is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->is_super_admin === true;
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
     * Check if admin can manage payments.
     */
    public function canManagePayments(): bool
    {
        return $this->hasPermission('can_manage_payments');
    }

    /**
     * Check if admin can view reports.
     */
    public function canViewReports(): bool
    {
        return $this->hasPermission('can_view_reports');
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
        $prefix = config('auth.admin_id_prefix', 'ADM');
        $lastAdmin = self::orderBy('admin_id', 'desc')->first();
        
        if ($lastAdmin) {
            $lastNumber = (int) substr($lastAdmin->admin_id, strlen($prefix));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "{$prefix}{$newNumber}";
    }

    /**
     * Set default permissions based on role.
     */
    public function setDefaultPermissions(): void
    {
        $roles = config('auth.admin_roles', [
            'super_admin' => 'super_admin',
            'finance_officer' => 'finance_officer',
            'registrar' => 'registrar',
            'academic_officer' => 'academic_officer'
        ]);
        
        $permissions = match($this->role) {
            $roles['super_admin'] => [
                'can_manage_students' => true,
                'can_manage_courses' => true,
                'can_manage_fees' => true,
                'can_view_payments' => true,
                'can_process_payments' => true,
                'can_generate_reports' => true,
                'can_manage_admins' => true,
            ],
            $roles['finance_officer'] => [
                'can_manage_students' => false,
                'can_manage_courses' => false,
                'can_manage_fees' => true,
                'can_view_payments' => true,
                'can_process_payments' => true,
                'can_generate_reports' => true,
                'can_manage_admins' => false,
            ],
            $roles['registrar'] => [
                'can_manage_students' => true,
                'can_manage_courses' => true,
                'can_manage_fees' => false,
                'can_view_payments' => true,
                'can_process_payments' => false,
                'can_generate_reports' => true,
                'can_manage_admins' => false,
            ],
            $roles['academic_officer'] => [
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
