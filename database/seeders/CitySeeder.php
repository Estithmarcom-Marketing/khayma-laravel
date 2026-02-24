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
            ['name_en' => 'Riyadh', 'name_ar' => 'الرياض', 'zip' => '11461', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Jeddah', 'name_ar' => 'جدة', 'zip' => '21577', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Mecca', 'name_ar' => 'مكة المكرمة', 'zip' => '21955', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Medina', 'name_ar' => 'المدينة المنورة', 'zip' => '42311', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Dammam', 'name_ar' => 'الدمام', 	'zip' => '31433', 	'is_active' => true, 	'can_ship' => true],
            ['name_en' => 'Taif', 	'name_ar' => 'الطائف', 'zip' => '26513', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Tabuk', 'name_ar' => 'تبوك', 'zip' => '71411', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Buraydah', 'name_ar' => 'بريدة', 'zip' => '51411', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Hafar Al‑Batin', 'name_ar' => 'حفر الباطن', 'zip' => '81521', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Khamis Mushait', 'name_ar' => 'خميس مشيط', 'zip' => '91521', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Khobar', 'name_ar' => 'الخبر', 	'zip' => '31952', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Abha', 	'name_ar' => 'أبها', 'zip' => '61411', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Hail', 'name_ar' => 'حائل', 'zip' => '55411', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Najran', 'name_ar' => 'نجران', 'zip' => '66251', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Yanbu', 'name_ar' => 'ينبع', 'zip' => '46455', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Jazan', 'name_ar' => 'جازان', 'zip' => '82711', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Qurayyat', 'name_ar' => 'القريات', 	'zip' => '53321', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Dhahran', 	'name_ar' => 'الظهران', 'zip' => '34232', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Al‑Baha', 	'name_ar' => 'الباحة', 'zip' => '65511', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Al Kharj', 	'name_ar' => 'الخرج', 'zip' => '16278', 'is_active' => true, 'can_ship' => true],
        ];
        City::insert($cities);
    }
}
