<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PaymentNotification Model for Edulink International College Nairobi
 * 
 * Manages real-time payment notifications and alerts
 * Handles multi-channel delivery (email, SMS, push, in-app)
 */
class PaymentNotification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'payment_id',
        'notification_type',
        'title',
        'message',
        'priority',
        'send_email',
        'send_sms',
        'send_push',
        'show_in_app',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'email_sent',
        'email_sent_at',
        'email_error',
        'sms_sent',
        'sms_sent_at',
        'sms_error',
        'push_sent',
        'push_sent_at',
        'push_error',
        'retry_count',
        'max_retries',
        'next_retry_at',
        'notification_data',
        'template_name',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'email_sent_at' => 'datetime',
            'sms_sent_at' => 'datetime',
            'push_sent_at' => 'datetime',
            'next_retry_at' => 'datetime',
            'notification_data' => 'array',
            'send_email' => 'boolean',
            'send_sms' => 'boolean',
            'send_push' => 'boolean',
            'show_in_app' => 'boolean',
            'email_sent' => 'boolean',
            'sms_sent' => 'boolean',
            'push_sent' => 'boolean',
        ];
    }

    /**
     * Get the student for this notification.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the payment for this notification.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Check if notification is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if notification was sent.
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if notification was delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if notification failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if notification was read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Check if notification can be retried.
     */
    public function canRetry(): bool
    {
        return $this->retry_count < $this->max_retries && 
               $this->status === 'failed' &&
               ($this->next_retry_at === null || $this->next_retry_at <= now());
    }

    /**
     * Mark notification as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark notification as delivered.
     */
    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark notification as failed.
     */
    public function markAsFailed(string $error = null): void
    {
        $this->increment('retry_count');
        
        $this->update([
            'status' => 'failed',
            'next_retry_at' => $this->calculateNextRetryTime(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark email as sent.
     */
    public function markEmailSent(): void
    {
        $this->update([
            'email_sent' => true,
            'email_sent_at' => now(),
        ]);
    }

    /**
     * Mark email as failed.
     */
    public function markEmailFailed(string $error): void
    {
        $this->update([
            'email_sent' => false,
            'email_error' => $error,
        ]);
    }

    /**
     * Mark SMS as sent.
     */
    public function markSmsSent(): void
    {
        $this->update([
            'sms_sent' => true,
            'sms_sent_at' => now(),
        ]);
    }

    /**
     * Mark SMS as failed.
     */
    public function markSmsFailed(string $error): void
    {
        $this->update([
            'sms_sent' => false,
            'sms_error' => $error,
        ]);
    }

    /**
     * Mark push notification as sent.
     */
    public function markPushSent(): void
    {
        $this->update([
            'push_sent' => true,
            'push_sent_at' => now(),
        ]);
    }

    /**
     * Mark push notification as failed.
     */
    public function markPushFailed(string $error): void
    {
        $this->update([
            'push_sent' => false,
            'push_error' => $error,
        ]);
    }

    /**
     * Calculate next retry time based on retry count.
     */
    private function calculateNextRetryTime(): ?\Carbon\Carbon
    {
        if ($this->retry_count >= $this->max_retries) {
            return null;
        }

        // Exponential backoff: 5 minutes, 15 minutes, 45 minutes
        $minutes = 5 * pow(3, $this->retry_count);
        return now()->addMinutes($minutes);
    }

    /**
     * Get priority color for UI.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'delivered' => 'success',
            'sent' => 'info',
            'pending' => 'warning',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Create payment received notification.
     */
    public static function createPaymentReceived(Student $student, Payment $payment): self
    {
        return self::create([
            'student_id' => $student->id,
            'payment_id' => $payment->id,
            'notification_type' => 'payment_received',
            'title' => 'Payment Received',
            'message' => "Your payment of {$payment->currency} {$payment->amount} has been received and is being processed.",
            'priority' => 'medium',
            'send_email' => true,
            'send_sms' => true,
            'send_push' => true,
            'show_in_app' => true,
            'notification_data' => [
                'payment_reference' => $payment->payment_reference,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'payment_method' => $payment->payment_method,
            ],
            'template_name' => 'payment_received',
        ]);
    }

    /**
     * Create payment confirmed notification.
     */
    public static function createPaymentConfirmed(Student $student, Payment $payment): self
    {
        return self::create([
            'student_id' => $student->id,
            'payment_id' => $payment->id,
            'notification_type' => 'payment_confirmed',
            'title' => 'Payment Confirmed',
            'message' => "Your payment of {$payment->currency} {$payment->amount} has been confirmed and applied to your account.",
            'priority' => 'high',
            'send_email' => true,
            'send_sms' => true,
            'send_push' => true,
            'show_in_app' => true,
            'notification_data' => [
                'payment_reference' => $payment->payment_reference,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'outstanding_balance' => $payment->outstanding_balance_after,
            ],
            'template_name' => 'payment_confirmed',
        ]);
    }

    /**
     * Create payment reminder notification.
     */
    public static function createPaymentReminder(Student $student, StudentEnrollment $enrollment): self
    {
        return self::create([
            'student_id' => $student->id,
            'notification_type' => 'payment_reminder',
            'title' => 'Payment Reminder',
            'message' => "Your payment of {$enrollment->installment_amount} is due on {$enrollment->next_payment_due->format('M d, Y')}.",
            'priority' => 'medium',
            'send_email' => true,
            'send_sms' => false,
            'send_push' => true,
            'show_in_app' => true,
            'notification_data' => [
                'enrollment_id' => $enrollment->id,
                'amount_due' => $enrollment->installment_amount,
                'due_date' => $enrollment->next_payment_due,
                'course_name' => $enrollment->course->name,
            ],
            'template_name' => 'payment_reminder',
        ]);
    }

    /**
     * Scope for pending notifications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed notifications that can be retried.
     */
    public function scopeRetryable($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', 'max_retries')
            ->where(function($q) {
                $q->whereNull('next_retry_at')
                  ->orWhere('next_retry_at', '<=', now());
            });
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for notifications by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope for notifications by priority.
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }
}
