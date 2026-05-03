<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BppOrganizationRequest extends FormRequest
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
            'name_en' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:bpp_organizations,id',
            'member_id' => 'nullable|exists:members,id',
            'order' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama jabatan wajib diisi.',
            'name.max' => 'Nama jabatan maksimal 255 karakter.',
            'name_en.max' => 'Nama jabatan (EN) maksimal 255 karakter.',
            'parent_id.exists' => 'Jabatan induk tidak valid.',
            'member_id.exists' => 'Anggota tidak valid.',
            'order.required' => 'Urutan wajib diisi.',
            'order.integer' => 'Urutan harus berupa angka.',
            'order.min' => 'Urutan minimal 0.',
        ];
    }
}
