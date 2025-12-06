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
        // Update enum for trx_payments table
        DB::statement("ALTER TABLE trx_payments MODIFY COLUMN payment_method ENUM('cash', 'card', 'transfer', 'e_wallet', 'qris', 'other') DEFAULT 'cash'");
        
        // Update enum for trx_orders table
        DB::statement("ALTER TABLE trx_orders MODIFY COLUMN payment_method ENUM('cash', 'card', 'transfer', 'e_wallet', 'qris', 'other') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum for trx_payments table
        DB::statement("ALTER TABLE trx_payments MODIFY COLUMN payment_method ENUM('cash', 'card', 'transfer', 'e_wallet', 'other') DEFAULT 'cash'");
        
        // Revert enum for trx_orders table
        DB::statement("ALTER TABLE trx_orders MODIFY COLUMN payment_method ENUM('cash', 'card', 'transfer', 'e_wallet', 'other') NULL");
    }
};
