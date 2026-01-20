<?php

namespace App\Services\V1\Website\Address;

use App\Models\Address;
use App\Models\User;
use DB;

class AddressService
{
    public function list(User $user)
    {
        return $user->addresses()->with('city')->get();
    }

    public function store(User $user, array $data)
    {

        return DB::transaction(function () use ($user, $data) {
            $address = $user->addresses()->create([
                'city_id' => $data['city_id'],
                'name' => $data['name'],
                'value' => $data['value'],
                'is_default' => $data['is_default'] ?? false,
                'additional_info' => $data['additional_info'] ?? null,
            ]);
            $address->load('city');

            return $address;
        });
    }

    public function delete(Address $address)
    {
        return DB::transaction(function () use ($address) {
            $address->delete();
        });
    }

    public function show(Address $address)
    {
        return $address->load('city');
    }

    public function update(Address $address, array $data)
    {
        return DB::transaction(function () use ($address, $data) {
            $address->update([
                'city_id' => $data['city_id'] ?? $address->city_id,
                'name' => $data['name'] ?? $address->name,
                'value' => $data['value'] ?? $address->value,
                'is_default' => $data['is_default'] ?? $address->is_default,
                'additional_info' => $data['additional_info'] ?? $address->additional_info,
            ]);

            $address->load('city');

            return $address->refresh();
        });
    }
}
