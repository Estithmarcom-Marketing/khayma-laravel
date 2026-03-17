<?php

namespace App\Services\V1\Admin\Banner;

use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class BannerService
{
    public function list()
    {
        return Banner::with('media')->paginate(10);
    }

    public function update(Banner $banner, array $data)
    {
        return DB::transaction(function () use ($banner, $data) {
            $banner->update([
                'redirect_url' => $data['redirect_url'] ?? $banner->redirect_url,
                'is_active'    => $data['is_active'] ?? $banner->is_active,
            ]);

            if (isset($data['banners']) && is_array($data['banners'])) {
                $banner->clearMediaCollection('banners');
                foreach ($data['banners'] as $file) {
                    $banner->addMedia($file)->toMediaCollection('banners');
                }
            }

            return $banner->refresh()->load('media');
        });
    }
}
