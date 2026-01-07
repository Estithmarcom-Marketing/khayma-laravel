<?php

namespace App\Http\Requests\PaymentGateway;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentGatewayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_ar' => 'sometimes|string|max:255|unique:payment_gateways,name_ar,'.$this->paymentGateway->id,
            'name_en' => 'sometimes|string|max:255|unique:payment_gateways,name_en,'.$this->paymentGateway->id,
            'is_active' => 'sometimes|boolean',
            'payment_method_id' => 'sometimes|exists:payment_methods,id',
        ];
    }
}
