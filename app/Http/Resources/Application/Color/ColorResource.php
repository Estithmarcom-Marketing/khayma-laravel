<?php

namespace App\Http\Resources\Application\Color;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorResource extends JsonResource
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
            'code' => $this->code,
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
            'code' => $this->code,
        ];
    }
}
