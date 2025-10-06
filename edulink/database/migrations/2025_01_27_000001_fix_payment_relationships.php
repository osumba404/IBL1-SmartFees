<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Ensure the student_enrollment_id foreign key exists and is properly indexed
            if (!Schema::hasColumn('payments', 'student_enrollment_id')) {
                $table->foreignId('student_enrollment_id')->nullable()->constrained('student_enrollments')->onDelete('set null');
            }
            
            // Add index for better performance on payment queries
            $table->index(['student_id', 'student_enrollment_id', 'status']);
            $table->index(['payment_method', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'student_enrollment_id', 'status']);
            $table->dropIndex(['payment_method', 'status', 'created_at']);
        });
    }
};