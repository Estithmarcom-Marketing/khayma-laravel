<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total'             => $this->resource['total'],
            'percentage_change' => $this->resource['percentage_change'],
            'trend'             => $this->resource['trend'],
        ];
    }
}
