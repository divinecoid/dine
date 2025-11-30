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
        Schema::create('mdx_bank_accounts', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship: either brand_id or store_id (but not both)
            $table->foreignId('mdx_brand_id')->nullable()->constrained('mdx_brands')->onDelete('cascade');
            $table->foreignId('mdx_store_id')->nullable()->constrained('mdx_stores')->onDelete('cascade');
            
            // Bank account information
            $table->string('account_name'); // Nama pemegang rekening
            $table->string('account_number'); // Nomor rekening
            $table->string('bank_name'); // Nama bank
            $table->string('bank_code')->nullable(); // Kode bank (untuk integrasi)
            $table->string('branch_name')->nullable(); // Nama cabang
            $table->string('currency', 3)->default('IDR'); // Mata uang
            
            // Balance information
            $table->decimal('balance', 15, 2)->default(0); // Saldo saat ini
            $table->decimal('minimum_balance', 15, 2)->default(0)->nullable(); // Saldo minimum
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable(); // Catatan tambahan
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('mdx_brand_id');
            $table->index('mdx_store_id');
            
            // Ensure either brand_id or store_id is set (handled in application logic)
            // Note: MySQL doesn't support CHECK constraints well, so we rely on application logic
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mdx_bank_accounts');
    }
};

