<?php

namespace App\Services\V1\Website\ProductReminder;

use App\Models\ProductReminder;
use App\Models\ProductVariation;

class ProductReminderService
{
    public function list()
    {
        $user = auth()->user();
        $productReminders = $user->productReminders()->with(
            ['productVariation',
                'productVariation.color:id,name_en,name_ar,code',
                'productVariation.size:id,name_en,name_ar',
                'productVariation.properties:id,name_en,name_ar',
                'productVariation.product:id,name_en,name_ar,slug_en,slug_ar,brand_id,category_id,description_en,description_ar',
                'productVariation.product.media:id,model_id,name,file_name,collection_name,disk'])
            ->paginate(10);

        return $productReminders;
    }

    public function store(ProductVariation $productvariation)
    {
        $user = auth()->user();

        return $user->productReminders()->create([
            'product_variation_id' => $productvariation->id,
        ]);
    }

    public function delete(ProductReminder $productReminder)
    {
        $productReminder->delete();
    }

    public function show(ProductReminder $productReminder)
    {
        return $productReminder->load(['productVariation',
            'productVariation.color:id,name_en,name_ar,code',
            'productVariation.size:id,name_en,name_ar',
            'productVariation.properties:id,name_en,name_ar',
            'productVariation.product:id,name_en,name_ar,slug_en,slug_ar,brand_id,category_id,description_en,description_ar',
            'productVariation.product.media:id,model_id,name,file_name,collection_name,disk']);
    }
}
