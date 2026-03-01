<?php

namespace App\Http\Resources\Dashboard\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'status' => $this->status->value,
            'transaction_id' => $this->transaction_id,
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'payment_gateway' => new PaymentGatewayResource($this->whenLoaded('paymentGateway')),
            // 'payment_response' => $this->payment_response,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
