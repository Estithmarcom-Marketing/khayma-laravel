<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name_en' => 'Red',
                'name_ar' => 'أحمر',
                'code' => '#FF0000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Green',
                'name_ar' => 'أخضر',
                'code' => '#00FF00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Blue',
                'name_ar' => 'أزرق',
                'code' => '#0000FF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Yellow',
                'name_ar' => 'أصفر',
                'code' => '#FFFF00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Black',
                'name_ar' => 'أسود',
                'code' => '#000000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'White',
                'name_ar' => 'أبيض',
                'code' => '#FFFFFF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Orange',
                'name_ar' => 'برتقالي',
                'code' => '#FFA500',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Purple',
                'name_ar' => 'بنفسجي',
                'code' => '#800080',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Pink',
                'name_ar' => 'وردي',
                'code' => '#FFC0CB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_en' => 'Brown',
                'name_ar' => 'بني',
                'code' => '#A52A2A',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Color::insert($colors);
    }
}
