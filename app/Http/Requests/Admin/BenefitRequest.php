<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BenefitRequest extends FormRequest
{
    private const REQUIRED_STRING_VALIDATION = 'required|string|max:255';
    private const NULLABLE_STRING_VALIDATION = 'nullable|string|max:255';
    private const IMAGE_VALIDATION = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
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
            'title' => self::REQUIRED_STRING_VALIDATION,
            'title_en' => self::NULLABLE_STRING_VALIDATION,
            'subtitle' => 'required|string',
            'subtitle_en' => 'nullable|string',
            'button_text' => self::NULLABLE_STRING_VALIDATION,
            'button_text_en' => self::NULLABLE_STRING_VALIDATION,
            'url' => 'nullable|url',
            'order' => 'required|integer',
            'image' => self::IMAGE_VALIDATION,
            'image_2' => self::IMAGE_VALIDATION,
            'image_3' => self::IMAGE_VALIDATION,
        ];
    }
}
