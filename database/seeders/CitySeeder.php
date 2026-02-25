<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name_en' => 'Riyadh', 'name_ar' => 'الرياض', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Jeddah', 'name_ar' => 'جدة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Mecca', 'name_ar' => 'مكة المكرمة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Medina', 'name_ar' => 'المدينة المنورة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Dammam', 'name_ar' => 'الدمام', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Taif', 'name_ar' => 'الطائف', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Tabuk', 'name_ar' => 'تبوك', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Buraydah', 'name_ar' => 'بريدة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Hafar Al‑Batin', 'name_ar' => 'حفر الباطن', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Khamis Mushait', 'name_ar' => 'خميس مشيط', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Khobar', 'name_ar' => 'الخبر', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Abha', 'name_ar' => 'أبها', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Hail', 'name_ar' => 'حائل', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Najran', 'name_ar' => 'نجران', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Yanbu', 'name_ar' => 'ينبع', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Jazan', 'name_ar' => 'جازان', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Qurayyat', 'name_ar' => 'القريات', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Dhahran', 'name_ar' => 'الظهران', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Al‑Baha', 'name_ar' => 'الباحة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Al Kharj', 'name_ar' => 'الخرج', 'is_active' => true, 'can_ship' => true],
        ];
        City::insert($cities);
    }
}
