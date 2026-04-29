<?php

namespace App\Http\Resources\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
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
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
            'is_active' => when($this->is_active, true),
            'image' => $this->whenLoaded('media', fn() => $this->getFirstMediaUrl('payment_methods'), null),
            'payment_gateway' => $this->whenLoaded('paymentGateways', fn () => PaymentGatewayResource::collection($this->paymentGateways)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
