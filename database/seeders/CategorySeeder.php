<?php

namespace Database\Seeders;

use App\Models\v1\Brand;
use App\Models\v1\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'brand_slug' => 'bakmi-puri',
                'categories' => [
                    ['name' => 'Bakmi Spesial', 'slug' => 'bakmi-spesial', 'description' => 'Bakmi dengan topping spesial', 'sort_order' => 1],
                    ['name' => 'Bakmi Ayam', 'slug' => 'bakmi-ayam', 'description' => 'Bakmi dengan topping ayam', 'sort_order' => 2],
                    ['name' => 'Bakmi Seafood', 'slug' => 'bakmi-seafood', 'description' => 'Bakmi dengan topping seafood', 'sort_order' => 3],
                    ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Berbagai minuman segar', 'sort_order' => 4],
                ],
            ],
            [
                'brand_slug' => 'nasi-goreng-koboy',
                'categories' => [
                    ['name' => 'Nasi Goreng Spesial', 'slug' => 'nasi-goreng-spesial', 'description' => 'Nasi goreng dengan topping spesial', 'sort_order' => 1],
                    ['name' => 'Nasi Goreng Seafood', 'slug' => 'nasi-goreng-seafood', 'description' => 'Nasi goreng dengan seafood', 'sort_order' => 2],
                    ['name' => 'Nasi Goreng Ayam', 'slug' => 'nasi-goreng-ayam', 'description' => 'Nasi goreng dengan ayam', 'sort_order' => 3],
                    ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Berbagai minuman segar', 'sort_order' => 4],
                ],
            ],
            [
                'brand_slug' => 'nasi-sayur-ahua',
                'categories' => [
                    ['name' => 'Nasi Sayur Spesial', 'slug' => 'nasi-sayur-spesial', 'description' => 'Nasi sayur dengan lauk spesial', 'sort_order' => 1],
                    ['name' => 'Nasi Sayur Ayam', 'slug' => 'nasi-sayur-ayam', 'description' => 'Nasi sayur dengan ayam', 'sort_order' => 2],
                    ['name' => 'Nasi Sayur Ikan', 'slug' => 'nasi-sayur-ikan', 'description' => 'Nasi sayur dengan ikan', 'sort_order' => 3],
                    ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Berbagai minuman segar', 'sort_order' => 4],
                ],
            ],
            [
                'brand_slug' => 'warung-makan-sederhana',
                'categories' => [
                    ['name' => 'Lauk Pauk', 'slug' => 'lauk-pauk', 'description' => 'Berbagai lauk pauk', 'sort_order' => 1],
                    ['name' => 'Sayuran', 'slug' => 'sayuran', 'description' => 'Berbagai sayuran', 'sort_order' => 2],
                    ['name' => 'Sambal', 'slug' => 'sambal', 'description' => 'Berbagai sambal', 'sort_order' => 3],
                    ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Berbagai minuman segar', 'sort_order' => 4],
                ],
            ],
            [
                'brand_slug' => 'sate-kambing-pak-asep',
                'categories' => [
                    ['name' => 'Sate Kambing', 'slug' => 'sate-kambing', 'description' => 'Sate kambing dengan bumbu kacang', 'sort_order' => 1],
                    ['name' => 'Sate Ayam', 'slug' => 'sate-ayam', 'description' => 'Sate ayam dengan bumbu kacang', 'sort_order' => 2],
                    ['name' => 'Gulai', 'slug' => 'gulai', 'description' => 'Berbagai gulai', 'sort_order' => 3],
                    ['name' => 'Minuman', 'slug' => 'minuman', 'description' => 'Berbagai minuman segar', 'sort_order' => 4],
                ],
            ],
        ];

        foreach ($categories as $brandCategoryData) {
            $brand = Brand::where('slug', $brandCategoryData['brand_slug'])->first();
            
            if (!$brand) {
                $this->command->warn("Brand with slug '{$brandCategoryData['brand_slug']}' not found. Skipping categories.");
                continue;
            }

            foreach ($brandCategoryData['categories'] as $categoryData) {
                Category::updateOrCreate(
                    [
                        'mdx_brand_id' => $brand->id,
                        'slug' => $categoryData['slug'],
                    ],
                    array_merge($categoryData, [
                        'mdx_brand_id' => $brand->id,
                        'is_active' => true,
                        'image' => null,
                    ])
                );
            }
        }
    }
}

