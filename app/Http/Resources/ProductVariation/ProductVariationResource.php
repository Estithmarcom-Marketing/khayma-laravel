<?php

namespace App\Http\Resources\ProductVariation;

use App\Http\Resources\Property\PropertyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'is_active' => $this->is_active,
            'offer' => $this->offer,
            'offer_started_date' => $this->offer_started_date,
            'offer_expired_date' => $this->offer_expired_date,
            'color' => $this->whenLoaded('color', function () {
                return [
                    'id' => $this->color->id,
                    'name_en' => $this->color->name_en,
                    'name_ar' => $this->color->name_ar,
                    'code' => $this->color->code,
                ];
            }),
            'size' => $this->whenLoaded('size', function () {
                return [
                    'id' => $this->size->id,
                    'name_en' => $this->size->name_en,
                    'name_ar' => $this->size->name_ar,
                ];
            }),
            'properties' => PropertyResource::collection($this->whenLoaded('properties')),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }
}
