<?php

namespace App\Http\Resources\Application\Payment;

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
        return app()->isLocale('ar') ? $this->arabicResource() : $this->englishResource();

    }

    private function arabicResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_ar,
            'type' => $this->type,
            'image' => $this->whenLoaded('media', fn() => $this->getFirstMediaUrl('payment_methods'), null),
            'payment_gateway' => $this->whenLoaded('paymentGateways', fn () => PaymentGatewayResource::collection($this->paymentGateways)),
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'type' => $this->type,
            'image' => $this->whenLoaded('media', fn() => $this->getFirstMediaUrl('payment_methods'), null),
            'payment_gateway' => $this->whenLoaded('paymentGateways', fn () => PaymentGatewayResource::collection($this->paymentGateways)),
        ];
    }
}
