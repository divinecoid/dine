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
        Schema::create('trx_order_details', function (Blueprint $table) {
            $table->id();
            
            // Order relationship (mandatory)
            $table->foreignId('trx_order_id')->constrained('trx_orders')->onDelete('cascade');
            
            // Menu relationship (mandatory)
            $table->foreignId('mdx_menu_id')->constrained('mdx_menus')->onDelete('restrict');
            
            // Order detail information
            $table->integer('quantity')->default(1); // Jumlah pesanan
            $table->decimal('unit_price', 10, 2); // Harga per item saat order dibuat (snapshot)
            $table->decimal('discount_amount', 10, 2)->default(0); // Diskon per item
            $table->decimal('subtotal', 15, 2); // Subtotal = (unit_price * quantity) - discount_amount
            
            // Menu information snapshot (untuk keperluan history/reporting)
            $table->string('menu_name')->nullable(); // Nama menu saat order (snapshot)
            $table->text('menu_description')->nullable(); // Deskripsi menu saat order (snapshot)
            
            // Additional information
            $table->text('notes')->nullable(); // Catatan khusus untuk item ini (contoh: "Tanpa pedas", "Extra keju")
            
            $table->timestamps();
            
            // Indexes
            $table->index('trx_order_id');
            $table->index('mdx_menu_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_order_details');
    }
};

