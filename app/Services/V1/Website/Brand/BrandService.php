<?php

namespace App\Services\V1\Website\Brand;

use App\Models\Brand;

class BrandService
{
    public function list()
    {
        return Brand::select(['id', 'name_ar', 'name_en', 'slug_ar', 'slug_en', 'description_ar', 'description_en'])
            ->get();
    }

    public function show($id)
    {
        return Brand::select(['id', 'name_ar', 'name_en', 'slug_ar', 'slug_en', 'description_ar', 'description_en'])
            ->findorFail($id);
    }
}
