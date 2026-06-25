<?php

namespace App\Services\V1\Admin\Color;

use App\Models\Color;

class ColorService
{
    public function list()
    {
        return Color::paginate(10);
    }
    public function listWithoutPagination()
    {
        return Color::select(['id', 'name_ar', 'code'])->get();
    }

    public function store(array $data): Color
    {
        return Color::create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'code' => $data['code'],
        ]);
    }

    public function update(Color $color, array $data): Color
    {
        $color->update([
            'name_en' => $data['name_en'] ?? $color->name_en,
            'name_ar' => $data['name_ar'] ?? $color->name_ar,
            'code' => $data['code'] ?? $color->code,
        ]);

        return $color->refresh();
    }

    public function destroy(Color $color): void
    {
        $color->delete();
    }

    public function show(Color $color): Color
    {
        return $color;
    }
}
