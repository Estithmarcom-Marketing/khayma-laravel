<?php

namespace App\Services\V1\Admin\City;

use App\Models\City;
use App\Models\CityShipment;

class CityShipmentService
{
    public function list()
    {
        return CityShipment::with('city')->paginate(10);
    }

    public function show(CityShipment $cityShipment)
    {
        return $cityShipment->load('city');
    }

    public function store(City $city, array $data)
    {
        return $city->shipments()->create([
            'cost' => $data['cost'],
            'estimated_delivery_days' => $data['estimated_delivery_days'],
        ]);
    }

    public function update(CityShipment $cityShipment, array $data)
    {
        $cityShipment->update([
            'cost' => $data['cost']??$cityShipment->cost,
            'estimated_delivery_days' => $data['estimated_delivery_days']??$cityShipment->estimated_delivery_days,
        ]);

        return $cityShipment->refresh();
    }

    public function delete(CityShipment $cityShipment)
    {
        return $cityShipment->delete();
    }
}
