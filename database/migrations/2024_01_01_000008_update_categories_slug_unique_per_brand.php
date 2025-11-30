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
        Schema::table('mdx_categories', function (Blueprint $table) {
            // Drop the existing unique constraint on slug
            $table->dropUnique(['slug']);
            
            // Add composite unique constraint on brand_id and slug
            // This allows same slug for different brands, but unique per brand
            $table->unique(['mdx_brand_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_categories', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique(['mdx_brand_id', 'slug']);
            
            // Restore the original unique constraint on slug
            $table->unique('slug');
        });
    }
};

