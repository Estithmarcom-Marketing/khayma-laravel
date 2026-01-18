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
            ['name_en' => 'Cairo', 'name_ar' => 'القاهرة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Alexandria', 'name_ar' => 'الإسكندرية', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Giza', 'name_ar' => 'الجيزة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Port Said', 'name_ar' => 'بورسعيد', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Suez', 'name_ar' => 'السويس', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Luxor', 'name_ar' => 'الأقصر', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Aswan', 'name_ar' => 'أسوان', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Sharm El-Sheikh', 'name_ar' => 'شرم الشيخ', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Hurghada', 'name_ar' => 'الغردقة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Ismailia', 'name_ar' => 'الإسماعيلية', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Tanta', 'name_ar' => 'طنطا', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Mansoura', 'name_ar' => 'المنصورة', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Zagazig', 'name_ar' => 'الزقازيق', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Asyut', 'name_ar' => 'أسيوط', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Fayoum', 'name_ar' => 'الفيوم', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Beni Suef', 'name_ar' => 'بني سويف', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Minya', 'name_ar' => 'المنيا', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Sohag', 'name_ar' => 'سوهاج', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Qena', 'name_ar' => 'قنا', 'is_active' => true, 'can_ship' => true],
            ['name_en' => 'Damanhour', 'name_ar' => 'دمنهور', 'is_active' => true, 'can_ship' => true],
        ];

        City::insert($cities);
    }
}
