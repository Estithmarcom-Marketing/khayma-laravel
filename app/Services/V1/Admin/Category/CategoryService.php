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
            $category->update($data);
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
                'description_en' => $data['description_en'] ?? ''
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
            throw new \LogicException(__('category.sub_category_does_not_belong_to_given_category'));
        }

        return DB::transaction(function () use ($subCategory, $data) {
            $subCategory->update($data);
            if (isset($data['image'])) {
                $subCategory->clearMediaCollection('category');
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
            throw new \LogicException(__('category.sub_category_does_not_belong_to_given_category'));
        }
        $subCategory->clearMediaCollection('category')->delete();
    }

    public function subCategoriesNoPagination(Category $category)
    {
        return $category
            ->subCategories()
            ->select(['id', 'name_ar', 'name_en'])
            ->with(['parent', 'media:id,model_id,name,file_name,collection_name,disk'])
            ->get();
    }

    public function categoriesNoPagination()
    {
        return Category::select(['id', 'name_ar', 'name_en'])
            ->with(['media:id,model_id,name,file_name,collection_name,disk'])
            ->whereNull('parent_id')
            ->get();
    }
}
