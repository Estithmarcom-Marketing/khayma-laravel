<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'is_published' => $this->is_published,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name_ar' => $this->category->name_ar,
                    'name_en' => $this->category->name_en,
                ];
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name_ar' => $this->brand->name_ar,
                    'name_en' => $this->brand->name_en,
                ];
            }),
            'variations' => $this->whenLoaded('variations', $this->variations),
            'meta_title_ar' => $this->meta_title_ar,
            'meta_title_en' => $this->meta_title_en,
            'meta_description_ar' => $this->meta_description_ar,
            'meta_description_en' => $this->meta_description_en,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),

        ];
    }
}
