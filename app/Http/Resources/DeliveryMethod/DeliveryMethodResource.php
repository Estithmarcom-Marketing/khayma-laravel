<?php

namespace App\Http\Resources\DeliveryMethod;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryMethodResource extends JsonResource
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'is_active' => $this->is_active,
            'created_at' => $this->whenNotNull($this->created_at?->toDateTimeString()),
            'updated_at' => $this->whenNotNull($this->updated_at?->toDateTimeString()),
        ];
    }
}
