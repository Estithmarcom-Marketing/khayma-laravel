<?php

namespace App\Http\Resources\Application\Banner;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
            'position' => $this->position,
            'redirect_url' => $this->redirect_url,
            'banners' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'url' => $media->original_url,
                    ];
                });
            }),
        ];
    }

    private function englishResource()
    {
        return [
            'id' => $this->id,
            'title' => $this->title_en,
            'position' => $this->position,
            'redirect_url' => $this->redirect_url,
            'banners' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'url' => $media->original_url,
                    ];
                });
            }),
        ];
    }
}
