<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('mpesa', 'stripe', 'paypal', 'bank_transfer', 'cash', 'cheque', 'other')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('mpesa', 'stripe', 'bank_transfer', 'cash', 'cheque', 'other')");
    }
};