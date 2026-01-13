<?php

namespace App\Services\V1\Admin\Product;

use App\Models\ProductVariation;
use App\Models\Property;

class VariantPropertyService
{
    public function storeVariationProperty(ProductVariation $productVariation, array $data)
    {
        $productVariation->properties()->syncWithoutDetaching([
            $data['property_id'] => [
                'value_ar' => $data['value_ar'],
                'value_en' => $data['value_en'],
            ],
        ]);

        return $productVariation->properties()
            ->where('properties.id', $data['property_id'])
            ->first();

    }

    public function deleteVariationProperty(ProductVariation $productVariation, Property $property)
    {
        return $productVariation->properties()->detach($property->id);
    }

    public function showVariationProperty(ProductVariation $productVariation, Property $property)
    {
        return $productVariation->properties()->find($property->id);

    }

    public function listVariationProperties(ProductVariation $productVariation)
    {
        return $productVariation->properties()->paginate(10);
    }
}
