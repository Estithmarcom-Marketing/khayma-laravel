<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesByPaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name_ar'        => $this->name_ar,
            'name_en'        => $this->name_en,
            'type'           => $this->type,
            'total_revenue'  => (float) $this->total_revenue,
            'orders_count'   => (int) $this->orders_count,
            'products_count' => (int) $this->products_count,
        ];
    }
}
