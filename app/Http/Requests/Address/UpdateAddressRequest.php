<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'city_id' => 'sometimes|exists:cities,id',
            'name' => 'sometimes|string|max:255',
            'value' => 'sometimes|string|max:500',
            'is_default' => 'sometimes|boolean',
            'additional_info' => 'sometimes|nullable|string|max:1000',
        ];
    }
}
