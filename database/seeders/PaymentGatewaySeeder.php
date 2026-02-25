<?php

namespace Database\Seeders;

use App\Enums\Payments\PaymentMethodTypeEnum;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $onlineId = PaymentMethod::where('type', PaymentMethodTypeEnum::ONLINE)->value('id');
        $installmentId = PaymentMethod::where('type', PaymentMethodTypeEnum::INSTALLMENT)->value('id');


        $paymentGateways = [
            [
                'name_ar' => 'تابي',
                'name_en' => 'Tabby',
                'gateway' => 'tabby',
                'payment_method_id' => $installmentId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'تمارا',
                'name_en' => 'Tamara',
                'gateway' => 'tamara',
                'payment_method_id' => $installmentId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => ' ماي فاتورة',
                'name_en' => 'My Fatoorah',
                'gateway' => 'myfatoorah',
                'payment_method_id' => $onlineId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
           
        ];
       PaymentGateway::insert($paymentGateways);
    }
}
