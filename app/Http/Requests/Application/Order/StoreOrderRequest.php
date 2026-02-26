<?php

namespace App\Http\Requests\Application\Order;

use App\Enums\Payments\PaymentGatewayEnum;
use App\Models\DeliveryMethod;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
        $deliveryMethodId = $this->input('delivery_method_id');
        $paymentMethodId = $this->input('payment_method_id');

        $noShipping = $deliveryMethodId
            ? DeliveryMethod::where('id', $deliveryMethodId)
                ->where('has_shipping_cost', false)
                ->exists()
            : false;

        $isCash = $paymentMethodId
            ? PaymentMethod::find($paymentMethodId)?->type->value === 'cash'
            : false;

        return [
            'address_id' => [$noShipping ? 'nullable' : 'required_with:delivery_method_id', 'exists:addresses,id'],
            'delivery_method_id' => ['sometimes', 'exists:delivery_methods,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'gateway' => [$isCash ? 'nullable' : 'required', Rule::in(PaymentGatewayEnum::values())],
            'promo_code' => ['nullable', 'string', Rule::exists('promo_codes', 'code')->where('is_active', true)],
            'phone' => ['nullable', 'string', 'phone:SA'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email:rfc,dns'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if (! $this->filled(['payment_method_id', 'gateway'])) {
                return;
            }

            $gatewayExists = PaymentGateway::query()
                ->where('payment_method_id', $this->input('payment_method_id'))
                ->where('gateway', $this->input('gateway'))
                ->where('is_active', true)
                ->exists();

            if (! $gatewayExists) {
                $validator->errors()->add(
                    'gateway', __('orders.gateway_valid')
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'address_id.required_with' => __('orders.address_id_required_with'),
            'address_id.exists' => __('orders.address_id_exists'),
            'delivery_method_id.exists' => __('orders.delivery_method_id_exists'),
            'payment_method_id.required' => __('orders.payment_method_id_required'),
            'payment_method_id.exists' => __('orders.payment_method_id_exists'),
            'promo_code.exists' => __('orders.promo_code_exists'),
            'phone.phone' => __('orders.phone_phone'),
            'name.string' => __('orders.name_string'),
            'name.max' => __('orders.name_max'),
            'email.string' => __('orders.email_string'),
            'email.email' => __('orders.email_email'),
            'gateway.required' => __('orders.gateway_required'),
            'gateway.in' => __('orders.gateway_in'),
        ];
    }
}
