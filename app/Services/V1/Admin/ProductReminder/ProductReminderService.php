<?php

namespace App\Services\V1\Admin\ProductReminder;

use App\Models\Product;
use App\Models\ProductVariation;

class ProductReminderService
{
    public function getProducts()
    {
        $products = ProductVariation::with([
            'product:id,name_ar' ,
            'size:id,name_ar' ,
            'color:id,name_ar'
            ])
        ->withCount('reminders')
        ->whereHas('reminders')
        ->orderBy('reminders_count', 'desc')
        ->paginate();
        return $products;
    }
}
