<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Admins table for Edulink International College Nairobi
     * Manages administrative users with role-based access control
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            
            // Authentication fields
            $table->string('admin_id')->unique(); // e.g., ADM001
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('employee_id')->unique()->nullable();
            
            // Role and Permissions
            $table->enum('role', ['super_admin', 'admin', 'finance_officer', 'registrar', 'academic_officer', 'support_staff'])->default('admin');
            $table->json('permissions')->nullable(); // Store specific permissions as JSON
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            
            // Department Information
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('hire_date')->nullable();
            
            // Access Control
            $table->boolean('can_manage_students')->default(false);
            $table->boolean('can_manage_courses')->default(false);
            $table->boolean('can_manage_fees')->default(false);
            $table->boolean('can_view_payments')->default(false);
            $table->boolean('can_process_payments')->default(false);
            $table->boolean('can_generate_reports')->default(false);
            $table->boolean('can_manage_admins')->default(false);
            
            // Security
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->boolean('force_password_change')->default(false);
            
            // System fields
            $table->rememberToken();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['role', 'status']);
            $table->index(['department', 'position']);
            $table->index(['email', 'admin_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
