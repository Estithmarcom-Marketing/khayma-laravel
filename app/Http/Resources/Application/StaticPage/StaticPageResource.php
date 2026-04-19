<?php

namespace App\Http\Resources\Application\StaticPage;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaticPageResource extends JsonResource
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
            'title' => $this->title_ar,
            'slug' => $this->slug_ar,
            'content' => $this->content_ar,
            'meta_title' => $this->meta_title_ar,
            'meta_description' => $this->meta_description_ar,
        ];
    }
    private function englishResource()
    {
        return [
            'id' => $this->id,
            'title' => $this->title_en,
            'slug' => $this->slug_en,
            'content' => $this->content_en,
            'meta_title' => $this->meta_title_en,
            'meta_description' => $this->meta_description_en,
        ];
    }
}
