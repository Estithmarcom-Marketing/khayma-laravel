<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesChartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'month' => $this->month,
            'sales' => $this->sales,
        ];
    }
}
