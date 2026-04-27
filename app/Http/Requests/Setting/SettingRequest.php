<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'facebook'  => 'nullable|string|url|max:255',
            'instagram' => 'nullable|string|url|max:255',
            'x'         => 'nullable|string|url|max:255',
            'snapchat'  => 'nullable|string|url|max:255',
            'tiktok'    => 'nullable|string|url|max:255',
            'linkedin'  => 'nullable|string|url|max:255',
            'address'   => 'nullable|string|max:500',
            'phone'     => 'nullable|phone:SA',
            'email'     => 'nullable|email|max:255',
            'whatsapp'  => 'nullable|string|max:20',
            'telegram'  => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ];
    }
}
