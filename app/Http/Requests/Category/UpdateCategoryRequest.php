<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'name_en' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
            'description_en' => 'sometimes|nullable',
            'description_ar' => 'sometimes|nullable',
            'slug_en' => 'sometimes|string|max:255|unique:categories,slug_en,'.$this->route('category')->id,
            'slug_ar' => 'sometimes|string|max:255|unique:categories,slug_ar,'.$this->route('category')->id,
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ];
    }
}
