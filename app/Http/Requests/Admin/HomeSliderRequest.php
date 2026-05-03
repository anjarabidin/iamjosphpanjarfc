<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HomeSliderRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'required|string|max:500',
            'description_en' => 'nullable|string|max:500',
            'button_text' => 'required|string|max:100',
            'button_text_en' => 'nullable|string|max:100',
            'button_link' => 'required|url|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];

        // Media validation based on request type
        if ($this->isMethod('POST')) {
            // For create, media is required
            $rules['media'] = [
                'required',
                'file',
                'mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,wmv,flv',
                'max:51200' // 50MB max
            ];
        } else {
            // For update, media is optional
            $rules['media'] = [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,gif,webp,mp4,avi,mov,wmv,flv',
                'max:51200' // 50MB max
            ];
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul slider wajib diisi.',
            'title.max' => 'Judul slider maksimal 255 karakter.',
            'description.required' => 'Deskripsi slider wajib diisi.',
            'description.max' => 'Deskripsi slider maksimal 500 karakter.',
            'button_text.required' => 'Teks tombol slider wajib diisi.',
            'button_text.max' => 'Teks tombol slider maksimal 100 karakter.',
            'button_link.required' => 'Link tombol slider wajib diisi.',
            'button_link.url' => 'Link tombol slider harus berupa URL yang valid.',
            'media.required' => 'Media slider wajib diupload.',
            'media.file' => 'Media slider harus berupa file.',
            'media.mimes' => 'Media slider harus berupa gambar (jpeg, png, jpg, gif, webp) atau video (mp4, avi, mov, wmv, flv).',
            'media.max' => 'Ukuran media slider maksimal 50MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
