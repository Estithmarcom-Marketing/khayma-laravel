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

        $sub_categories = [
            [
                'name_ar' => 'اقمشة داخلية',
                'name_en' => 'Indoor Fabrics',
                'description_ar' => 'تصنيف الاقمشة الداخلية',
                'description_en' => 'Indoor fabrics category',
                'slug_en' => 'indoor-fabrics',
                'slug_ar' => 'اقمشة-داخلية',
                'parent_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'اقمشة خارجية',
                'name_en' => 'Outdoor Fabrics',
                'description_ar' => 'تصنيف الاقمشة الخارجية',
                'description_en' => 'Outdoor fabrics category',
                'slug_en' => 'outdoor-fabrics',
                'slug_ar' => 'اقمشة-خارجية',
                'parent_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'خيام بر',
                'name_en' => 'Camping Tents',
                'description_ar' => 'تصنيف خيام البر',
                'description_en' => 'Camping tents category',
                'slug_en' => 'camping-tents',
                'slug_ar' => 'خيام-بر',
                'parent_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'خيام عائلية',
                'name_en' => 'Family Tents',
                'description_ar' => 'تصنيف الخيام العائلية',
                'description_en' => 'Family tents category',
                'slug_en' => 'family-tents',
                'slug_ar' => 'خيام-عائلية',
                'parent_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'جلسات بر فاخرة',
                'name_en' => 'Luxury Outdoor Seating',
                'description_ar' => 'تصنيف جلسات البر الفاخرة',
                'description_en' => 'Luxury outdoor seating category',
                'slug_en' => 'luxury-outdoor-seating',
                'slug_ar' => 'جلسات-بر-فاخرة',
                'parent_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'جلسات بر اقتصادية',
                'name_en' => 'Budget Outdoor Seating',
                'description_ar' => 'تصنيف جلسات البر الاقتصادية',
                'description_en' => 'Budget outdoor seating category',
                'slug_en' => 'budget-outdoor-seating',
                'slug_ar' => 'جلسات-بر-اقتصادية',
                'parent_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('categories')->insert($sub_categories);
    }
}
