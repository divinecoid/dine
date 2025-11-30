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
        Schema::table('mdx_menu_store', function (Blueprint $table) {
            // Stock quantity per store (nullable karena mungkin tidak semua menu tracking stok)
            $table->integer('stock_quantity')->nullable()->after('is_available');
            
            // Reason/note ketika out of stock (nullable)
            $table->text('out_of_stock_reason')->nullable()->after('stock_quantity');
            
            // Minimum stock threshold untuk auto-set is_available (nullable)
            $table->integer('min_stock_threshold')->nullable()->after('out_of_stock_reason');
            
            // Index untuk performa query filtering berdasarkan stock
            $table->index(['mdx_store_id', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_menu_store', function (Blueprint $table) {
            $table->dropIndex(['mdx_store_id', 'is_available']);
            $table->dropColumn(['stock_quantity', 'out_of_stock_reason', 'min_stock_threshold']);
        });
    }
};

