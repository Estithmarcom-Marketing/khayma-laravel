<?php

namespace App\Services\V1\Admin\Brand;

use App\Models\Brand;

class BrandService
{
    public function list()
    {
        return Brand::paginate(10);
    }

    public function store(array $data)
    {
        return Brand::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'description_ar' => $data['description_ar'],
            'description_en' => $data['description_en'],
            'slug_ar' => $data['slug_ar'],
            'slug_en' => $data['slug_en'],

        ]);
    }

    public function update(Brand $brand, array $data)
    {
        $brand->update([
            'name_ar' => $data['name_ar'] ?? $brand->name_ar,
            'name_en' => $data['name_en'] ?? $brand->name_en,
            'description_ar' => $data['description_ar'] ?? $brand->description_ar,
            'description_en' => $data['description_en'] ?? $brand->description_en,
            'slug_ar' => $data['slug_ar'] ?? $brand->slug_ar,
            'slug_en' => $data['slug_en'] ?? $brand->slug_en,
        ]);

        return $brand->refresh();
    }

    public function show(Brand $brand)
    {
        return $brand;
    }

    public function delete(Brand $brand)
    {
        return $brand->delete();
    }
}
