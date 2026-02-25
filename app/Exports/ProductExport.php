<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithCustomCsvSettings, WithHeadings
{
    public function collection()
    {
        return Product::with([
            'category:id,name_en,name_ar',
            'brand:id,name_en,name_ar',
            'productVariations.size:id,name_en,name_ar',
            'productVariations.color:id,name_en,name_ar',
            'productVariations.properties:id,name_en,name_ar'
        ])
        ->select([
            'id',
            'name_en',
            'name_ar',
            'slug_en',
            'slug_ar',
            'description_en',
            'description_ar',
            'category_id',
            'brand_id',
            'created_at',
        ])->get()
        ->flatMap(function ($product) {
            
            if ($product->productVariations->isEmpty()) {
                return [[
                    'id' => $product->id,
                    'name_en' => $product->name_en,
                    'name_ar' => $product->name_ar,
                    'slug_en' => $product->slug_en,
                    'slug_ar' => $product->slug_ar,
                    'description_en' => $product->description_en,
                    'description_ar' => $product->description_ar,
                    'category_name_en' => optional($product->category)->name_en,
                    'category_name_ar' => optional($product->category)->name_ar,
                    'brand_name_en' => optional($product->brand)->name_en,
                    'brand_name_ar' => optional($product->brand)->name_ar,
                    'variation_price' => null,
                    'variation_color_en' => null,
                    'variation_color_ar' => null,
                    'variation_size_en' => null,
                    'variation_size_ar' => null,
                    'variation_offer' => null,
                    'variation_offer_started_date' => null,
                    'variation_offer_expired_date' => null,
                    'variation_stock_quantity' => null,
                    'variation_properties_en' => null,
                    'variation_properties_ar' => null,
                    'created_at' => $product->created_at,
                ]];
            }
            
            return $product->productVariations->map(function ($variation) use ($product) {
                return [
                    'id' => $product->id,
                    'name_en' => $product->name_en,
                    'name_ar' => $product->name_ar,
                    'slug_en' => $product->slug_en,
                    'slug_ar' => $product->slug_ar,
                    'description_en' => $product->description_en,
                    'description_ar' => $product->description_ar,
                    'category_name_en' => optional($product->category)->name_en,
                    'category_name_ar' => optional($product->category)->name_ar,
                    'brand_name_en' => optional($product->brand)->name_en,
                    'brand_name_ar' => optional($product->brand)->name_ar,
                    'variation_price' => $variation->price,
                    'variation_color_en' => optional($variation->color)->name_en,
                    'variation_color_ar' => optional($variation->color)->name_ar,
                    'variation_size_en' => optional($variation->size)->name_en,
                    'variation_size_ar' => optional($variation->size)->name_ar,
                    'variation_offer' => $variation->offer,
                    'variation_offer_started_date' => $variation->offer_started_date,
                    'variation_offer_expired_date' => $variation->offer_expired_date,
                    'variation_stock_quantity' => $variation->stock_quantity,
                    'variation_properties_en' => $variation->properties->map(function ($property) {
                        return "{$property->name_en}: {$property->pivot->value_en}";
                    })->implode(' | '),
                    'variation_properties_ar' => $variation->properties->map(function ($property) {
                        return "{$property->name_ar}: {$property->pivot->value_ar}";
                    })->implode(' | '),
                    'created_at' => $product->created_at,
                ];
            });
        });
    }

    public function headings(): array
    {
        $headings = [
            'en' => [
                'ID',
                'Name (EN)',
                'Name (AR)',
                'Slug (EN)',
                'Slug (AR)',
                'Description (EN)',
                'Description (AR)',
                'Category (EN)',
                'Category (AR)',
                'Brand (EN)',
                'Brand (AR)',
                'Price',
                'Color (EN)',
                'Color (AR)',
                'Size (EN)',
                'Size (AR)',
                'Stock Quantity',
                'Offer',
                'Offer Start Date',
                'Offer End Date',
                'Properties (EN)',
                'Properties (AR)',
                'Created At',
            ],
            'ar' => [
                'المعرف',
                'الاسم (إنجليزي)',
                'الاسم (عربي)',
                'الرابط (إنجليزي)',
                'الرابط (عربي)',
                'الوصف (إنجليزي)',
                'الوصف (عربي)',
                'الفئة (إنجليزي)',
                'الفئة (عربي)',
                'العلامة التجارية (إنجليزي)',
                'العلامة التجارية (عربي)',
                'السعر',
                'اللون (إنجليزي)',
                'اللون (عربي)',
                'المقاس (إنجليزي)',
                'المقاس (عربي)',
                'الكمية المتوفرة',
                'العرض',
                'تاريخ بداية العرض',
                'تاريخ نهاية العرض',
                'الخصائص (إنجليزي)',
                'الخصائص (عربي)',
                'تاريخ الإنشاء',
            ],
        ];

        $locale = app()->getLocale();

        return $headings[$locale]?? $headings['ar'];
    }


    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
        ];
    }
}