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
        // Update enum in users table
        DB::statement("ALTER TABLE users MODIFY COLUMN account_type ENUM('FREE_TRIAL', 'CORE', 'SCALE', 'INFINITE') NULL COMMENT 'Account type for brand owner (FREE_TRIAL, CORE, SCALE, INFINITE)'");
        
        // Update enum in registrations table
        DB::statement("ALTER TABLE registrations MODIFY COLUMN account_type ENUM('FREE_TRIAL', 'CORE', 'SCALE', 'INFINITE') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum in users table
        DB::statement("ALTER TABLE users MODIFY COLUMN account_type ENUM('CORE', 'SCALE', 'INFINITE') NULL COMMENT 'Account type for brand owner (CORE, SCALE, INFINITE)'");
        
        // Revert enum in registrations table
        DB::statement("ALTER TABLE registrations MODIFY COLUMN account_type ENUM('CORE', 'SCALE', 'INFINITE') NOT NULL");
    }
};
