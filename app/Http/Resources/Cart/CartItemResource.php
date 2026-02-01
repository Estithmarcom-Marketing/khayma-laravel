<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\ProductVariation\ProductVariationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'product_variation' => ProductVariationResource::make($this->productVariation),
            'product' => $this->productVariation ? ProductResource::make($this->productVariation->product) : null,
            'created_at' => optional($this->created_at),
            'updated_at' => optional($this->updated_at),
        ];
    }
}
