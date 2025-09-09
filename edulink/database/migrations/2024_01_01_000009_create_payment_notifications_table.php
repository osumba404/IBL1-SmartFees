<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Payment Notifications table for Edulink International College Nairobi
     * Manages real-time payment notifications and alerts
     */
    public function up(): void
    {
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('cascade');
            
            // Notification Details
            $table->string('notification_type'); // payment_received, payment_failed, payment_reminder, etc.
            $table->string('title');
            $table->text('message');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Delivery Channels
            $table->boolean('send_email')->default(true);
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_push')->default(true);
            $table->boolean('show_in_app')->default(true);
            
            // Delivery Status
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            
            // Email Delivery
            $table->boolean('email_sent')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->text('email_error')->nullable();
            
            // SMS Delivery
            $table->boolean('sms_sent')->default(false);
            $table->timestamp('sms_sent_at')->nullable();
            $table->text('sms_error')->nullable();
            
            // Push Notification
            $table->boolean('push_sent')->default(false);
            $table->timestamp('push_sent_at')->nullable();
            $table->text('push_error')->nullable();
            
            // Retry Logic
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->timestamp('next_retry_at')->nullable();
            
            // Additional Data
            $table->json('notification_data')->nullable(); // Store additional context data
            $table->string('template_name')->nullable(); // Email/SMS template used
            
            // System fields
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['student_id', 'notification_type']);
            $table->index(['status', 'sent_at']);
            $table->index(['priority', 'created_at']);
            $table->index(['next_retry_at', 'retry_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_notifications');
    }
};
