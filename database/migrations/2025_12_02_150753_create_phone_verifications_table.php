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
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            
            // Phone number to verify
            $table->string('phone')->index();
            
            // OTP code
            $table->string('code', 6); // 6-digit OTP
            
            // Registration ID (if verifying for registration)
            $table->foreignId('registration_id')->nullable()->constrained('registrations')->onDelete('cascade');
            
            // Status
            $table->enum('status', ['pending', 'verified', 'expired'])->default('pending');
            
            // Attempts tracking
            $table->integer('attempts')->default(0);
            $table->integer('max_attempts')->default(3);
            
            // Expiry
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['phone', 'status']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_verifications');
    }
};
