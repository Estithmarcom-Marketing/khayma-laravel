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
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();

    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ar,
            'shipments' => $this->whenLoaded('shipments', fn () => CityShipmentResource::collection($this->shipments)),
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'shipments' => $this->whenLoaded('shipments', fn () => CityShipmentResource::collection($this->shipments)),
        ];
    }
}
