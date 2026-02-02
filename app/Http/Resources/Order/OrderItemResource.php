<?php

namespace App\Http\Resources\Order;

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
            'price' => $this->productVariation->price,
            'offer' => $this->productVariation->offer,
            'total' => ($this->productVariation->price - $this->productVariation->offer) * $this->quantity,
            'product_variation_id' => $this->productVariation->id,
            'product' => [
                'name_en' => $this->productVariation->product->name_en,
                'name_ar' => $this->productVariation->product->name_ar,
                'slug_en' => $this->productVariation->product->slug_en,
                'slug_ar' => $this->productVariation->product->slug_ar,
            ],
        ];
    }
}
