<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PermissionRequest extends FormRequest
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
        $base = ['label' => ['nullable', 'string', 'max:255']];

        if ($this->isMethod('post')) {
            return $base + [
                'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            ];
        }

        $id = $this->route('permission') ?? $this->route('id');
        if (is_object($id)) $id = $id->id;

        return $base + [
            'name' => ['required', 'string', 'max:255', Rule::unique('permissions', 'name')->ignore($id)],
        ];
    }
}
