<?php

namespace App\Http\Resources\Application\ProductReminders;

use App\Http\Resources\Application\Product\ProductResource;
use App\Http\Resources\Application\ProductVariation\ProductVariationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductRemindersResource extends JsonResource
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
            'product_variation' => $this->whenLoaded('productVariation', ProductVariationResource::make($this->productVariation)),
            'product' => $this->productVariation ? ProductResource::make($this->productVariation->product) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
