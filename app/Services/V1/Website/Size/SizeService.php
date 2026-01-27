<?php

namespace App\Services\V1\Website\Size;

use App\Models\Size;

class SizeService
{
    public function list()
    {
        return Size::select('name_ar', 'name_en')->get();
    }

    public function show($id)
    {
        return Size::select('name_ar', 'name_en')->where('id', $id)->first();
    }
}
