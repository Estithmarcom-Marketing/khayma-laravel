<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\DeliveryMethod\DeliveryMethodResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'status' => $this->status->value,
            'prices' => [
                'subtotal' => $this->subtotal_price,
                'tax' => $this->tax_amount,
                'shipping' => $this->shipping_cost,
                'discount' => $this->discount_amount,
                'total' => $this->total_price,
            ],
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'delivery_method' => new DeliveryMethodResource($this->whenLoaded('deliveryMethod')),
            'created_at' => $this->created_at,
        ];
    }
}
