<?php

namespace App\Http\Requests\Banner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'redirect_url' => ['sometimes', 'nullable', 'url'],
            'is_active'    => ['sometimes', 'boolean'],
            'banners'   => ['sometimes', 'nullable', 'array', 'max:10'],
            'banners.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240'],
        ];
    }
}
