<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport implements FromCollection, WithCustomCsvSettings, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Category::select([
            'id',
            'name_en',
            'name_ar',
            'slug_en',
            'slug_ar',
            'description_en',
            'description_ar',
            'parent_id',
            'created_at',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name EN',
            'Name AR',
            'Slug EN',
            'Slug AR',
            'Description EN',
            'Description AR',
            'Parent ID',
            'Created At',
        ];
    }

    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}
