<?php

namespace Database\Seeders;

use App\Models\v1\Store;
use App\Models\v1\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all stores
        $stores = Store::all();
        
        if ($stores->isEmpty()) {
            $this->command->warn('No stores found. Please run StoreSeeder first.');
            return;
        }

        foreach ($stores as $store) {
            // Create tables for each store
            // Small stores get 5-8 tables, larger stores get 10-15 tables
            $numberOfTables = rand(5, 15);
            
            for ($i = 1; $i <= $numberOfTables; $i++) {
                // Determine floor (some stores have 2 floors)
                $floor = $i <= ($numberOfTables / 2) ? 1 : 2;
                
                // Determine zone
                $zones = ['Indoor', 'Outdoor', 'VIP', 'Regular'];
                $zone = $zones[array_rand($zones)];
                
                // Determine capacity (2, 4, 6, or 8 people)
                $capacity = [2, 4, 6, 8][array_rand([2, 4, 6, 8])];
                
                // Determine status
                $statuses = ['available', 'occupied', 'reserved'];
                $status = $statuses[array_rand($statuses)];

                $table = Table::firstOrNew(
                    [
                        'mdx_store_id' => $store->id,
                        'table_number' => $i,
                    ]
                );

                // Generate unique_identifier only if it's a new record or empty
                if (!$table->exists || empty($table->unique_identifier)) {
                    $table->unique_identifier = (string) Str::uuid();
                }

                // Set other attributes (will update existing or set for new)
                $table->name = "Meja {$i}";
                $table->capacity = $capacity;
                $table->status = $status;
                $table->zone = $zone;
                $table->floor = $floor;
                $table->notes = null;
                $table->sort_order = $i;
                $table->is_active = true;

                $table->save();
            }
        }
    }
}

