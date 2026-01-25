<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * AuditLogService
 * 
 * Handles audit logging for administrative actions and critical operations
 */
class AuditLogService
{
    /**
     * Log admin action
     */
    public function logAdminAction(string $action, array $data = [], ?string $model = null, ?int $modelId = null): void
    {
        $admin = Auth::guard('admin')->user();
        
        $logData = [
            'action' => $action,
            'admin_id' => $admin?->id,
            'admin_email' => $admin?->email,
            'admin_name' => $admin?->name,
            'model' => $model,
            'model_id' => $modelId,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel('audit')->info('Admin Action', $logData);
    }

    /**
     * Log payment verification
     */
    public function logPaymentVerification(int $paymentId, array $verificationData = []): void
    {
        $this->logAdminAction(
            'payment_verified',
            $verificationData,
            'Payment',
            $paymentId
        );
    }

    /**
     * Log payment refund
     */
    public function logPaymentRefund(int $paymentId, float $refundAmount, string $reason): void
    {
        $this->logAdminAction(
            'payment_refunded',
            [
                'refund_amount' => $refundAmount,
                'refund_reason' => $reason,
            ],
            'Payment',
            $paymentId
        );
    }

    /**
     * Log student status change
     */
    public function logStudentStatusChange(int $studentId, string $oldStatus, string $newStatus): void
    {
        $this->logAdminAction(
            'student_status_changed',
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ],
            'Student',
            $studentId
        );
    }

    /**
     * Log admin creation
     */
    public function logAdminCreation(int $adminId, array $permissions = []): void
    {
        $this->logAdminAction(
            'admin_created',
            ['permissions' => $permissions],
            'Admin',
            $adminId
        );
    }

    /**
     * Log permission change
     */
    public function logPermissionChange(int $adminId, array $oldPermissions, array $newPermissions): void
    {
        $this->logAdminAction(
            'permissions_changed',
            [
                'old_permissions' => $oldPermissions,
                'new_permissions' => $newPermissions,
            ],
            'Admin',
            $adminId
        );
    }

    /**
     * Log course creation/update
     */
    public function logCourseChange(string $action, int $courseId, array $changes = []): void
    {
        $this->logAdminAction(
            "course_{$action}",
            $changes,
            'Course',
            $courseId
        );
    }

    /**
     * Log security event
     */
    public function logSecurityEvent(string $event, array $data = []): void
    {
        $logData = [
            'event' => $event,
            'data' => $data,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel('security')->warning('Security Event', $logData);
    }

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $email, string $guard = 'admin'): void
    {
        $this->logSecurityEvent('failed_login_attempt', [
            'email' => $email,
            'guard' => $guard,
        ]);
    }

    /**
     * Log payment processing
     */
    public function logPaymentProcessing(int $paymentId, string $status, array $data = []): void
    {
        $logData = [
            'payment_id' => $paymentId,
            'status' => $status,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
        ];

        Log::channel('payments')->info('Payment Processing', $logData);
    }
}
