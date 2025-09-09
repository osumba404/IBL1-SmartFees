<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Courses table for Edulink International College Nairobi
     * Stores information about academic programs and courses
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            
            // Course Identification
            $table->string('course_code')->unique(); // e.g., CS101, BBA201
            $table->string('name'); // Course name
            $table->text('description')->nullable();
            
            // Course Details
            $table->enum('level', ['certificate', 'diploma', 'degree', 'masters', 'phd']);
            $table->enum('type', ['full_time', 'part_time', 'online', 'hybrid']);
            $table->integer('duration_months'); // Course duration in months
            $table->integer('credit_hours')->nullable();
            
            // Department/Faculty
            $table->string('department')->nullable();
            $table->string('faculty')->nullable();
            
            // Fee Structure
            $table->decimal('total_fee', 12, 2); // Total course fee
            $table->decimal('registration_fee', 10, 2)->default(0.00);
            $table->decimal('examination_fee', 10, 2)->default(0.00);
            $table->decimal('library_fee', 10, 2)->default(0.00);
            $table->decimal('lab_fee', 10, 2)->default(0.00);
            
            // Payment Structure
            $table->boolean('allows_installments')->default(true);
            $table->integer('max_installments')->default(4); // Maximum number of installments
            $table->decimal('minimum_deposit_percentage', 5, 2)->default(25.00); // Minimum deposit %
            
            // Course Status and Availability
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();
            $table->integer('max_students')->nullable(); // Maximum enrollment capacity
            $table->integer('current_enrollment')->default(0);
            
            // Prerequisites
            $table->json('prerequisites')->nullable(); // Store prerequisite course IDs as JSON
            $table->text('admission_requirements')->nullable();
            
            // System fields
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'level', 'type']);
            $table->index(['department', 'faculty']);
            $table->index(['course_code', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
