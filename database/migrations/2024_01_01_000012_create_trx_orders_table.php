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
        Schema::create('trx_orders', function (Blueprint $table) {
            $table->id();
            
            // Store relationship (mandatory)
            $table->foreignId('mdx_store_id')->constrained('mdx_stores')->onDelete('restrict');
            
            // Brand relationship (optional, bisa dari store atau set langsung)
            $table->foreignId('mdx_brand_id')->nullable()->constrained('mdx_brands')->onDelete('restrict');
            
            // Table relationship (nullable, hanya untuk dine-in)
            $table->foreignId('mdx_table_id')->nullable()->constrained('mdx_tables')->onDelete('set null');
            
            // Order information
            $table->string('order_number')->unique(); // Nomor order (unique, auto-generated)
            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery'])
                ->default('dine_in'); // Tipe order
            
            // Customer information
            $table->string('customer_name'); // Nama pemesan (mandatory)
            $table->string('customer_phone')->nullable(); // Nomor telepon pemesan
            $table->string('customer_email')->nullable(); // Email pemesan
            
            // Delivery information (hanya untuk delivery)
            $table->text('delivery_address')->nullable(); // Alamat pengiriman
            $table->decimal('delivery_latitude', 10, 8)->nullable(); // Koordinat pengiriman
            $table->decimal('delivery_longitude', 11, 8)->nullable(); // Koordinat pengiriman
            
            // Order status
            $table->enum('status', [
                'pending',      // Menunggu konfirmasi
                'confirmed',    // Sudah dikonfirmasi
                'preparing',    // Sedang disiapkan
                'ready',        // Siap diambil/diantar
                'completed',    // Selesai
                'cancelled'     // Dibatalkan
            ])->default('pending');
            
            // Financial information
            $table->decimal('subtotal', 15, 2)->default(0); // Subtotal sebelum discount/tax
            $table->decimal('discount_amount', 15, 2)->default(0); // Jumlah diskon
            $table->decimal('tax_amount', 15, 2)->default(0); // Jumlah pajak
            $table->decimal('service_charge', 15, 2)->default(0); // Service charge
            $table->decimal('total_amount', 15, 2)->default(0); // Total akhir
            
            // Payment information
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'refunded'])
                ->default('pending'); // Status pembayaran
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'e_wallet', 'other'])
                ->nullable(); // Metode pembayaran
            
            // Additional information
            $table->text('notes')->nullable(); // Catatan order
            $table->text('admin_notes')->nullable(); // Catatan admin/internal
            
            // Timestamps
            $table->timestamp('ordered_at')->nullable(); // Waktu order dibuat
            $table->timestamp('confirmed_at')->nullable(); // Waktu dikonfirmasi
            $table->timestamp('prepared_at')->nullable(); // Waktu selesai disiapkan
            $table->timestamp('completed_at')->nullable(); // Waktu selesai
            $table->timestamp('cancelled_at')->nullable(); // Waktu dibatalkan
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('mdx_store_id');
            $table->index('mdx_brand_id');
            $table->index('mdx_table_id');
            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_type');
            $table->index('ordered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_orders');
    }
};

