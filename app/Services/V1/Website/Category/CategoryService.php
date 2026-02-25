<?php

namespace App\Services\V1\Website\Category;

use App\Models\Category;

class CategoryService
{
    public function list()
    {
        return Category::select(['id', 'name_ar', 'name_en'])
            ->with([
                'subCategories:id,name_ar,name_en,parent_id'])
            ->whereNull('parent_id')
            ->get();
    }

    public function show($id)
    {
        return Category::select(['id', 'name_ar', 'name_en'])
            ->with([
                'subCategories:id,name_ar,name_en,parent_id'])
            ->findorFail($id);
    }
}
