<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        
        $fabrics = Category::where('slug_en', 'fabrics')->first();
        $tents = Category::where('slug_en', 'tents')->first();
        $seating = Category::where('slug_en', 'outdoor-seating')->first();
        $khayma_Brand = Brand::where('slug_en', 'khayma')->first();

        $products = [
            [
                'name_ar' => 'قماش مقاوم للماء',
                'name_en' => 'Waterproof Fabric',
                'description_ar' => 'قماش عالي الجودة مقاوم للماء والعوامل الجوية',
                'description_en' => 'High quality fabric resistant to water and weather',
                'slug_ar' => 'قماش-مقاوم-للماء',
                'slug_en' => 'waterproof-fabric',
                'category_id' => $fabrics->id,
                'brand_id' => $khayma_Brand->id,
                'is_published' => true,
                'meta_title_ar' => 'قماش مقاوم للماء للبيع',
                'meta_title_en' => 'Waterproof Fabric for Sale',
                'meta_description_ar' => 'أفضل قماش مقاوم للماء مناسب للاستخدام الخارجي',
                'meta_description_en' => 'Best waterproof fabric suitable for outdoor use',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name_ar' => 'خيمة بر عائلية',
                'name_en' => 'Family Tent',
                'description_ar' => 'خيمة بر واسعة مناسبة للعائلات والرحلات',
                'description_en' => 'Spacious family tent for camping and trips',
                'slug_ar' => 'خيمة-بر-عائلية',
                'slug_en' => 'family-tent',
                'category_id' => $tents->id,
                'brand_id' => $khayma_Brand->id,
                'is_published' => true,
                'meta_title_ar' => 'خيمة بر عائلية فاخرة',
                'meta_title_en' => 'Luxury Family Tent',
                'meta_description_ar' => 'خيمة قوية تتحمل الظروف الصحراوية',
                'meta_description_en' => 'Durable tent that withstands desert conditions',
                'created_at' => $now,
                'updated_at' => $now,
            ],

            [
                'name_ar' => 'جلسة بر فاخرة',
                'name_en' => 'Luxury Outdoor Seating',
                'description_ar' => 'جلسة بر مريحة بتصميم عصري',
                'description_en' => 'Comfortable outdoor seating with modern design',
                'slug_ar' => 'جلسة-بر-فاخرة',
                'slug_en' => 'luxury-outdoor-seating',
                'category_id' => $seating->id,
                'brand_id' => $khayma_Brand->id,
                'is_published' => true,
                'meta_title_ar' => 'جلسات بر فاخرة',
                'meta_title_en' => 'Luxury Outdoor Seating',
                'meta_description_ar' => 'أفضل جلسات بر للرحلات والاستجمام',
                'meta_description_en' => 'Best outdoor seating for camping and relaxation',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        Product::insert($products);

    }
}
