<?php

namespace App\Services\V1\Website\ProductReminder;

use App\Events\ProductReminder\ProductReminderPlaced;
use App\Models\ProductReminder;
use App\Models\ProductVariation;

class ProductReminderService
{
    public function list()
    {
        $user = auth()->user();
        $productReminders = $user->productReminders()
            ->where('is_notified', false)
            ->with(
                [
                    'productVariation' => function ($q) {
                        $q->selectWithActiveOffer()
                            ->withIsInReminder()
                            ->active();
                    },
                    'productVariation.color:id,name_en,name_ar,code',
                    'productVariation.size:id,name_en,name_ar',
                    'productVariation.properties:id,name_en,name_ar',
                    'productVariation.product:id,name_en,name_ar,slug_en,slug_ar,brand_id,category_id,description_en,description_ar',
                    'productVariation.product.media:id,model_id,name,file_name,collection_name,disk'
                ]
            )
            ->paginate(10);

        return $productReminders;
    }

    public function store(ProductVariation $productvariation)
    {
        $user = auth()->user();

        if ($productvariation->stock_quantity == 0) {
            $reminder = $user->productReminders()->create([
                'product_variation_id' => $productvariation->id,
            ]);

            event(new ProductReminderPlaced($reminder));

            return $reminder;
        }

        return false;
    }

    public function delete(ProductVariation $productvariation)
    {
        $productReminder = auth('sanctum')->user()->productReminders()->where('product_variation_id', $productvariation->id)->first();

        return $productReminder->delete();
    }

    public function show(ProductReminder $productReminder)
    {
        return $productReminder->load([
            'productVariation' => function ($q) {
                $q->selectWithActiveOffer()
                    ->withIsInReminder()
                    ->active();
            },
            'productVariation.color:id,name_en,name_ar,code',
            'productVariation.size:id,name_en,name_ar',
            'productVariation.properties:id,name_en,name_ar',
            'productVariation.product:id,name_en,name_ar,slug_en,slug_ar,brand_id,category_id,description_en,description_ar',
            'productVariation.product.media:id,model_id,name,file_name,collection_name,disk',
        ]);
    }
}
