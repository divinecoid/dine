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
        Schema::create('trx_payments', function (Blueprint $table) {
            $table->id();
            
            // Store relationship (mandatory)
            $table->foreignId('mdx_store_id')->constrained('mdx_stores')->onDelete('restrict');
            
            // Brand relationship (optional, bisa dari store atau set langsung)
            $table->foreignId('mdx_brand_id')->nullable()->constrained('mdx_brands')->onDelete('restrict');
            
            // Order relationship (mandatory)
            $table->foreignId('trx_order_id')->constrained('trx_orders')->onDelete('restrict');
            
            // Payment information
            $table->string('payment_number')->unique(); // Nomor pembayaran (unique, auto-generated)
            $table->decimal('amount', 15, 2); // Jumlah pembayaran
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'e_wallet', 'other'])
                ->default('cash'); // Metode pembayaran
            
            // Customer information (dari order, tapi bisa di-override)
            $table->string('customer_name'); // Nama customer
            $table->string('customer_phone')->nullable(); // Nomor telepon customer
            
            // Payment details
            $table->text('notes')->nullable(); // Catatan pembayaran
            $table->text('reference_number')->nullable(); // Nomor referensi (untuk transfer/e-wallet)
            
            // Payment status
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])
                ->default('completed'); // Status pembayaran
            
            // Timestamps
            $table->timestamp('paid_at')->nullable(); // Waktu pembayaran dilakukan
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('mdx_store_id');
            $table->index('mdx_brand_id');
            $table->index('trx_order_id');
            $table->index('payment_number');
            $table->index('status');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_payments');
    }
};
