<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:500',
            'description_en' => 'nullable|string|max:500',
            'slug_ar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug_ar')
                    ->whereNull('deleted_at'),
            ],
            'slug_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug_en')
                    ->whereNull('deleted_at'),
            ],
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'is_published' => 'required|boolean',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_title_en' => 'nullable|string|max:255',
            'meta_description_ar' => 'nullable|string|max:500',
            'meta_description_en' => 'nullable|string|max:500',

        ];
    }
}
