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
        Schema::create('mdx_category_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mdx_category_id')->constrained('mdx_categories')->onDelete('cascade');
            $table->foreignId('mdx_menu_id')->constrained('mdx_menus')->onDelete('cascade');
            $table->timestamps();

            // Ensure unique combination of category and menu
            $table->unique(['mdx_category_id', 'mdx_menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mdx_category_menu');
    }
};

