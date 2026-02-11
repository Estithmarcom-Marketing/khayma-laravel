<?php

namespace App\Http\Requests\Application\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:500',
            'is_default' => 'sometimes|boolean',
            'additional_info' => 'sometimes|string|max:1000',
        ];
    }
}
