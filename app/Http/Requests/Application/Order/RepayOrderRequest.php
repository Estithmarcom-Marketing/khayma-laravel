<?php

namespace App\Http\Requests\Application\Order;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepayOrderRequest extends FormRequest
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
        $paymentMethodId = $this->input('payment_method_id');
        $isCash = $paymentMethodId
           ? PaymentMethod::find($paymentMethodId)?->type->value === 'cash'
           : false;

        return [
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'gateway' => [$isCash ? 'nullable' : 'required', Rule::in(PaymentGatewayEnum::values())],
        ];
    }
}
