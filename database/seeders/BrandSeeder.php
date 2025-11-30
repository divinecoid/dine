<?php

namespace Database\Seeders;

use App\Models\v1\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Bakmi Puri',
                'slug' => 'bakmi-puri',
                'description' => 'Bakmi Puri menyajikan bakmi dengan cita rasa autentik dan lezat. Nikmati berbagai varian bakmi yang menggugah selera.',
                'logo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Nasi Goreng Koboy',
                'slug' => 'nasi-goreng-koboy',
                'description' => 'Nasi Goreng Koboy dengan cita rasa khas yang pedas dan menggugah selera. Tersedia berbagai varian nasi goreng yang nikmat.',
                'logo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Nasi Sayur Ahua',
                'slug' => 'nasi-sayur-ahua',
                'description' => 'Nasi Sayur Ahua menyajikan menu sehat dengan berbagai pilihan sayuran segar dan nasi hangat. Cocok untuk yang suka makanan sehat.',
                'logo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Warung Makan Sederhana',
                'slug' => 'warung-makan-sederhana',
                'description' => 'Warung Makan Sederhana dengan menu rumah makan yang lezat dan harga terjangkau. Makanan tradisional yang menggugah selera.',
                'logo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Sate Kambing Pak Asep',
                'slug' => 'sate-kambing-pak-asep',
                'description' => 'Sate Kambing Pak Asep dengan bumbu kacang yang khas dan daging yang empuk. Sate terlezat dengan cita rasa tradisional.',
                'logo' => null,
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brandData) {
            Brand::updateOrCreate(
                ['slug' => $brandData['slug']],
                $brandData
            );
        }
    }
}

