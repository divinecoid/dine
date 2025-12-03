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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            
            // Registration data
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password'); // Hashed password
            $table->enum('account_type', ['CORE', 'SCALE', 'INFINITE']);
            
            // Status tracking
            $table->enum('status', ['pending_verification', 'pending_payment', 'payment_verified', 'completed', 'expired', 'cancelled'])
                ->default('pending_verification');
            
            // Phone verification
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('phone_verified_at')->nullable();
            
            // Payment information (for SCALE and INFINITE)
            $table->decimal('payment_amount', 15, 2)->nullable();
            $table->enum('payment_method', ['qris', 'virtual_account'])->nullable();
            $table->string('payment_reference')->nullable(); // QRIS/VA reference
            $table->timestamp('payment_verified_at')->nullable();
            
            // Expiry
            $table->timestamp('expires_at')->nullable(); // Registration expires if not completed
            
            $table->timestamps();
            
            // Indexes
            $table->index('email');
            $table->index('phone');
            $table->index('status');
            $table->index('account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
