<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeSectorRequest extends FormRequest
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
        $rules = [
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'order'       => 'nullable|integer|min:1',
        ];

        if ($this->isMethod('post')) {
            // Saat create (POST)
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240'; // 10 MB
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Saat update (PUT/PATCH)
            $rules['image'] = 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:10240'; // 10 MB
        }

        return $rules;
    }
}
