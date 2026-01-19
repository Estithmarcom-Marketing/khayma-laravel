<?php

namespace App\Services\V1\Admin\Home;

use App\Models\Banner;
use Illuminate\Support\Facades\DB;

class HomeService
{
    public function storeBanners(array $data)
    {
        return DB::transaction(function () use ($data) {
            $banner = Banner::updateOrCreate(['position' => $data['position']], [
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'redirect_url' => $data['redirect_url'] ?? null,
                'position' => $data['position'],
                'is_active' => $data['is_active'] ?? true,
            ]);
            if (isset($data['banners']) && is_array($data['banners'])) {
                $banner->clearMediaCollection('banners');
                foreach ($data['banners'] as $bannerData) {
                    $banner->addMedia($bannerData)->toMediaCollection('banners');
                }
            }

            return $banner->refresh()->load('media');
        });
    }

    public function listHomeBanners()
    {
        return Banner::query()
            ->with('media')
            ->active()
            ->orderBy('created_at')
            ->get()
            ->groupBy('position');
    }

    public function listBanners()
    {
        return Banner::query()
            ->with('media')
            ->orderBy('created_at')
            ->paginate(10);
    }

    public function deleteBanner(Banner $banner)
    {
        return $banner->delete();
    }
}
