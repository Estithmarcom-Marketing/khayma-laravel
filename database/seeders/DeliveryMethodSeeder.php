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
                'has_shipping_cost' => true,
            ],
            [
                'name_en' => 'In-Store Pickup',
                'name_ar' => 'استلام من المتجر',
                'is_active' => 1,
                'has_shipping_cost' => false,
            ],

        ];
        DeliveryMethod::insert($deliveryMethods);
    }
}
