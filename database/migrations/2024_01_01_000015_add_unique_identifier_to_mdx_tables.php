<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mdx_tables', function (Blueprint $table) {
            // Add unique identifier column (UUID)
            $table->uuid('unique_identifier')->nullable()->unique()->after('id');
        });

        // Generate unique identifiers for existing records
        $tables = DB::table('mdx_tables')->get();
        foreach ($tables as $table) {
            DB::table('mdx_tables')
                ->where('id', $table->id)
                ->update([
                    'unique_identifier' => (string) \Illuminate\Support\Str::uuid(),
                ]);
        }

        // Make unique_identifier NOT NULL after populating existing records
        Schema::table('mdx_tables', function (Blueprint $table) {
            $table->uuid('unique_identifier')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mdx_tables', function (Blueprint $table) {
            $table->dropColumn('unique_identifier');
        });
    }
};

