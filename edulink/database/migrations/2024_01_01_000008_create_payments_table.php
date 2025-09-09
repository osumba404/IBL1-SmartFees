<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Payments table for Edulink International College Nairobi
     * Tracks all student payments including M-Pesa and Stripe transactions
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->onDelete('cascade');
            
            // Payment Identification
            $table->string('payment_reference')->unique(); // e.g., PAY2024001
            $table->string('transaction_id')->unique(); // External transaction ID
            $table->string('receipt_number')->nullable(); // M-Pesa receipt or Stripe receipt
            
            // Payment Details
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('KES'); // KES, USD, etc.
            $table->enum('payment_method', ['mpesa', 'stripe', 'bank_transfer', 'cash', 'cheque', 'other']);
            $table->enum('payment_type', ['tuition', 'registration', 'examination', 'library', 'lab', 'activity', 'accommodation', 'other']);
            
            // Payment Gateway Information
            $table->string('gateway_transaction_id')->nullable(); // M-Pesa or Stripe transaction ID
            $table->string('gateway_reference')->nullable(); // Gateway-specific reference
            $table->json('gateway_response')->nullable(); // Store full gateway response
            
            // M-Pesa Specific Fields
            $table->string('mpesa_receipt_number')->nullable();
            $table->string('mpesa_transaction_date')->nullable();
            $table->string('mpesa_phone_number')->nullable();
            $table->decimal('mpesa_transaction_cost', 8, 2)->nullable();
            
            // Stripe Specific Fields
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->json('stripe_metadata')->nullable();
            
            // Bank Transfer Fields
            $table->string('bank_name')->nullable();
            $table->string('bank_reference')->nullable();
            $table->date('bank_transaction_date')->nullable();
            
            // Payment Status and Verification
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Payment Timing
            $table->timestamp('payment_date');
            $table->timestamp('processed_at')->nullable();
            $table->date('value_date')->nullable(); // When funds are available
            
            // Fee Allocation
            $table->json('fee_breakdown')->nullable(); // How payment is allocated to different fees
            $table->decimal('outstanding_balance_before', 12, 2)->nullable();
            $table->decimal('outstanding_balance_after', 12, 2)->nullable();
            
            // Refund Information
            $table->boolean('is_refunded')->default(false);
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();
            $table->string('refund_reference')->nullable();
            
            // Late Payment
            $table->boolean('is_late_payment')->default(false);
            $table->decimal('late_payment_penalty', 10, 2)->default(0.00);
            $table->integer('days_late')->nullable();
            
            // Notes and Comments
            $table->text('payment_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // System fields
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['student_id', 'payment_date']);
            $table->index(['status', 'is_verified']);
            $table->index(['payment_method', 'payment_type']);
            $table->index(['transaction_id', 'receipt_number']);
            $table->index(['mpesa_receipt_number', 'mpesa_phone_number']);
            $table->index(['stripe_payment_intent_id', 'stripe_charge_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
