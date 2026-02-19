<?php

namespace App\Services\V1\Admin\City;

use App\Models\City;

class CityService
{
    public function list()
    {
        return City::with('shipments')->paginate(10);
    }

    public function getActive()
    {
        return City::active()->paginate(10);
    }

    public function listWithShipments()
    {
        return City::with(['shipments:id,cost,estimated_delivery_days'])->paginate(10);
    }

    public function store(array $data): City
    {
        $city = City::create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'is_active' => $data['is_active'] ?? true,
            'can_ship' => $data['can_ship'] ?? true,
        ]);
        $this->storeOrUpdateShipments($city, $data);
        return $city;
    }

    public function update(City $city, array $data): City
    {
        $city->update([
            'name_en' => $data['name_en'] ?? $city->name_en,
            'name_ar' => $data['name_ar'] ?? $city->name_ar,
            'is_active' => $data['is_active'] ?? $city->is_active,
            'can_ship' => $data['can_ship'] ?? $city->can_ship,
        ]);
        $this->storeOrUpdateShipments($city, $data);
        return $city->refresh();
    }

    public function delete(City $city): void
    {
        $city->shipments()->delete();
        $city->delete();
    }

    public function show(City $city): City
    {
        return $city->load('shipments');
    }

    private function storeOrUpdateShipments(City $city, array $data): void
    {
        if (isset($data['cost'])) {
            $city->shipments()->updateOrCreate(
                ['city_id' => $city->id],
                [
                    'cost' => $data['cost'],
                    'estimated_delivery_days' => $data['estimated_delivery_days'] ?? null,
                ]
            );
        }
    }
}
