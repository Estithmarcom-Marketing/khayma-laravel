<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name_ar' => 'سامسونج',
                'name_en' => 'Samsung',
                'description_ar' => 'شركة رائدة في صناعة الإلكترونيات والأجهزة الذكية',
                'description_en' => 'Leading company in electronics and smart devices',
                'slug_ar' => 'سامسونج',
                'slug_en' => 'samsung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'آبل',
                'name_en' => 'Apple',
                'description_ar' => 'شركة متخصصة في الهواتف الذكية وأجهزة الكمبيوتر',
                'description_en' => 'Company specializing in smartphones and computers',
                'slug_ar' => 'آبل',
                'slug_en' => 'apple',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'سوني',
                'name_en' => 'Sony',
                'description_ar' => 'شركة يابانية للإلكترونيات والترفيه',
                'description_en' => 'Japanese electronics and entertainment company',
                'slug_ar' => 'سوني',
                'slug_en' => 'sony',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'إل جي',
                'name_en' => 'LG',
                'description_ar' => 'شركة كورية للإلكترونيات والأجهزة المنزلية',
                'description_en' => 'Korean electronics and home appliances company',
                'slug_ar' => 'إل جي',
                'slug_en' => 'lg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'هواوي',
                'name_en' => 'Huawei',
                'description_ar' => 'شركة عالمية في مجال الاتصالات والإلكترونيات',
                'description_en' => 'Global telecommunications and electronics company',
                'slug_ar' => 'هواوي',
                'slug_en' => 'huawei',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'خيمة',
                'name_en' => 'Khayma',
                'description_ar' => 'شركة رائدة في صناعة الخيم والجلسات البرية',
                'description_en' => 'Leading company in the manufacture of tents and outdoor seating',
                'slug_ar' => 'خيمة',
                'slug_en' => 'khayma',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Brand::insert($brands);
    }
}
