<?php

namespace App\Services\V1\Admin\Category;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function list()
    {
        return Category::with(['parent', 'media'])->whereNull('parent_id')->paginate(10);
    }

    public function show(Category $category)
    {
        return $category->load(['parent', 'subCategories', 'media']);
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $category = Category::create([
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'],
                'slug_ar' => $data['slug_ar'],
                'slug_en' => $data['slug_en'],
                'parent_id' => null,
            ]);

            if (isset($data['image'])) {
                $category->addMedia($data['image'])->toMediaCollection('category');
            }

            return $category->refresh();
        });
    }

    public function update(Category $category, array $data)
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update([
                'name_ar' => $data['name_ar'] ?? $category->name_ar,
                'name_en' => $data['name_en'] ?? $category->name_en,
                'description_ar' => $data['description_ar'] ?? $category->description_ar,
                'description_en' => $data['description_en'] ?? $category->description_en,
                'slug_ar' => $data['slug_ar'] ?? $category->slug_ar,
                'slug_en' => $data['slug_en'] ?? $category->slug_en,
            ]);
            if (isset($data['image'])) {
                $category->addMedia($data['image'])->toMediaCollection('category');
            }

            return $category->refresh();
        });
    }

    public function delete(Category $category)
    {
        return $category->clearMediaCollection('category')->delete();
    }

    public function storeSubCategory(Category $category, array $data)
    {
        return DB::transaction(function () use ($category, $data) {
            $subCategory = $category->subCategories()->create([
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'],
                'description_ar' => $data['description_ar'] ?? '',
                'description_en' => $data['description_en'] ?? '',
                'slug_ar' => $data['slug_ar'],
                'slug_en' => $data['slug_en'],
            ]);

            if (isset($data['image'])) {
                $subCategory->addMedia($data['image'])->toMediaCollection('category');
            }

            return $subCategory->refresh();
        });
    }

    public function updateSubCategory(Category $category, Category $subCategory, array $data)
    {
        if ($subCategory->parent_id !== $category->id) {
            throw new \Exception('The specified sub-category does not belong to the given category.');
        }

        return DB::transaction(function () use ($subCategory, $data) {
            $subCategory->update([
                'name_ar' => $data['name_ar'] ?? $subCategory->name_ar,
                'name_en' => $data['name_en'] ?? $subCategory->name_en,
                'description_ar' => $data['description_ar'] ?? $subCategory->description_ar,
                'description_en' => $data['description_en'] ?? $subCategory->description_en,
                'slug_ar' => $data['slug_ar'] ?? $subCategory->slug_ar,
                'slug_en' => $data['slug_en'] ?? $subCategory->slug_en,
            ]);
            if (isset($data['image'])) {
                $subCategory->addMedia($data['image'])->toMediaCollection('category');
            }
            return $subCategory->refresh();
        });
    }

    public function listSubCategories(Category $category)
    {
        return $category
            ->subCategories()
            ->with(['parent', 'media:id,model_id,name,file_name,collection_name,disk'])
            ->paginate(10);
    }

    public function deleteSubCategory(Category $category, Category $subCategory)
    {
        if ($subCategory->parent_id !== $category->id) {
            throw new \Exception('The specified sub-category does not belong to the given category.');
        }
        $subCategory->clearMediaCollection('category')->delete();
    }
}
