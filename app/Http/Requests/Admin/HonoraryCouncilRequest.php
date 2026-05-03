<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HonoraryCouncilRequest extends FormRequest
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
            'position'    => 'required|string|max:255',
            'position_en' => 'required|string|max:255',
            'order'       => 'required|integer|min:1',
        ];

        if ($this->isMethod('post')) {
            // Saat create, image wajib
            $rules['image'] = 'required|image|mimes:jpeg,jpg,png|max:20480';
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Saat update, image opsional
            $rules['image'] = 'nullable|image|mimes:jpeg,jpg,png|max:20480';
        }

        return $rules;
    }
}
