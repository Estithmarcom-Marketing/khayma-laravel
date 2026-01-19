<?php

namespace App\Services\V1\User\Home;

use App\Models\Banner;

class HomeService
{
    public function getHomeBanners()
    {
        return Banner::query()
            ->with('media')
            ->active()
            ->orderBy('created_at')
            ->get()
            ->groupBy('position');
    }
}
