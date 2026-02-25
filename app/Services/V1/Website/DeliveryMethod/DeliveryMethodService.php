<?php

namespace App\Services\V1\Website\DeliveryMethod;

use App\Models\DeliveryMethod;

class DeliveryMethodService
{
    public function list()
    {
        return DeliveryMethod::select('id', 'name_ar', 'name_en')
            ->active()
            ->get();
    }
}
