<?php

namespace App\Http\Resources\Application\City;

use App\Http\Resources\Application\CityShipment\CityShipmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'is_active' => $this->whenNotNull($this->is_active),
            'can_ship' => $this->whenNotNull($this->can_ship),
            'shipments' => $this->whenLoaded('shipments', CityShipmentResource::collection($this->shipments)
            ),
            'created_at' => $this->whenNotNull($this->created_at),
            'updated_at' => $this->whenNotNull($this->updated_at),
        ];
    }
}
