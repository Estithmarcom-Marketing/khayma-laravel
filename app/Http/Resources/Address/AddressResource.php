<?php

namespace App\Http\Resources\Address;

use App\Http\Resources\City\CityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'name' => $this->name,
            'value' => $this->value,
            'city' => $this->whenLoaded('city', CityResource::make($this->city)),
            'is_default' => $this->is_default,
            'additional_info' => $this->additional_info,
            'created_at' => $this->whenNotNull($this->created_at?->toDateTimeString()),
            'updated_at' => $this->whenNotNull($this->updated_at?->toDateTimeString()),
        ];
    }
}
