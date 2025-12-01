<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed users with roles first
        $this->call([
            UserSeeder::class,
        ]);

        // Seed master data in correct order
        $this->call([
            BrandSeeder::class,
            StoreSeeder::class,
            CategorySeeder::class,
            MenuSeeder::class,
            TableSeeder::class,
        ]);
    }
}
