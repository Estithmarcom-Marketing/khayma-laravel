<?php

namespace App\Services\V1\Website\Address;

use App\Models\Address;
use App\Models\User;

class AddressService
{
    public function list(User $user)
    {
        return $user->addresses()->with('city.shipments:id,city_id,cost,estimated_delivery_days')->get();
    }

    public function store(User $user, array $data)
    {
        $address = $user->addresses()->create([
            'city_id' => $data['city_id'],
            'name' => $data['name'],
            'value' => $data['value'],
            'is_default' => $data['is_default'] ?? false,
            'additional_info' => $data['additional_info'] ?? null,
        ]);

        return $address->load('city:id,name_en,name_ar');
    }

    public function delete(Address $address)
    {
        return $address->delete();
    }

    public function show(Address $address)
    {
        return $address->load('city.shipments:id,city_id,cost,estimated_delivery_days');
    }

    public function update(Address $address, array $data)
    {
        $address->update([
            'city_id' => $data['city_id'] ?? $address->city_id,
            'name' => $data['name'] ?? $address->name,
            'value' => $data['value'] ?? $address->value,
            'is_default' => $data['is_default'] ?? $address->is_default,
            'additional_info' => $data['additional_info'] ?? $address->additional_info,
        ]);

        return $address->load('city:id,name_en,name_ar');
    }
}
