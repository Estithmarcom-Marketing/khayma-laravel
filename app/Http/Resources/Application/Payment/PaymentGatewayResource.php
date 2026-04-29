<?php

namespace App\Http\Resources\Application\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
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
            'gateway' => $this->gateway,
            'image' => $this->whenLoaded('media', fn() => $this->getFirstMediaUrl('payment_gateways'), null),
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'gateway' => $this->gateway,
            'image' => $this->whenLoaded('media', fn() => $this->getFirstMediaUrl('payment_gateways'), null),
        ];
    }
}
