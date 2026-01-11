<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [

            [
                'name_ar' => 'هاتف سامسونج جالاكسي S23',
                'name_en' => 'Samsung Galaxy S23',
                'description_ar' => 'هاتف ذكي متطور بكاميرا عالية الأداء',
                'description_en' => 'Advanced smartphone with high-performance camera',
                'slug_ar' => 'هاتف-سامسونج-جالاكسي-s23',
                'slug_en' => 'samsung-galaxy-s23',
                'category_id' => 2,
                'brand_id' => 2,
                'meta_title_ar' => 'سامسونج جالاكسي S23',
                'meta_title_en' => 'Samsung Galaxy S23',
                'meta_description_ar' => 'اشتري هاتف سامسونج جالاكسي S23 بأفضل سعر',
                'meta_description_en' => 'Buy Samsung Galaxy S23 at the best price',
                'is_published' => true,
                 'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'آيفون 14 برو',
                'name_en' => 'iPhone 14 Pro',
                'description_ar' => 'هاتف آيفون بأداء قوي وكاميرا احترافية',
                'description_en' => 'Powerful iPhone with professional camera',
                'slug_ar' => 'ايفون-14-برو',
                'slug_en' => 'iphone-14-pro',
                'category_id' => 2,
                'brand_id' => 3,
                'meta_title_ar' => 'آيفون 14 برو',
                'meta_title_en' => 'iPhone 14 Pro',
                'meta_description_ar' => 'هاتف آيفون 14 برو بأحدث التقنيات',
                'meta_description_en' => 'iPhone 14 Pro with latest technology',
                'is_published' => true,
                 'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name_ar' => 'لابتوب ديل XPS 15',
                'name_en' => 'Dell XPS 15 Laptop',
                'description_ar' => 'لابتوب احترافي بشاشة عالية الدقة وأداء قوي',
                'description_en' => 'Professional laptop with high-resolution display',
                'slug_ar' => 'لابتوب-ديل-xps-15',
                'slug_en' => 'dell-xps-15',
                'category_id' => 3,
                'brand_id' => 6,
                'meta_title_ar' => 'ديل XPS 15',
                'meta_title_en' => 'Dell XPS 15',
                'meta_description_ar' => 'لابتوب ديل XPS 15 للأعمال والاحتراف',
                'meta_description_en' => 'Dell XPS 15 laptop for professionals',
                'is_published' => true,
                 'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'ماك بوك برو M2',
                'name_en' => 'MacBook Pro M2',
                'description_ar' => 'لابتوب آبل بمعالج M2 وأداء فائق',
                'description_en' => 'Apple laptop with M2 chip and superior performance',
                'slug_ar' => 'ماك-بوك-برو-m2',
                'slug_en' => 'macbook-pro-m2',
                'category_id' => 3,
                'brand_id' => 3,
                'meta_title_ar' => 'ماك بوك برو M2',
                'meta_title_en' => 'MacBook Pro M2',
                'meta_description_ar' => 'ماك بوك برو M2 للأعمال الإبداعية',
                'meta_description_en' => 'MacBook Pro M2 for creative professionals',
                'is_published' => true,
                 'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name_ar' => 'سماعات سوني WH-1000XM5',
                'name_en' => 'Sony WH-1000XM5 Headphones',
                'description_ar' => 'سماعات عزل ضوضاء بجودة صوت عالية',
                'description_en' => 'Noise-cancelling headphones with premium sound',
                'slug_ar' => 'سماعات-سوني-wh-1000xm5',
                'slug_en' => 'sony-wh-1000xm5',
                'category_id' => 1,
                'brand_id' => 4,
                'meta_title_ar' => 'سماعات سوني WH-1000XM5',
                'meta_title_en' => 'Sony WH-1000XM5',
                'meta_description_ar' => 'أفضل سماعات عزل ضوضاء من سوني',
                'meta_description_en' => 'Best noise-cancelling headphones from Sony',
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
               
            ],
        ];
        Product::insert($products);

    }
}
