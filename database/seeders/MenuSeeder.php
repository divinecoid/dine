<?php

namespace Database\Seeders;

use App\Models\v1\Brand;
use App\Models\v1\Category;
use App\Models\v1\Menu;
use App\Models\v1\Store;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'brand_slug' => 'bakmi-puri',
                'menus' => [
                    [
                        'name' => 'Bakmi Ayam Spesial',
                        'slug' => 'bakmi-ayam-spesial',
                        'description' => 'Bakmi dengan ayam, pangsit goreng, dan sayuran segar',
                        'price' => 35000,
                        'categories' => ['bakmi-spesial', 'bakmi-ayam'],
                    ],
                    [
                        'name' => 'Bakmi Ayam Original',
                        'slug' => 'bakmi-ayam-original',
                        'description' => 'Bakmi dengan ayam suwir dan sayuran',
                        'price' => 28000,
                        'categories' => ['bakmi-ayam'],
                    ],
                    [
                        'name' => 'Bakmi Udang Spesial',
                        'slug' => 'bakmi-udang-spesial',
                        'description' => 'Bakmi dengan udang segar, pangsit goreng, dan sayuran',
                        'price' => 45000,
                        'categories' => ['bakmi-spesial', 'bakmi-seafood'],
                    ],
                    [
                        'name' => 'Bakmi Cumi',
                        'slug' => 'bakmi-cumi',
                        'description' => 'Bakmi dengan cumi segar dan sayuran',
                        'price' => 42000,
                        'categories' => ['bakmi-seafood'],
                    ],
                    [
                        'name' => 'Es Teh Manis',
                        'slug' => 'es-teh-manis',
                        'description' => 'Es teh manis segar',
                        'price' => 8000,
                        'categories' => ['minuman'],
                    ],
                    [
                        'name' => 'Es Jeruk',
                        'slug' => 'es-jeruk',
                        'description' => 'Es jeruk peras segar',
                        'price' => 10000,
                        'categories' => ['minuman'],
                    ],
                ],
            ],
            [
                'brand_slug' => 'nasi-goreng-koboy',
                'menus' => [
                    [
                        'name' => 'Nasi Goreng Spesial Koboy',
                        'slug' => 'nasi-goreng-spesial-koboy',
                        'description' => 'Nasi goreng dengan ayam, udang, telur, dan kerupuk',
                        'price' => 38000,
                        'categories' => ['nasi-goreng-spesial'],
                    ],
                    [
                        'name' => 'Nasi Goreng Seafood Koboy',
                        'slug' => 'nasi-goreng-seafood-koboy',
                        'description' => 'Nasi goreng dengan udang, cumi, dan kerang',
                        'price' => 45000,
                        'categories' => ['nasi-goreng-seafood'],
                    ],
                    [
                        'name' => 'Nasi Goreng Ayam Koboy',
                        'slug' => 'nasi-goreng-ayam-koboy',
                        'description' => 'Nasi goreng dengan ayam suwir dan telur',
                        'price' => 32000,
                        'categories' => ['nasi-goreng-ayam'],
                    ],
                    [
                        'name' => 'Nasi Goreng Petai',
                        'slug' => 'nasi-goreng-petai',
                        'description' => 'Nasi goreng dengan petai dan terasi',
                        'price' => 35000,
                        'categories' => ['nasi-goreng-spesial'],
                    ],
                    [
                        'name' => 'Es Teh Manis',
                        'slug' => 'es-teh-manis',
                        'description' => 'Es teh manis segar',
                        'price' => 8000,
                        'categories' => ['minuman'],
                    ],
                    [
                        'name' => 'Es Jeruk',
                        'slug' => 'es-jeruk',
                        'description' => 'Es jeruk peras segar',
                        'price' => 10000,
                        'categories' => ['minuman'],
                    ],
                ],
            ],
            [
                'brand_slug' => 'nasi-sayur-ahua',
                'menus' => [
                    [
                        'name' => 'Nasi Sayur Spesial Ahua',
                        'slug' => 'nasi-sayur-spesial-ahua',
                        'description' => 'Nasi sayur dengan ayam, ikan, tahu, tempe, dan sambal',
                        'price' => 28000,
                        'categories' => ['nasi-sayur-spesial'],
                    ],
                    [
                        'name' => 'Nasi Sayur Ayam Ahua',
                        'slug' => 'nasi-sayur-ayam-ahua',
                        'description' => 'Nasi sayur dengan ayam goreng dan sambal',
                        'price' => 25000,
                        'categories' => ['nasi-sayur-ayam'],
                    ],
                    [
                        'name' => 'Nasi Sayur Ikan Ahua',
                        'slug' => 'nasi-sayur-ikan-ahua',
                        'description' => 'Nasi sayur dengan ikan goreng dan sambal',
                        'price' => 24000,
                        'categories' => ['nasi-sayur-ikan'],
                    ],
                    [
                        'name' => 'Nasi Sayur Tahu Tempe',
                        'slug' => 'nasi-sayur-tahu-tempe',
                        'description' => 'Nasi sayur dengan tahu dan tempe goreng',
                        'price' => 18000,
                        'categories' => ['nasi-sayur-spesial'],
                    ],
                    [
                        'name' => 'Es Teh Manis',
                        'slug' => 'es-teh-manis',
                        'description' => 'Es teh manis segar',
                        'price' => 8000,
                        'categories' => ['minuman'],
                    ],
                    [
                        'name' => 'Es Jeruk',
                        'slug' => 'es-jeruk',
                        'description' => 'Es jeruk peras segar',
                        'price' => 10000,
                        'categories' => ['minuman'],
                    ],
                ],
            ],
            [
                'brand_slug' => 'warung-makan-sederhana',
                'menus' => [
                    [
                        'name' => 'Ayam Goreng',
                        'slug' => 'ayam-goreng',
                        'description' => 'Ayam goreng crispy dengan bumbu khas',
                        'price' => 22000,
                        'categories' => ['lauk-pauk'],
                    ],
                    [
                        'name' => 'Ikan Gurame Goreng',
                        'slug' => 'ikan-gurame-goreng',
                        'description' => 'Ikan gurame goreng garing',
                        'price' => 35000,
                        'categories' => ['lauk-pauk'],
                    ],
                    [
                        'name' => 'Sayur Lodeh',
                        'slug' => 'sayur-lodeh',
                        'description' => 'Sayur lodeh dengan santan',
                        'price' => 12000,
                        'categories' => ['sayuran'],
                    ],
                    [
                        'name' => 'Sayur Asem',
                        'slug' => 'sayur-asem',
                        'description' => 'Sayur asem segar',
                        'price' => 10000,
                        'categories' => ['sayuran'],
                    ],
                    [
                        'name' => 'Sambal Terasi',
                        'slug' => 'sambal-terasi',
                        'description' => 'Sambal terasi pedas',
                        'price' => 5000,
                        'categories' => ['sambal'],
                    ],
                    [
                        'name' => 'Es Teh Manis',
                        'slug' => 'es-teh-manis',
                        'description' => 'Es teh manis segar',
                        'price' => 5000,
                        'categories' => ['minuman'],
                    ],
                ],
            ],
            [
                'brand_slug' => 'sate-kambing-pak-asep',
                'menus' => [
                    [
                        'name' => 'Sate Kambing (10 tusuk)',
                        'slug' => 'sate-kambing-10',
                        'description' => 'Sate kambing dengan bumbu kacang, 10 tusuk',
                        'price' => 45000,
                        'categories' => ['sate-kambing'],
                    ],
                    [
                        'name' => 'Sate Kambing (20 tusuk)',
                        'slug' => 'sate-kambing-20',
                        'description' => 'Sate kambing dengan bumbu kacang, 20 tusuk',
                        'price' => 85000,
                        'categories' => ['sate-kambing'],
                    ],
                    [
                        'name' => 'Sate Ayam (10 tusuk)',
                        'slug' => 'sate-ayam-10',
                        'description' => 'Sate ayam dengan bumbu kacang, 10 tusuk',
                        'price' => 35000,
                        'categories' => ['sate-ayam'],
                    ],
                    [
                        'name' => 'Gulai Kambing',
                        'slug' => 'gulai-kambing',
                        'description' => 'Gulai kambing dengan bumbu khas',
                        'price' => 50000,
                        'categories' => ['gulai'],
                    ],
                    [
                        'name' => 'Es Teh Manis',
                        'slug' => 'es-teh-manis',
                        'description' => 'Es teh manis segar',
                        'price' => 8000,
                        'categories' => ['minuman'],
                    ],
                    [
                        'name' => 'Es Jeruk',
                        'slug' => 'es-jeruk',
                        'description' => 'Es jeruk peras segar',
                        'price' => 10000,
                        'categories' => ['minuman'],
                    ],
                ],
            ],
        ];

        foreach ($menus as $brandMenuData) {
            $brand = Brand::where('slug', $brandMenuData['brand_slug'])->first();
            
            if (!$brand) {
                $this->command->warn("Brand with slug '{$brandMenuData['brand_slug']}' not found. Skipping menus.");
                continue;
            }

            // Get all stores for this brand
            $stores = Store::where('mdx_brand_id', $brand->id)->get();

            foreach ($brandMenuData['menus'] as $menuData) {
                // Get category slugs
                $categorySlugs = $menuData['categories'];
                unset($menuData['categories']);

                // Create or update menu
                $menu = Menu::updateOrCreate(
                    ['slug' => $menuData['slug']],
                    array_merge($menuData, [
                        'is_available' => true,
                        'is_active' => true,
                        'sort_order' => 1,
                        'image' => null,
                    ])
                );

                // Attach categories
                $categories = Category::where('mdx_brand_id', $brand->id)
                    ->whereIn('slug', $categorySlugs)
                    ->get();

                if ($categories->isNotEmpty()) {
                    $categoryIds = $categories->pluck('id')->toArray();
                    $menu->categories()->syncWithoutDetaching($categoryIds);
                }

                // Attach menu to all stores of this brand with availability
                $storePivots = [];
                foreach ($stores as $store) {
                    $storePivots[$store->id] = [
                        'is_available' => true,
                        'stock_quantity' => 100,
                        'min_stock_threshold' => 10,
                        'out_of_stock_reason' => null,
                    ];
                }
                
                // Sync stores - this will update existing or create new
                $menu->stores()->sync($storePivots);
            }
        }
    }
}

