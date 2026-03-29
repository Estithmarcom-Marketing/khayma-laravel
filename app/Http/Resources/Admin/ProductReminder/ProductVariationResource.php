<?php

namespace App\Http\Resources\Admin\ProductReminder;

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
            'id'              => $this->id,
            'sku'             => $this->sku,
            'stock_quantity'  => $this->stock_quantity,
            'reminders_count' => $this->reminders_count,
            'product'         => $this->whenLoaded('product', fn () => [
                'id'      => $this->product->id,
                'name_ar' => $this->product->name_ar,
            ]),
            'color'           => $this->whenLoaded('color', fn () => [
                'id'      => $this->color->id,
                'name_ar' => $this->color->name_ar,
            ]),
            'size'            => $this->whenLoaded('size', fn () => [
                'id'      => $this->size->id,
                'name_ar' => $this->size->name_ar,
            ]),
        ];
    }
}