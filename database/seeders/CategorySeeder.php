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
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'خيام',
                'name_en' => 'Tents',
                'description_ar' => 'تصنيف الخيام',
                'description_en' => 'Tents category',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'جلسات بر',
                'name_en' => 'Outdoor Seating',
                'description_ar' => 'تصنيف جلسات البر',
                'description_en' => 'Outdoor seating category',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'أغطية وملحقات',
                'name_en' => 'Covers and Accessories',
                'description_ar' => 'تصنيف الأغطية والملحقات',
                'description_en' => 'Covers and accessories category',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'إضاءة خارجية',
                'name_en' => 'Outdoor Lighting',
                'description_ar' => 'تصنيف الإضاءة الخارجية',
                'description_en' => 'Outdoor lighting category',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'أدوات التخييم',
                'name_en' => 'Camping Tools',
                'description_ar' => 'تصنيف أدوات التخييم',
                'description_en' => 'Camping tools category',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('categories')->upsert($categories, ['name_ar', 'name_en'], ['name_ar', 'name_en', 'description_ar', 'description_en', 'created_at', 'updated_at']);

        $sub_categories = [
            [
                'name_ar' => 'اقمشة داخلية',
                'name_en' => 'Indoor Fabrics',
                'description_ar' => 'تصنيف الاقمشة الداخلية',
                'description_en' => 'Indoor fabrics category',
                'parent_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'اقمشة خارجية',
                'name_en' => 'Outdoor Fabrics',
                'description_ar' => 'تصنيف الاقمشة الخارجية',
                'description_en' => 'Outdoor fabrics category',
                'parent_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'اقمشة قطنية',
                'name_en' => 'Cotton Fabrics',
                'description_ar' => 'تصنيف الاقمشة القطنية',
                'description_en' => 'Cotton fabrics category',
                'parent_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'خيام بر',
                'name_en' => 'Camping Tents',
                'description_ar' => 'تصنيف خيام البر',
                'description_en' => 'Camping tents category',
                'parent_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'خيام عائلية',
                'name_en' => 'Family Tents',
                'description_ar' => 'تصنيف الخيام العائلية',
                'description_en' => 'Family tents category',
                'parent_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'خيام للأطفال',
                'name_en' => 'Kids Tents',
                'description_ar' => 'تصنيف خيام الأطفال',
                'description_en' => 'Kids tents category',
                'parent_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'جلسات بر فاخرة',
                'name_en' => 'Luxury Outdoor Seating',
                'description_ar' => 'تصنيف جلسات البر الفاخرة',
                'description_en' => 'Luxury outdoor seating category',
                'parent_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'جلسات بر اقتصادية',
                'name_en' => 'Budget Outdoor Seating',
                'description_ar' => 'تصنيف جلسات البر الاقتصادية',
                'description_en' => 'Budget outdoor seating category',
                'parent_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'أغطية الخيام',
                'name_en' => 'Tent Covers',
                'description_ar' => 'تصنيف أغطية الخيام',
                'description_en' => 'Tent covers category',
                'parent_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'ملحقات الخيام',
                'name_en' => 'Tent Accessories',
                'description_ar' => 'تصنيف ملحقات الخيام',
                'description_en' => 'Tent accessories category',
                'parent_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'مصابيح LED',
                'name_en' => 'LED Lights',
                'description_ar' => 'تصنيف مصابيح LED الخارجية',
                'description_en' => 'Outdoor LED lights category',
                'parent_id' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'فوانيس وشموع',
                'name_en' => 'Lanterns and Candles',
                'description_ar' => 'تصنيف الفوانيس والشموع',
                'description_en' => 'Lanterns and candles category',
                'parent_id' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'أدوات الطهي',
                'name_en' => 'Cooking Tools',
                'description_ar' => 'تصنيف أدوات الطهي الخارجية',
                'description_en' => 'Outdoor cooking tools category',
                'parent_id' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name_ar' => 'أكياس النوم',
                'name_en' => 'Sleeping Bags',
                'description_ar' => 'تصنيف أكياس النوم',
                'description_en' => 'Sleeping bags category',
                'parent_id' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        DB::table('categories')->upsert($sub_categories, ['name_ar', 'name_en'], ['name_ar', 'name_en', 'description_ar', 'description_en', 'parent_id', 'created_at', 'updated_at']);
    }
}
