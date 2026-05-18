<?php

namespace App\Services\V1\Admin\Property;

use App\Models\Property;

class PropertyService
{
    public function list()
    {
        return Property::select(['id', 'name_ar', 'name_en', 'created_at'])
            ->latest()
            ->paginate(10);
    }
    public function listWithoutPagination()
    {
        return Property::select(['id', 'name_ar'])
            ->latest()
            ->get();
    }

    public function show(Property $property)
    {
        return $property;
    }

    public function delete(Property $property)
    {
        return $property->delete();
    }

    public function store(array $data)
    {
        return Property::create($data);
    }

    public function update(Property $property, array $data)
    {
        $property->fill($data)->save();

        return $property->refresh();
    }
}
