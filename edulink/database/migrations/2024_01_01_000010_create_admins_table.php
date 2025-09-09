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
            $table->string('name'); // Full name
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Personal Information
            $table->string('phone')->nullable();
            $table->string('employee_id')->unique()->nullable();
            
            // Role and Permissions
            $table->enum('role', ['super_admin', 'admin'])->default('admin');
            $table->boolean('is_active')->default(true);
            
            // Department Information
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('hire_date')->nullable();
            
            // Access Control Permissions
            $table->boolean('can_manage_students')->default(false);
            $table->boolean('can_manage_courses')->default(false);
            $table->boolean('can_manage_payments')->default(false);
            $table->boolean('can_view_reports')->default(false);
            $table->boolean('can_approve_students')->default(false);
            $table->boolean('can_manage_fees')->default(false);
            
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
            $table->index(['role', 'is_active']);
            $table->index(['department', 'position']);
            $table->index(['email']);
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
