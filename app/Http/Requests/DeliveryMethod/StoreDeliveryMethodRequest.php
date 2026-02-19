<?php

namespace App\Http\Requests\DeliveryMethod;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryMethodRequest extends FormRequest
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
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'has_shipping_cost' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => __('shipping_methods.validation.name_ar_required'),
            'name_en.required' => __('shipping_methods.validation.name_en_required'),
            'is_active.required' => __('shipping_methods.validation.is_active_required'),
            'has_shipping_cost.required' => __('shipping_methods.validation.has_shipping_cost_required'),
        ];
    }
}
