<?php

namespace App\Http\Resources\PromoCode;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeResource extends JsonResource
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
            'code' => $this->code,
            'value' => $this->value,
            'is_percentage' => $this->is_percentage,
            'is_active' => $this->is_active,
            'usage_limit' => $this->usage_limit,
            'times_used' => $this->times_used,
            'expires_at' => $this->whenNotNull($this->expires_at->toDateTimeString()),
            'created_at' => $this->whenNotNull($this->created_at->toDateTimeString()),
            'updated_at' => $this->whenNotNull($this->updated_at->toDateTimeString()),
        ];
    }
}
