<?php

namespace App\Services\V1\Website\PaymentMethod;

use App\Models\PaymentMethod;

class PaymentMethodService
{
    public function list()
    {
        return PaymentMethod::select('id', 'name_ar', 'name_en')
            ->active()
            ->get();
    }
}
