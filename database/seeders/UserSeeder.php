<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\v1\Brand;
use App\Models\v1\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Brand Owner User
        $brandOwner = User::updateOrCreate(
            ['email' => 'brandowner@dine.co.id'],
            [
                'name' => 'Brand Owner',
                'email' => 'brandowner@dine.co.id',
                'password' => Hash::make('password'),
                'role' => 'brand_owner',
                'email_verified_at' => now(),
            ]
        );

        // Assign first brand to brand owner (if exists)
        $firstBrand = Brand::first();
        if ($firstBrand && !$brandOwner->brands()->where('mdx_brands.id', $firstBrand->id)->exists()) {
            $brandOwner->brands()->attach($firstBrand->id);
            $this->command->info("Assigned brand '{$firstBrand->name}' to Brand Owner");
        }

        // Store Manager User
        $storeManager = User::updateOrCreate(
            ['email' => 'storemanager@dine.co.id'],
            [
                'name' => 'Store Manager',
                'email' => 'storemanager@dine.co.id',
                'password' => Hash::make('password'),
                'role' => 'store_manager',
                'email_verified_at' => now(),
            ]
        );

        // Assign first store to store manager (if exists and not already assigned)
        $firstStore = Store::whereNull('user_id')->first();
        if ($firstStore) {
            $firstStore->update(['user_id' => $storeManager->id]);
            $this->command->info("Assigned store '{$firstStore->name}' to Store Manager");
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Brand Owner: brandowner@dine.co.id / password');
        $this->command->info('Store Manager: storemanager@dine.co.id / password');
    }
}
