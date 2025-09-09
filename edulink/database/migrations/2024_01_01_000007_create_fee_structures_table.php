<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fee Structures table for Edulink International College Nairobi
     * Manages detailed fee breakdown for courses and semesters
     */
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            
            // Fee Structure Details
            $table->string('fee_structure_code')->unique(); // e.g., FS2024-CS101-SEM1
            $table->string('name'); // e.g., "Computer Science Semester 1 2024 Fees"
            $table->text('description')->nullable();
            
            // Tuition Fees
            $table->decimal('tuition_fee', 12, 2);
            $table->decimal('lab_fee', 10, 2)->default(0.00);
            $table->decimal('library_fee', 10, 2)->default(0.00);
            $table->decimal('examination_fee', 10, 2)->default(0.00);
            $table->decimal('registration_fee', 10, 2)->default(0.00);
            
            // Additional Fees
            $table->decimal('activity_fee', 10, 2)->default(0.00);
            $table->decimal('technology_fee', 10, 2)->default(0.00);
            $table->decimal('student_services_fee', 10, 2)->default(0.00);
            $table->decimal('graduation_fee', 10, 2)->default(0.00);
            $table->decimal('id_card_fee', 10, 2)->default(0.00);
            
            // Insurance and Medical
            $table->decimal('medical_insurance_fee', 10, 2)->default(0.00);
            $table->decimal('accident_insurance_fee', 10, 2)->default(0.00);
            
            // Accommodation (if applicable)
            $table->decimal('accommodation_fee', 10, 2)->default(0.00);
            $table->decimal('meal_plan_fee', 10, 2)->default(0.00);
            
            // Calculated Totals
            $table->decimal('subtotal', 12, 2); // Sum of all fees
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2); // Subtotal - discount
            
            // Payment Terms
            $table->boolean('allows_installments')->default(true);
            $table->integer('max_installments')->default(4);
            $table->decimal('minimum_deposit_percentage', 5, 2)->default(25.00);
            $table->decimal('minimum_deposit_amount', 10, 2)->nullable();
            
            // Late Payment Settings
            $table->decimal('late_payment_penalty_rate', 5, 2)->default(5.00); // Percentage
            $table->decimal('late_payment_fixed_penalty', 10, 2)->default(0.00);
            $table->integer('grace_period_days')->default(7);
            
            // Validity Period
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            
            // System fields
            $table->timestamps();
            
            // Unique constraint for course-semester combination
            $table->unique(['course_id', 'semester_id'], 'unique_course_semester_fee');
            
            // Indexes for performance
            $table->index(['status', 'effective_from', 'effective_until']);
            $table->index(['total_amount', 'allows_installments']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
