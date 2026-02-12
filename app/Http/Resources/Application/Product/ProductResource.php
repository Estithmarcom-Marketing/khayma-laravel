<?php

namespace App\Http\Resources\Application\Product;

use App\Http\Resources\Application\ProductVariation\ProductVariationResource;
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
            'is_published' => $this->when(isset($this->is_published), fn () => (bool) $this->is_published),
            'is_favourite' => $this->when(isset($this->is_favourite), fn () => (bool) $this->is_favourite),
            'is_in_cart' => $this->when(isset($this->is_in_cart), fn () => (bool) $this->is_in_cart),
            'is_in_reminder' => $this->when(isset($this->is_in_reminder), fn () => (bool) $this->is_in_reminder),
            'order_count' => $this->when(isset($this->orders_count), fn () => (int) $this->orders_count),
            'price' => $this->when(isset($this->price), fn () => (float) $this->price),
            'offer' => $this->when(isset($this->offer), fn () => (float) $this->offer),
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
            'variations' => ProductVariationResource::collection($this->whenLoaded('productVariations')),
            'images' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'name' => $media->name,
                        'url' => $media->original_url,
                    ];
                });
            }),
            'rating' => $this->when($this->relationLoaded('reviews') || isset($this->reviews_avg_rating), function () {
                return [
                    'average' => round((float) ($this->reviews_avg_rating ?? 0), 1),
                    'count' => (int) ($this->reviews_count ?? 0),

                ];
            }, [
                'average' => 0,
                'count' => 0,
            ]),
            'meta_title_ar' => $this->meta_title_ar,
            'meta_title_en' => $this->meta_title_en,
            'meta_description_ar' => $this->meta_description_ar,
            'meta_description_en' => $this->meta_description_en,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
