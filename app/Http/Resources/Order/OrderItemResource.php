<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Color\ColorResource;
use App\Http\Resources\Size\SizeResource;
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
 
        return [

            'quantity' => $this->quantity,
            'price' => (float) $this->productVariation->price,
            'offer' => (float) $this->productVariation->offer,
            'total' =>   (float) (($this->productVariation->price - $this->productVariation->offer) * $this->quantity),     
            'product_variation_id' => $this->productVariation->id,
            'color' =>$this->productVariation->relationLoaded('color') ? new ColorResource($this->productVariation->color) : null,
            'size' => $this->productVariation->relationLoaded('size') ? new SizeResource($this->productVariation->size) : null,
            'product' => [
                'name_en' => $this->productVariation->product->name_en,
                'name_ar' => $this->productVariation->product->name_ar,
                'slug_en' => $this->productVariation->product->slug_en,
                'slug_ar' => $this->productVariation->product->slug_ar,
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
