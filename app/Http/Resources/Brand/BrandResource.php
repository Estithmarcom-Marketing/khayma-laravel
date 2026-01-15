<?php

namespace App\Http\Resources\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'image' => $this->whenLoaded('media', $this->whenNotNull($this->getFirstMediaUrl('brand'))),
            'created_at' => $this->whenNotNull($this->created_at->toDateTimeString()),
            'updated_at' => $this->whenNotNull($this->updated_at->toDateTimeString()),
        ];
    }
}
