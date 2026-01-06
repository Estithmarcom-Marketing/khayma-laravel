<?php

namespace App\Http\Requests\PromoCode;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromoCodeRequest extends FormRequest
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
            'code' => ['sometimes', 'string', 'max:50'],
            'value' => ['sometimes', 'numeric', 'min:0'],
            'is_percentage' => ['sometimes', 'boolean'],
            'expires_at' => ['nullable', 'sometimes', 'date', 'after:today'],
            'is_active' => ['sometimes', 'boolean'],
            'usage_limit' => ['nullable', 'sometimes', 'integer', 'min:1'],
        ];
    }
}
