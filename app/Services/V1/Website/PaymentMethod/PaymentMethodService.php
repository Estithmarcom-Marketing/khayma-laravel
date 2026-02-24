<?php

namespace App\Services\V1\Website\PaymentMethod;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    public function list()
    {
        return PaymentMethod::select('id', 'name_ar', 'name_en', 'type')
            ->with(['paymentGateways' => function ($q) {
                $q->select('id', 'name_ar', 'name_en', 'gateway', 'payment_method_id')
                    ->active();
            }])
            ->active()
            ->get();
    }
}
