<?php

namespace App\Services\V1\Admin\Brand;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class BrandService
{
    public function list()
    {
        return Brand::with('media')->paginate(10);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $brand = Brand::create([
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description_ar' => $data['description_ar'] ?? null,
                'description_en' => $data['description_en'] ?? null,
                'slug_ar' => $data['slug_ar'],
                'slug_en' => $data['slug_en'],
            ]);
            if (isset($data['image'])) {
                $brand->addMedia($data['image'])->toMediaCollection('brand');
            }

            return $brand->refresh();
        });
    }

    public function update(Brand $brand, array $data)
    {
        return DB::transaction(function () use ($brand, $data) {
            $brand->update([
                'name_ar' => $data['name_ar'] ?? $brand->name_ar,
                'name_en' => $data['name_en'] ?? $brand->name_en,
                'description_ar' => $data['description_ar'] ?? $brand->description_ar,
                'description_en' => $data['description_en'] ?? $brand->description_en,
                'slug_ar' => $data['slug_ar'] ?? $brand->slug_ar,
                'slug_en' => $data['slug_en'] ?? $brand->slug_en,
            ]);
            if (isset($data['image'])) {
                $brand->addMedia($data['image'])->toMediaCollection('brand');
            }

            return $brand->refresh();
        });
    }

    public function show(Brand $brand)
    {
        return $brand->load(['media']);
    }

    public function delete(Brand $brand)
    {
        return $brand->clearMediaCollection('brand')->delete();
    }
}
