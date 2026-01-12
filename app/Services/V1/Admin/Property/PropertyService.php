<?php

namespace App\Services\V1\Admin\Property;

use App\Models\Property;

class PropertyService
{
    public function list()
    {
        return Property::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
        return Property::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
        ]);
    }

    public function update(Property $property, array $data)
    {
        $property->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
        ]);

        return $property->refresh();
    }
}
