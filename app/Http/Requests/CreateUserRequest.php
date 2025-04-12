<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'f_name' => 'nullable|string|max:255',
            'l_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'gender' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_admin' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
