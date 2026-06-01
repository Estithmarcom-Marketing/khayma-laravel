<?php

namespace App\Helpers;

use Illuminate\Support\Str;

if (! function_exists('make_slug')) {
    function make_slug(string $title, string $locale, string $model, string $column = 'slug'): string
    {
        if ($locale === 'ar') {
            $slug = trim($title);

            // Remove all special characters except letters, numbers, spaces, and hyphens
            $slug = preg_replace('/[^\p{Arabic}\p{L}\p{N}\s-]/u', '', $slug);

            // Replace consecutive spaces with a single hyphen
            $slug = preg_replace('/\s+/u', '-', $slug);

            // Remove duplicate hyphens
            $slug = preg_replace('/-+/', '-', $slug);

            // Trim hyphens from beginning and end
            $slug = trim($slug, '-');
        } else {
            $slug = Str::slug($title);
        }

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
