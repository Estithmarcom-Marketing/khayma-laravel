<?php

namespace App\Http\Resources\Application\ProductVariation;

use App\Http\Resources\Application\Property\PropertyResource;
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
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();

    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'is_active' => $this->is_active,
            'is_in_reminder' => (bool) $this->is_in_reminder,
            'price' => (float) $this->price,
            'offer' => (float) $this->offer,
            'offer_started_date' => $this->offer_started_date,
            'offer_expired_date' => $this->offer_expired_date,
            'color' => $this->whenLoaded('color', function () {
                return [
                    'id' => $this->color->id,
                    'name' => $this->color->name_ar,
                    'code' => $this->color->code,
                ];
            }),
            'size' => $this->whenLoaded('size', function () {
                return [
                    'id' => $this->size->id,
                    'name' => $this->size->name_ar,
                ];
            }),
            'properties' => PropertyResource::collection($this->whenLoaded('properties')),

        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'stock_quantity' => $this->stock_quantity,
            'is_active' => $this->is_active,
            'is_in_reminder' => (bool) $this->is_in_reminder,
            'price' => (float) $this->price,
            'offer' => (float) $this->offer,
            'offer_started_date' => $this->offer_started_date,
            'offer_expired_date' => $this->offer_expired_date,
            'color' => $this->whenLoaded('color', function () {
                return [
                    'id' => $this->color->id,
                    'name' => $this->color->name_en,
                    'code' => $this->color->code,
                ];
            }),
            'size' => $this->whenLoaded('size', function () {
                return [
                    'id' => $this->size->id,
                    'name' => $this->size->name_en,
                ];
            }),
            'properties' => PropertyResource::collection($this->whenLoaded('properties')),

        ];
    }
}
