<?php

namespace App\Http\Resources\Dashboard\Order;

use App\Http\Resources\Address\AddressResource;
use App\Http\Resources\Dashboard\Payment\PaymentMethodResource;
use App\Http\Resources\Dashboard\Payment\PaymentResource;
use App\Http\Resources\Dashboard\User\UserResource;
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
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal_price,
            'tax' => (float) $this->tax_amount,
            'shipping' => (float)$this->shipping_cost,
            'discount_of_offer' => (float)$this->discount_of_offer,
            'discount_of_promo_code' => (float) $this->discount_of_promo_code,
            'total_discount' => (float)$this->discount_of_offer + $this->discount_of_promo_code,
            'promo_code' => $this->promo_code,
            'total' => (float) $this->total_price,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'address_details' => $this->address_details,
            'phone' => $this->phone,
            'delivery_method' => new DeliveryMethodResource($this->whenLoaded('deliveryMethod')),
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'customer' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'delivered_at' => $this->delivered_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
