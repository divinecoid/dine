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
        Schema::table('trx_payments', function (Blueprint $table) {
            $table->string('xendit_payment_id')->nullable()->after('reference_number');
            $table->text('xendit_qr_code')->nullable()->after('xendit_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trx_payments', function (Blueprint $table) {
            $table->dropColumn(['xendit_payment_id', 'xendit_qr_code']);
        });
    }
};
