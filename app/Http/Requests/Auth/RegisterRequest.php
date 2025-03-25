<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserGenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
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
            'f-name' => 'nullable|string|max:255',
            'l-name' => 'nullable|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users,email',
            'phone' => [
                'required',
                'string',
                'regex:/^\d{6,20}$/',
                'unique:users,phone'
            ],
            'phone_country_code' => [
                'required',
                'regex:/^\+\d{1,5}$/'
            ],
            'password' => 'required|string|min:8|confirmed',
            'gender' => ["nullable",new Enum(UserGenderEnum::class)],
            'agree_terms' => 'required|accepted',
        ];
    }

    /**
     * Custom error messages for validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'agree_terms.accepted' => 'You must accept the terms and conditions.',
            'phone.regex' => 'The phone number must be between 6 and 20 digits.',
            'phone_country_code.regex' => 'The country code must start with "+" followed by 1 to 5 digits.',
        ];
    }
}
