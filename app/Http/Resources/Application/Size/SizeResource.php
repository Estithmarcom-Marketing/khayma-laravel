<?php

namespace App\Http\Resources\Application\Size;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
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
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'name' => $this->name_en,
        ];
    }
}
