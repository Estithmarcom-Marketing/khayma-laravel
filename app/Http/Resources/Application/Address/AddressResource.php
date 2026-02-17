<?php

namespace App\Http\Resources\Application\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Application\City\CityResource;


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
            'city' => $this->whenLoaded('city',fn()=> CityResource::make($this->city)),
            'is_default' => (bool) $this->is_default,
            'additional_info' => $this->additional_info,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
