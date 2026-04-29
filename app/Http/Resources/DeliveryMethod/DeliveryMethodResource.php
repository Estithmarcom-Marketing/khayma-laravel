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
            'is_active' => $this->whenNotNull($this->is_active),
            'has_shipping_cost' => $this->whenNotNull($this->has_shipping_cost),
            'image' => $this->whenLoaded('media', fn() => $this->getFirstMediaUrl('delivery_methods'), null),
            'created_at' => $this->whenNotNull($this->created_at),
            'updated_at' => $this->whenNotNull($this->updated_at),
        ];
    }
}
