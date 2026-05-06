<?php

namespace App\Http\Resources\Application\Order;

use App\Http\Resources\Application\Color\ColorResource;
use App\Http\Resources\Application\Size\SizeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'price' => $this->productVariation->price,
            'offer' => $this->productVariation->offer,
            'tax' => $this->productVariation->tax,
            'total' => ($this->productVariation->price - $this->productVariation->offer) * $this->quantity,
            'product_variation_id' => $this->productVariation->id,
            'color' => $this->productVariation->relationLoaded('color') ? new ColorResource($this->productVariation->color) : null,
            'size' => $this->productVariation->relationLoaded('size') ? new SizeResource($this->productVariation->size) : null,
            'product' => [
                'name' => $this->productVariation->product->name_ar,
                'slug' => $this->productVariation->product->slug_ar,
                'images' => $this->productVariation->product->media->map(function ($media) {
                    return [
                        'name' => $media->name,
                        'url' => $media->original_url,
                    ];
                })->whenNotEmpty(fn ($collection) => $collection->values()),
            ],
        ];
    }

    private function englishResource()
    {
        return [
            'quantity' => $this->quantity,
            'price' => $this->productVariation->price,
            'offer' => $this->productVariation->offer,
            'tax' => $this->productVariation->tax,
            'total' => ($this->productVariation->price - $this->productVariation->offer) * $this->quantity,
            'product_variation_id' => $this->productVariation->id,
            'color' => $this->productVariation->relationLoaded('color') ? new ColorResource($this->productVariation->color) : null,
            'size' => $this->productVariation->relationLoaded('size') ? new SizeResource($this->productVariation->size) : null,
            'product' => [
                'name' => $this->productVariation->product->name_en,
                'slug' => $this->productVariation->product->slug_en,
                'images' => $this->productVariation->product->media->map(function ($media) {
                    return [
                        'name' => $media->name,
                        'url' => $media->original_url,
                    ];
                })->whenNotEmpty(fn ($collection) => $collection->values()),
            ],
        ];
    }
}
