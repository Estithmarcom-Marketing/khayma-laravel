<?php

namespace App\Services\V1\Website\ProductReminder;

use App\Models\ProductReminder;
use App\Models\ProductVariation;

class ProductReminderService
{
    public function list()
    {
        $user = auth('sanctum')->user();
        $productReminders = $user->productReminders()->with(['productVariation'])->paginate(10);

        return $productReminders;
    }

    public function store(ProductVariation $productvariation)
    {
        $user = auth('sanctum')->user();

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
        return $productReminder->load(['productVariation', 'productVariation.color', 'productVariation.size']);
    }
}
