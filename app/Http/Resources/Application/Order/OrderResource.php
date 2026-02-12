<?php

namespace App\Http\Resources\Application\Order;

use App\Http\Resources\Application\Address\AddressResource;
use App\Http\Resources\Application\Order\OrderItemResource;
use App\Http\Resources\Application\DeliveryMethod\DeliveryMethodResource;
use App\Http\Resources\Application\PaymentMethod\PaymentMethodResource;
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
            'subtotal' => $this->subtotal_price,
            'tax' => $this->tax_amount,
            'shipping' => $this->shipping_cost,
            'discount' => $this->discount_amount,
            'promo_code' => $this->promo_code,
            'total' => $this->total_price,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'address_details' => $this->address_details,
            'phone' => $this->phone,
            'delivery_method' => new DeliveryMethodResource($this->whenLoaded('deliveryMethod')),
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'created_at' => $this->created_at,
            'delivered_at' => $this->delivered_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
