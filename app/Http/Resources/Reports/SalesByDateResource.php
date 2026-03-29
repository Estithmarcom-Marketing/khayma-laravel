<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesByDateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'period'         => $this->resource['period'],
            'total_revenue'  => $this->resource['total_revenue'],
            'orders_count'   => $this->resource['orders_count'],
            'products_count' => $this->resource['products_count'],
        ];
    }
}
