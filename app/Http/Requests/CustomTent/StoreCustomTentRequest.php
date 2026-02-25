<?php

namespace App\Http\Requests\CustomTent;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomTentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule,array<mixed>,string>
     */
    public function rules(): array
    {
        return [
          'user_name' => ['required','string','max:255'],
          'phone'=> ['required','string'],
          'description' => ['required','string'],
          'size'=> ['nullable','sometimes','string'],
         'images' => ['nullable', 'array', 'max:10'],
         'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:10240']
        ];
    }
}
