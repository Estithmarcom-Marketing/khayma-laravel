<?php

namespace App\Services\V1\Website\Category;

use App\Models\Category;

class CategoryService
{
    public function list()
    {
        return Category::select(['id', 'name_ar', 'name_en'])
            ->with(['media:id,model_id,name,file_name,collection_name,disk',
                'subCategories.media:id,model_id,name,file_name,collection_name,disk'])
            ->whereNull('parent_id')
            ->get();
    }

    public function show($id)
    {
        return Category::select(['id', 'name_ar', 'name_en'])
            ->with(['media:id,model_id,name,file_name,collection_name,disk',
                'subCategories.media:id,model_id,name,file_name,collection_name,disk'])
            ->findorFail($id);
    }
}
