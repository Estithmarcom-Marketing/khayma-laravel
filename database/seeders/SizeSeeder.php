<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            [
                'name_en' => 'Extra Small',
                'name_ar' => 'صغير جداً',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Small',
                'name_ar' => 'صغير',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name_en' => 'Medium',
                'name_ar' => 'متوسط',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name_en' => 'Large',
                'name_ar' => 'كبير',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name_en' => 'Extra Large',
                'name_ar' => 'كبير جداً',
                'created_at' => now(),
                'updated_at' => now(),

            ],
            [
                'name_en' => 'XXL',
                'name_ar' => 'ضخم',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name_en' => 'One Size',
                'name_ar' => 'مقاس واحد',
                'created_at' => now(),
                'updated_at' => now(),

            ],
        ];
        Size::insert($sizes);
    }
}
