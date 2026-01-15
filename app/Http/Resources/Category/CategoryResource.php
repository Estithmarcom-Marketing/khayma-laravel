<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'slug_en' => $this->slug_en,
            'slug_ar' => $this->slug_ar,
            'description_en' => $this->description_en,
            'description_ar' => $this->description_ar,
            'image' => $this->whenLoaded('media', $this->whenNotNull($this->getFirstMediaUrl('category'))),
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent ? [
                    'id' => $this->parent->id,
                    'name_en' => $this->parent->name_en,
                    'name_ar' => $this->parent->name_ar,
                ] : null;
            }),

            'children' => $this->whenLoaded('subCategories', function () {
                return $this->children ? [
                    'id' => $this->children->id,
                    'name_en' => $this->children->name_en,
                    'name_ar' => $this->children->name_ar,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
