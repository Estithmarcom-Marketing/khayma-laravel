<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubCategoryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $subCategoryId = $this->route('subCategory')->id;
        return [
            'name_en' => 'sometimes|string|max:255|unique:categories,name_en,' . $subCategoryId,
            'name_ar' => 'sometimes|string|max:255|unique:categories,name_ar,' . $subCategoryId,
            'description_en' => 'sometimes|nullable|max:5000',
            'description_ar' => 'sometimes|nullable|max:5000',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
        ];
    }
}
