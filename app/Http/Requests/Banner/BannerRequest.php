<?php

namespace App\Http\Requests\Banner;

use App\Enums\BannerPosition\BannerPositionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class BannerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bannerId = $this->route('banner')?->id;

        return [
            'title_en' => ['required', 'string', 'max:255'],
            'title_ar' => ['required', 'string', 'max:255'],
            'position' => [
                'required',
                new Enum(BannerPositionEnum::class)
            ],
            'redirect_url' => ['nullable', 'url'],
            'is_active' => ['sometimes', 'boolean'],
            'banners' => ['nullable', 'array', 'max:10'],
            'banners.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240'],
        ];
    }
}
