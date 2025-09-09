<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Students table for Edulink International College Nairobi
     * Stores student authentication and profile information
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // Authentication fields
            $table->string('student_id')->unique(); // e.g., EDU2024001
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('national_id')->nullable();
            $table->string('passport_number')->nullable();
            
            // Address Information
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Kenya');
            $table->string('postal_code')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Academic Information
            $table->enum('status', ['active', 'inactive', 'suspended', 'graduated', 'deferred'])->default('active');
            $table->date('enrollment_date');
            $table->date('expected_graduation_date')->nullable();
            
            // Financial Status
            $table->decimal('total_fees_owed', 12, 2)->default(0.00);
            $table->decimal('total_fees_paid', 12, 2)->default(0.00);
            $table->boolean('has_outstanding_fees')->default(false);
            $table->date('last_payment_date')->nullable();
            
            // System fields
            $table->rememberToken();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'enrollment_date']);
            $table->index(['has_outstanding_fees']);
            $table->index(['email', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
