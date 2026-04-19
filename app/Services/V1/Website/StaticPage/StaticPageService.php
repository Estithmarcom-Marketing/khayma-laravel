<?php

namespace App\Services\V1\Website\StaticPage;

use App\Models\StaticPage;

class StaticPageService
{
    public function list()
    {
        return StaticPage::latest()->get();
    }
    public function show($identifier)
    {
        return StaticPage::where('slug_en', $identifier)
            ->orWhere('slug_ar', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();
    }
}
