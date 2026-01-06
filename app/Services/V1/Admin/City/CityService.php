<?php

namespace App\Services\V1\Admin\City;

use App\Models\City;

class CityService
{
    public function list()
    {
        return City::paginate(10);
    }

    public function store(array $data): City
    {
        return City::create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'is_active' => $data['is_active'] ?? true,
            'can_ship' => $data['can_ship'] ?? true,
        ]);
    }

    public function update(City $city, array $data): City
    {
        $city->update([
            'name_en' => $data['name_en'] ?? $city->name_en,
            'name_ar' => $data['name_ar'] ?? $city->name_ar,
            'is_active' => $data['is_active'] ?? $city->is_active,
            'can_ship' => $data['can_ship'] ?? $city->can_ship,
        ]);

        return $city;
    }

    public function delete(City $city): void
    {
        $city->delete();
    }

    public function show(City $city): City
    {
        return $city;
    }
}
