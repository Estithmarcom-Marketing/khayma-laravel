<?php

namespace App\Http\Requests\StaticPage;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaticPageRequest extends FormRequest
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
        $staticPageId = $this->route('staticPage')->id;
        return [
            'title_ar' => ['sometimes', 'string', 'max:255'],
            'title_en' => ['sometimes', 'string', 'max:255'],
            'content_ar' => ['sometimes', 'string'],
            'content_en' => ['sometimes', 'string'],
            'slug_ar' => ['sometimes', 'string', 'max:255', Rule::unique('static_pages', 'slug_ar')->ignore($staticPageId)],
            'slug_en' => ['sometimes', 'string', 'max:255', Rule::unique('static_pages', 'slug_en')->ignore($staticPageId)],
            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],
            'meta_description_ar' => ['nullable', 'string', 'max:2000'],
            'meta_description_en' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
