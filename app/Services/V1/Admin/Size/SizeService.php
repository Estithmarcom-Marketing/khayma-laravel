<?php

namespace App\Services\V1\Admin\Size;

use App\Models\Size;

class SizeService
{
    public function list()
    {
        return Size::all();
    }

    public function store(array $data)
    {
        return Size::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
        ]);
    }

    public function update(Size $size, array $data)
    {
        $size->update([
            'name_ar' => $data['name_ar'] ?? $size->name_ar,
            'name_en' => $data['name_en'] ?? $size->name_en,
        ]);

        return $size->refresh();
    }

    public function delete(Size $size)
    {
         $size->delete();
    }

    public function show(Size $size)
    {
        return $size;
    }
}
