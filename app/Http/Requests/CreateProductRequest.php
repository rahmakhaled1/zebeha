<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ImageRequest;


class CreateProductRequest extends FormRequest
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
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'supcategory_id' => 'required|exists:sup_categories,id',
                'images' => 'nullable|array',
                'images.*.path' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            ];
        }


}
