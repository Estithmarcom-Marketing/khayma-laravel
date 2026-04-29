<?php

namespace App\Http\Requests\PaymentGateway;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentGatewayRequest extends FormRequest
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
            'name_ar' => 'required|string|max:255|unique:payment_gateways,name_ar',
            'name_en' => 'required|string|max:255|unique:payment_gateways,name_en',
            'gateway' => 'required|string|max:255|unique:payment_gateways,gateway',
            'is_active' => 'required|boolean',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240']
        ];
    }
}
