<?php

namespace App\Http\Resources\Application\Brand;

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
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();

    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ar,
            'description' => $this->description_ar,
            'slug' => $this->slug_ar,
            'image' => $this->whenLoaded('media', $this->whenNotNull($this->getFirstMediaUrl('brand'))),

        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'description' => $this->description_en,
            'slug' => $this->slug_en,
            'image' => $this->whenLoaded('media', $this->whenNotNull($this->getFirstMediaUrl('brand'))),

        ];

    }
}
