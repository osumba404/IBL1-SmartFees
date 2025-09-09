<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Semesters table for Edulink International College Nairobi
     * Manages academic periods and their fee schedules
     */
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            
            // Semester Identification
            $table->string('semester_code')->unique(); // e.g., 2024-1, 2024-2
            $table->string('name'); // e.g., "Semester 1 2024", "Summer 2024"
            $table->text('description')->nullable();
            
            // Academic Year and Period
            $table->integer('academic_year'); // e.g., 2024
            $table->enum('period', ['semester_1', 'semester_2', 'semester_3', 'summer', 'winter']);
            
            // Semester Dates
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_start_date');
            $table->date('registration_end_date');
            
            // Fee Payment Deadlines
            $table->date('fee_payment_deadline');
            $table->date('late_payment_deadline')->nullable();
            $table->decimal('late_payment_penalty_percentage', 5, 2)->default(5.00);
            $table->decimal('late_payment_penalty_fixed', 10, 2)->default(0.00);
            
            // Grace Period Settings
            $table->integer('grace_period_days')->default(7); // Days after deadline
            $table->boolean('allows_grace_period')->default(true);
            $table->text('grace_period_conditions')->nullable();
            
            // Semester Status
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->boolean('is_current_semester')->default(false);
            
            // Fee Structure for this semester
            $table->decimal('base_tuition_fee', 12, 2)->default(0.00);
            $table->decimal('activity_fee', 10, 2)->default(0.00);
            $table->decimal('technology_fee', 10, 2)->default(0.00);
            $table->decimal('student_services_fee', 10, 2)->default(0.00);
            
            // Enrollment Settings
            $table->integer('max_credits_per_student')->nullable();
            $table->integer('min_credits_per_student')->nullable();
            $table->boolean('allows_late_enrollment')->default(false);
            $table->date('late_enrollment_deadline')->nullable();
            
            // System fields
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['academic_year', 'period']);
            $table->index(['status', 'is_current_semester']);
            $table->index(['start_date', 'end_date']);
            $table->index(['fee_payment_deadline', 'late_payment_deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
