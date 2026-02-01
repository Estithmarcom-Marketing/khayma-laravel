<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name_ar' => ' كاش عند التوصيل',
                'name_en' => 'Cash on Delivery',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'أونلاين',
                'name_en' => 'Online',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        PaymentMethod::insert($paymentMethods);

    }
}
