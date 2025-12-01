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
        // Create pivot table for brand-user (many-to-many: 1 brand owner can own multiple brands)
        Schema::create('mdx_brand_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mdx_brand_id')->constrained('mdx_brands')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure one user can only be associated with a brand once
            $table->unique(['mdx_brand_id', 'user_id']);
        });

        // Add user_id to stores (one-to-one: 1 store manager manages 1 store)
        Schema::table('mdx_stores', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('mdx_brand_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_stores', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::dropIfExists('mdx_brand_user');
    }
};
