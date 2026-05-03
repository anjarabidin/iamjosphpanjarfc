<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
        // Ambil method request
        $method = request()->method();

        if ($method === 'POST') {
            // Rules untuk CREATE (store)
            return [
                'name'  => 'required|string|max:255',
                'type'  => 'required|string|max:255',
                'value' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:10240',
            ];
        }

        if ($method === 'PUT' || $method === 'PATCH') {
            // Rules untuk UPDATE
            return [
                'name'  => 'sometimes|required|string|max:255',
                'type'  => 'sometimes|required|string|max:255',
                'value' => 'sometimes|required|string|max:255',
                // biasanya image optional saat update
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:10240',
            ];
        }

        return [];
    }
}
