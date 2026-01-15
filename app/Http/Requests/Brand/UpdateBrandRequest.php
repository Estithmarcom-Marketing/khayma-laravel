<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
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
        return [
            'name_ar' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'slug_ar' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('brands', 'slug_ar')->ignore($this->route('brand')),
            ],
            'slug_en' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('brands', 'slug_en')->ignore($this->route('brand')),
            ],
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',

        ];
    }
}
