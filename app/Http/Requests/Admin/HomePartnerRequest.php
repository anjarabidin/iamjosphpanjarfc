<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomePartnerRequest extends FormRequest
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
            'order' => 'required|integer|min:1',
            'link' => 'nullable|url',
            'is_active' => 'boolean',
        ];

        if ($this->isMethod('post')) {
            // Aturan saat create (POST)
            $rules['image'] = 'required|image|mimes:jpg,jpeg,png|max:10240';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Aturan saat update (PUT/PATCH)
            $rules['image'] = 'nullable|image|mimes:jpg,jpeg,png|max:10240';
        }

        return $rules;
    }
}
