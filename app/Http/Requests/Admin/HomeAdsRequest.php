<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class HomeAdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Normalisasi nek UI ngirim d-m-Y
    protected function prepareForValidation(): void
    {
        $toYmd = function ($v) {
            if (is_string($v) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $v)) {
                return Carbon::createFromFormat('d-m-Y', $v)->format('Y-m-d');
            }
            return $v;
        };

        $this->merge([
            'start_date' => $toYmd($this->input('start_date')),
            'end_date'   => $toYmd($this->input('end_date')),
            'is_active'  => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'media'      => [$isCreate ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,gif,svg,webp,mp4,webm,mov', 'max:102400'],
            'link'       => ['nullable', 'url', 'max:255'],
            'order'      => ['nullable', 'integer', 'min:0'],
            'is_active'  => ['required', 'boolean'], // wis di-coerce ing prepareForValidation
            // Paksa format Y-m-d supaya deterministic
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date'   => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }
}
