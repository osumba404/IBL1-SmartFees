<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Student Enrollments table for Edulink International College Nairobi
     * Links students to courses and semesters with enrollment details
     */
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            
            // Enrollment Details
            $table->string('enrollment_number')->unique(); // e.g., ENR2024001
            $table->date('enrollment_date');
            $table->enum('enrollment_type', ['new', 'continuing', 'transfer', 'readmission']);
            
            // Academic Status
            $table->enum('status', ['enrolled', 'withdrawn', 'completed', 'deferred', 'suspended'])->default('enrolled');
            $table->date('status_change_date')->nullable();
            $table->text('status_change_reason')->nullable();
            
            // Fee Information for this enrollment
            $table->decimal('total_fees_due', 12, 2);
            $table->decimal('fees_paid', 12, 2)->default(0.00);
            $table->decimal('outstanding_balance', 12, 2)->default(0.00);
            $table->boolean('fees_fully_paid')->default(false);
            
            // Payment Plan
            $table->enum('payment_plan', ['full_payment', 'installments'])->default('installments');
            $table->integer('installment_count')->nullable();
            $table->decimal('installment_amount', 10, 2)->nullable();
            $table->date('next_payment_due')->nullable();
            
            // Academic Performance
            $table->decimal('gpa', 3, 2)->nullable();
            $table->integer('credits_enrolled')->nullable();
            $table->integer('credits_completed')->nullable();
            
            // Deferment Information
            $table->boolean('is_deferred')->default(false);
            $table->date('deferment_start_date')->nullable();
            $table->date('deferment_end_date')->nullable();
            $table->text('deferment_reason')->nullable();
            
            // System fields
            $table->timestamps();
            
            // Unique constraint to prevent duplicate enrollments
            $table->unique(['student_id', 'course_id', 'semester_id'], 'unique_student_course_semester');
            
            // Indexes for performance
            $table->index(['status', 'enrollment_date']);
            $table->index(['fees_fully_paid', 'outstanding_balance']);
            $table->index(['is_deferred', 'deferment_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
