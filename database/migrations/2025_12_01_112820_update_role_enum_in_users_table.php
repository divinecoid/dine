<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing enum and recreate with new values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('brand_owner', 'store_manager', 'chef', 'waiter', 'kasir') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('brand_owner', 'store_manager') NULL");
    }
};
