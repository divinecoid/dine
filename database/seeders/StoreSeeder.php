<?php

namespace Database\Seeders;

use App\Models\v1\Brand;
use App\Models\v1\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all brands
        $brands = Brand::all();
        
        if ($brands->isEmpty()) {
            $this->command->warn('No brands found. Please run BrandSeeder first.');
            return;
        }

        $stores = [
            [
                'brand_slug' => 'bakmi-puri',
                'stores' => [
                    [
                        'name' => 'Bakmi Puri Cabang Senayan',
                        'slug' => 'bakmi-puri-senayan',
                        'description' => 'Cabang utama Bakmi Puri di area Senayan dengan suasana yang nyaman.',
                        'address' => 'Jl. Senayan No. 123, Jakarta Selatan',
                        'phone' => '021-12345678',
                        'email' => 'senayan@bkmipuri.com',
                        'latitude' => -6.2277,
                        'longitude' => 106.7997,
                        'image' => null,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Bakmi Puri Cabang Kemang',
                        'slug' => 'bakmi-puri-kemang',
                        'description' => 'Cabang Bakmi Puri di area Kemang dengan fasilitas lengkap.',
                        'address' => 'Jl. Kemang Raya No. 45, Jakarta Selatan',
                        'phone' => '021-87654321',
                        'email' => 'kemang@bkmipuri.com',
                        'latitude' => -6.2606,
                        'longitude' => 106.8103,
                        'image' => null,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'brand_slug' => 'nasi-goreng-koboy',
                'stores' => [
                    [
                        'name' => 'Nasi Goreng Koboy Cabang Menteng',
                        'slug' => 'nasi-goreng-koboy-menteng',
                        'description' => 'Cabang utama Nasi Goreng Koboy di area Menteng.',
                        'address' => 'Jl. Menteng Raya No. 67, Jakarta Pusat',
                        'phone' => '021-23456789',
                        'email' => 'menteng@nasigorengkoboy.com',
                        'latitude' => -6.1944,
                        'longitude' => 106.8229,
                        'image' => null,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Nasi Goreng Koboy Cabang Blok M',
                        'slug' => 'nasi-goreng-koboy-blokm',
                        'description' => 'Cabang Nasi Goreng Koboy di area Blok M yang strategis.',
                        'address' => 'Jl. Blok M No. 89, Jakarta Selatan',
                        'phone' => '021-34567890',
                        'email' => 'blokm@nasigorengkoboy.com',
                        'latitude' => -6.2442,
                        'longitude' => 106.7979,
                        'image' => null,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'brand_slug' => 'nasi-sayur-ahua',
                'stores' => [
                    [
                        'name' => 'Nasi Sayur Ahua Cabang Cikini',
                        'slug' => 'nasi-sayur-ahua-cikini',
                        'description' => 'Cabang utama Nasi Sayur Ahua di area Cikini.',
                        'address' => 'Jl. Cikini Raya No. 12, Jakarta Pusat',
                        'phone' => '021-45678901',
                        'email' => 'cikini@nisayurahua.com',
                        'latitude' => -6.1947,
                        'longitude' => 106.8397,
                        'image' => null,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Nasi Sayur Ahua Cabang Tebet',
                        'slug' => 'nasi-sayur-ahua-tebet',
                        'description' => 'Cabang Nasi Sayur Ahua di area Tebet.',
                        'address' => 'Jl. Tebet Timur Dalam No. 34, Jakarta Selatan',
                        'phone' => '021-56789012',
                        'email' => 'tebet@nisayurahua.com',
                        'latitude' => -6.2335,
                        'longitude' => 106.8523,
                        'image' => null,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'brand_slug' => 'warung-makan-sederhana',
                'stores' => [
                    [
                        'name' => 'Warung Makan Sederhana Cabang Pasar Minggu',
                        'slug' => 'warung-makan-sederhana-pasar-minggu',
                        'description' => 'Cabang utama Warung Makan Sederhana di area Pasar Minggu.',
                        'address' => 'Jl. Pasar Minggu No. 56, Jakarta Selatan',
                        'phone' => '021-67890123',
                        'email' => 'pasarminggu@warungsederhana.com',
                        'latitude' => -6.2824,
                        'longitude' => 106.8406,
                        'image' => null,
                        'is_active' => true,
                    ],
                ],
            ],
            [
                'brand_slug' => 'sate-kambing-pak-asep',
                'stores' => [
                    [
                        'name' => 'Sate Kambing Pak Asep Cabang Kebayoran',
                        'slug' => 'sate-kambing-pak-asep-kebayoran',
                        'description' => 'Cabang utama Sate Kambing Pak Asep di area Kebayoran.',
                        'address' => 'Jl. Kebayoran Baru No. 78, Jakarta Selatan',
                        'phone' => '021-78901234',
                        'email' => 'kebayoran@satepakasep.com',
                        'latitude' => -6.2442,
                        'longitude' => 106.7979,
                        'image' => null,
                        'is_active' => true,
                    ],
                ],
            ],
        ];

        foreach ($stores as $brandStoreData) {
            $brand = Brand::where('slug', $brandStoreData['brand_slug'])->first();
            
            if (!$brand) {
                $this->command->warn("Brand with slug '{$brandStoreData['brand_slug']}' not found. Skipping stores.");
                continue;
            }

            foreach ($brandStoreData['stores'] as $storeData) {
                Store::updateOrCreate(
                    ['slug' => $storeData['slug']],
                    array_merge($storeData, ['mdx_brand_id' => $brand->id])
                );
            }
        }
    }
}

