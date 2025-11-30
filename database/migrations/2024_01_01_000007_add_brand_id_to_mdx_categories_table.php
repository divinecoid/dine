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
            $table->foreignId('mdx_brand_id')->after('id')->constrained('mdx_brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_categories', function (Blueprint $table) {
            $table->dropForeign(['mdx_brand_id']);
            $table->dropColumn('mdx_brand_id');
        });
    }
};

