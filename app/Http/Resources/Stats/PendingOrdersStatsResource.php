<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendingOrdersStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total' => $this->resource,
        ];
    }
}
