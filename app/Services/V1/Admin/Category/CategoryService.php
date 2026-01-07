<?php

namespace App\Services\V1\Admin\Category;

use App\Models\Category;

class CategoryService
{
    public function list()
    {
        return Category::whereNull('parent_id')->paginate(10);
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function store(array $data)
    {
        return Category::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'description_ar' => $data['description_ar'],
            'description_en' => $data['description_en'],
            'slug_ar' => $data['slug_ar'],
            'slug_en' => $data['slug_en'],
            'parent_id' => null,
        ]);
    }

    public function update(Category $category, array $data)
    {
        $category->update([
            'name_ar' => $data['name_ar'] ?? $category->name_ar,
            'name_en' => $data['name_en'] ?? $category->name_en,
            'description_ar' => $data['description_ar'] ?? $category->description_ar,
            'description_en' => $data['description_en'] ?? $category->description_en,
            'slug_ar' => $data['slug_ar'] ?? $category->slug_ar,
            'slug_en' => $data['slug_en'] ?? $category->slug_en,
        ]);

        return $category->refresh();
    }

    public function delete(Category $category)
    {
        return $category->delete();
    }

    public function storeSubCategory(Category $category, array $data)
    {
        return $category->subCategories()->create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'description_ar' => $data['description_ar'],
            'description_en' => $data['description_en'],
            'slug_ar' => $data['slug_ar'],
            'slug_en' => $data['slug_en'],
        ]);
    }

    public function updateSubCategory(Category $category, Category $subCategory, array $data)
    {
        if ($subCategory->parent_id !== $category->id) {
            throw new \Exception('The specified sub-category does not belong to the given category.');
        }

        $subCategory->update([
            'name_ar' => $data['name_ar'] ?? $subCategory->name_ar,
            'name_en' => $data['name_en'] ?? $subCategory->name_en,
            'description_ar' => $data['description_ar'] ?? $subCategory->description_ar,
            'description_en' => $data['description_en'] ?? $subCategory->description_en,
            'slug_ar' => $data['slug_ar'] ?? $subCategory->slug_ar,
            'slug_en' => $data['slug_en'] ?? $subCategory->slug_en,
        ]);

        return $subCategory->refresh();
    }

    public function listSubCategories(Category $category)
    {
        return $category->subCategories()->paginate(10);
    }

    public function deleteSubCategory(Category $category, Category $subCategory)
    {
        if ($subCategory->parent_id !== $category->id) {
            throw new \Exception('The specified sub-category does not belong to the given category.');
        }

        return $subCategory->delete();
    }
}
