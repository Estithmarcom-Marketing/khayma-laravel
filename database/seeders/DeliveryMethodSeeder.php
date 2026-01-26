<?php

namespace Database\Seeders;

use App\Models\DeliveryMethod;
use Illuminate\Database\Seeder;

class DeliveryMethodSeeder extends Seeder
{
    public function run()
    {
        $deliveryMethods = [
            [
                'name_en' => 'Standard Shipping',
                'name_ar' => 'الشحن القياسي',
                'is_active' => 1,
            ],
            [
                'name_en' => 'Express Shipping',
                'name_ar' => 'الشحن السريع',
                'is_active' => 1,
            ],
            [
                'name_en' => 'Next-Day Delivery',
                'name_ar' => 'التوصيل في اليوم التالي',
                'is_active' => 1,
            ],
            [
                'name_en' => 'In-Store Pickup',
                'name_ar' => 'استلام من المتجر',
                'is_active' => 1,
            ],

        ];
        DeliveryMethod::insert($deliveryMethods);
    }
}
