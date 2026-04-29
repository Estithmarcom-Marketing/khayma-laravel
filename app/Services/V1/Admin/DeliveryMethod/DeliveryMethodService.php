<?php

namespace App\Services\V1\Admin\DeliveryMethod;

use App\Models\DeliveryMethod;

class DeliveryMethodService
{
    public function list()
    {
        return DeliveryMethod::with('media')->get();
    }

    public function getActive()
    {
        return DeliveryMethod::with('media')->active()->get();
    }

    public function store(array $data)
    {
        $deliveryMethod = DeliveryMethod::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'is_active' => $data['is_active'] ?? true,
            'has_shipping_cost' => $data['has_shipping_cost'] ?? true,
        ]);
        if (isset($data['image'])) {
            $deliveryMethod->addMedia($data['image'])->toMediaCollection('delivery_methods');
        }

        return $deliveryMethod;
    }

    public function update(DeliveryMethod $deliveryMethod, array $data)
    {
        $deliveryMethod->update([
            'name_ar' => $data['name_ar'] ?? $deliveryMethod->name_ar,
            'name_en' => $data['name_en'] ?? $deliveryMethod->name_en,
            'is_active' => $data['is_active'] ?? $deliveryMethod->is_active,
            'has_shipping_cost' => $data['has_shipping_cost'] ?? $deliveryMethod->has_shipping_cost,
        ]);
        if (isset($data['image'])) {
            $deliveryMethod->clearMediaCollection('delivery_methods');
            $deliveryMethod->addMedia($data['image'])->toMediaCollection('delivery_methods');
        }

        return $deliveryMethod->refresh();
    }

    public function show(DeliveryMethod $deliveryMethod)
    {
        return $deliveryMethod->load('media');
    }

    public function delete(DeliveryMethod $deliveryMethod)
    {
        $deliveryMethod->clearMediaCollection('delivery_methods');
        return $deliveryMethod->delete();
    }
}
