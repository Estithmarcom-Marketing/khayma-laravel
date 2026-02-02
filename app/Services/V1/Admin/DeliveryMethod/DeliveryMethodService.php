<?php

namespace App\Services\V1\Admin\DeliveryMethod;

use App\Models\DeliveryMethod;

class DeliveryMethodService
{
    public function list()
    {
        return DeliveryMethod::all();
    }

    public function getActive()
    {
        return DeliveryMethod::active()->get();
    }

    public function store(array $data)
    {
        return DeliveryMethod::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? true,
            'has_shipping_cost' => $data['has_shipping_cost'] ?? true,
        ]);
    }

    public function update(DeliveryMethod $deliveryMethod, array $data)
    {
        $deliveryMethod->update([
            'name_ar' => $data['name_ar'] ?? $deliveryMethod->name_ar,
            'name_en' => $data['name_en'] ?? $deliveryMethod->name_en,
            'is_active' => $data['is_active'] ?? $deliveryMethod->is_active,
            'has_shipping_cost' => $data['has_shipping_cost'] ?? $deliveryMethod->has_shipping_cost,
        ]);

        return $deliveryMethod->refresh();
    }

    public function show(DeliveryMethod $deliveryMethod)
    {
        return $deliveryMethod;
    }

    public function delete(DeliveryMethod $deliveryMethod)
    {
        return $deliveryMethod->delete();
    }
}
