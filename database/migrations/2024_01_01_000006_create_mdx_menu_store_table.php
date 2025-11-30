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
        Schema::create('mdx_menu_store', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mdx_menu_id')->constrained('mdx_menus')->onDelete('cascade');
            $table->foreignId('mdx_store_id')->constrained('mdx_stores')->onDelete('cascade');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Ensure unique combination of menu and store
            $table->unique(['mdx_menu_id', 'mdx_store_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mdx_menu_store');
    }
};

