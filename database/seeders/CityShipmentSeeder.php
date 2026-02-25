<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\CityShipment;
use Illuminate\Database\Seeder;

class CityShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shipments = [
            'Riyadh' => ['cost' => 15.00, 'estimated_delivery_days' => 1],
            'Jeddah' => ['cost' => 20.00, 'estimated_delivery_days' => 2],
            'Mecca' => ['cost' => 20.00, 'estimated_delivery_days' => 2],
            'Medina' => ['cost' => 20.00, 'estimated_delivery_days' => 2],
            'Dammam' => ['cost' => 25.00, 'estimated_delivery_days' => 2],
            'Taif' => ['cost' => 25.00, 'estimated_delivery_days' => 3],
            'Tabuk' => ['cost' => 35.00, 'estimated_delivery_days' => 4],
            'Buraydah' => ['cost' => 25.00, 'estimated_delivery_days' => 3],
            'Hafar Al‑Batin' => ['cost' => 30.00, 'estimated_delivery_days' => 3],
            'Khamis Mushait' => ['cost' => 30.00, 'estimated_delivery_days' => 3],
            'Khobar' => ['cost' => 25.00, 'estimated_delivery_days' => 2],
            'Abha' => ['cost' => 30.00, 'estimated_delivery_days' => 3],
            'Hail' => ['cost' => 30.00, 'estimated_delivery_days' => 3],
            'Najran' => ['cost' => 35.00, 'estimated_delivery_days' => 4],
            'Yanbu' => ['cost' => 25.00, 'estimated_delivery_days' => 3],
            'Jazan' => ['cost' => 35.00, 'estimated_delivery_days' => 4],
            'Qurayyat' => ['cost' => 40.00, 'estimated_delivery_days' => 5],
            'Dhahran' => ['cost' => 25.00, 'estimated_delivery_days' => 2],
            'Al‑Baha' => ['cost' => 30.00, 'estimated_delivery_days' => 3],
            'Al Kharj' => ['cost' => 20.00, 'estimated_delivery_days' => 2],
        ];

        $cities = City::whereIn('name_en', array_keys($shipments))->get()->keyBy('name_en');

        $records = [];

        foreach ($shipments as $cityName => $details) {
            if (! isset($cities[$cityName])) {
                continue;
            }

            $records[] = [
                'city_id' => $cities[$cityName]->id,
                'cost' => $details['cost'],
                'estimated_delivery_days' => $details['estimated_delivery_days'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        CityShipment::insert($records);
    }
}
