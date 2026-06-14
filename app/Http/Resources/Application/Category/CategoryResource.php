<?php

namespace App\Http\Resources\Application\Category;

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
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();
    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ar,
            'description' => $this->description_ar,

            'image' => $this->whenLoaded('media', $this->whenNotNull($this->getFirstMediaUrl('category'))),
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent ? [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name_ar,
                ] : null;
            }),

            'children' => $this->whenLoaded('subCategories', function () {
                return $this->subCategories->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name_ar,
                        'image' => $this->whenLoaded('media', $this->whenNotNull($child->getFirstMediaUrl('category'))),
                    ];
                });
            }),
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'description' => $this->description_en,
            'image' => $this->whenLoaded('media', $this->whenNotNull($this->getFirstMediaUrl('category'))),
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent ? [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name_en,
                ] : null;
            }),
            'children' => $this->whenLoaded('subCategories', function () {
                return $this->subCategories->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name_en,
                        'image' => $this->whenLoaded('media', $this->whenNotNull($child->getFirstMediaUrl('category'))),
                    ];
                });
            }),

        ];
    }
}
