<?php

namespace App\Helpers;

use Illuminate\Support\Str;

if (! function_exists('make_slug')) {
    function make_slug(string $title, string $locale, string $model, string $column = 'slug'): string
    {
        $slug = $locale == 'ar'
            ? preg_replace('/\s+/u', '-', trim($title))
            : Str::slug($title);

        $allSlugs = $model::where($column, 'LIKE', $slug . '%')
            ->pluck($column);

        if (! $allSlugs->contains($slug)) {
            return $slug;
        }

        $i = 1;
        while ($allSlugs->contains($slug . '-' . $i)) {
            $i++;
        }

        return $slug . '-' . $i;
    }
    if (! function_exists('normalize_saudi_phone_number')) {
        function normalize_saudi_phone_number(string $phone): string
        {
            $phone = preg_replace('/\D/', '', $phone);

            if (str_starts_with($phone, '00')) {
                $phone = substr($phone, 2);
            }

            if (str_starts_with($phone, '966')) {
                $phone = substr($phone, 3);
            }

            $phone = ltrim($phone, '0');

            if (preg_match('/^\d{9}$/', $phone)) {
                return '966' . $phone;
            }

            return $phone;
        }
    }
}
