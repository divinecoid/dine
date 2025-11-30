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
        Schema::create('mdx_tables', function (Blueprint $table) {
            $table->id();
            
            // Store relationship
            $table->foreignId('mdx_store_id')->constrained('mdx_stores')->onDelete('cascade');
            
            // Table information
            $table->string('table_number'); // Nomor meja (e.g., "1", "A1", "VIP-1")
            $table->string('name')->nullable(); // Nama alias meja (optional)
            $table->integer('capacity')->default(2); // Kapasitas maksimal (jumlah kursi/orang)
            
            // Status
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance', 'closed'])
                ->default('available'); // Status meja
            
            // Location/Zone information
            $table->string('zone')->nullable(); // Area/zone meja (e.g., "Indoor", "Outdoor", "VIP", "Terrace")
            $table->string('floor')->nullable(); // Lantai (e.g., "1", "2", "Rooftop")
            
            // Additional information
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->integer('sort_order')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true); // Status aktif
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('mdx_store_id');
            $table->index('status');
            $table->index('zone');
            
            // Ensure unique table number per store
            $table->unique(['mdx_store_id', 'table_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mdx_tables');
    }
};

