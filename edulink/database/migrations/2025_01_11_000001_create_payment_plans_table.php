<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_enrollment_id')->constrained('student_enrollments')->onDelete('cascade');
            $table->string('plan_name');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->integer('total_installments');
            $table->integer('completed_installments')->default(0);
            $table->enum('status', ['active', 'completed', 'overdue', 'cancelled'])->default('active');
            $table->decimal('late_fee_rate', 5, 2)->default(5.00); // Percentage
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->integer('grace_period_days')->default(7);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_plans');
    }
};