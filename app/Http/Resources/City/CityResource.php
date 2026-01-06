<?php

namespace App\Http\Resources\City;

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
            'is_active' => $this->is_active,
            'can_ship' => $this->can_ship,
            'created_at' => $this->whenNotNull($this->created_at?->toDateTimeString()),
            'updated_at' => $this->whenNotNull($this->updated_at?->toDateTimeString()),
        ];
    }
}
