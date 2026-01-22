<?php

namespace App\Services\V1\Website\Color;

use App\Models\Color;

class ColorService
{
    public function list()
    {
        return Color::select(['id', 'code'])->get();
    }

    public function show($id)
    {
        return Color::select(['id', 'code'])->where('id', $id)->first();
    }
}
