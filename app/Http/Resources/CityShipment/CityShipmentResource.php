<?php

namespace App\Http\Resources\CityShipment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cost' => $this->cost,
            'estimated_delivery_days' => $this->estimated_delivery_days,
            'city_id' => $this->city_id,
            'city_name_en' => $this->city->name_en,
            'city_name_ar' => $this->city->name_ar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
