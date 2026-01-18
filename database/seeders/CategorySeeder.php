<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $categories = [
            [
                'name_ar' => 'اقمشة',
                'name_en' => 'Fabrics',
                'description_ar' => 'تصنيف الاقمشة',
                'description_en' => 'Fabrics category',
                'slug_en' => 'fabrics',
                'slug_ar' => 'اقمشة',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'خيام',
                'name_en' => 'Tents',
                'description_ar' => 'تصنيف الخيام',
                'description_en' => 'Tents category',
                'slug_en' => 'tents',
                'slug_ar' => 'خيام',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'جلسات بر',
                'name_en' => 'Outdoor Seating',
                'description_ar' => 'تصنيف جلسات البر',
                'description_en' => 'Outdoor seating category',
                'slug_en' => 'outdoor-seating',
                'slug_ar' => 'جلسات-بر',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}
