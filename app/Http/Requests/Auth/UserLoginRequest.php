<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Propaganistas\LaravelPhone\PhoneNumber;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
  
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           
            'phone' => 'required|phone:SA', 
            'otp_code' => 'required|string|max:50',
            'remember_me' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
  public function messages(): array
    {
        $locale = app()->getLocale();
        if ($locale == 'ar') {
            return [
                'phone.required' => 'رقم الهاتف مطلوب',
                'phone.phone' => 'تنسيق رقم الهاتف غير صالح',
                'otp_code.required' => 'رمز التحقق مطلوب',
            ];
        }
        return [
            'phone.required' => 'Phone number is required',
            'phone.phone' => 'Invalid phone number format',
            'otp_code.required' => 'OTP code is required',
        ];
    }
}
