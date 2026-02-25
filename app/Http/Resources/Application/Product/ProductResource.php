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
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();

    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ar,
            'description' => $this->description_ar,
            'slug' => $this->slug_ar,
            'is_published' => $this->when(isset($this->is_published), fn () => (bool) $this->is_published),
            'is_favourite' => $this->when(isset($this->is_favourite), fn () => (bool) $this->is_favourite),
            'is_in_cart' => $this->when(isset($this->is_in_cart), fn () => (bool) $this->is_in_cart),
            'order_count' => $this->when(isset($this->orders_count), fn () => (int) $this->orders_count),
            'price' => $this->when(isset($this->price), fn () => (float) $this->price),
            'offer' => $this->when(isset($this->offer), fn () => (float) $this->offer),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name_ar,
                ];
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name_ar,
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
            'meta_title' => $this->meta_title_ar,
            'meta_description' => $this->meta_description_ar,
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'description' => $this->description_en,
            'slug' => $this->slug_en,
            'is_published' => $this->when(isset($this->is_published), fn () => (bool) $this->is_published),
            'is_favourite' => $this->when(isset($this->is_favourite), fn () => (bool) $this->is_favourite),
            'is_in_cart' => $this->when(isset($this->is_in_cart), fn () => (bool) $this->is_in_cart),
            'order_count' => $this->when(isset($this->orders_count), fn () => (int) $this->orders_count),
            'price' => $this->when(isset($this->price), fn () => (float) $this->price),
            'offer' => $this->when(isset($this->offer), fn () => (float) $this->offer),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name_en,
                ];
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name_en,
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
            'meta_title' => $this->meta_title_en,
            'meta_description' => $this->meta_description_en,
        ];
    }
}
