<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeMemberRequest extends FormRequest
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
    public function rules()
    {
        // atur berdasarkan method
        if ($this->isMethod('post')) {
            return [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:51200', // maksimal 5MB
                'link' => 'nullable|url',
                'order' => 'nullable|integer|min:0',
            ];
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:51200',
                'link' => 'nullable|url',
                'order' => 'nullable|integer|min:0',
            ];
        }
    }
}
