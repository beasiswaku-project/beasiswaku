<?php

namespace App\Http\Requests\Admin;

use App\Models\Wilayah;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both creating and updating a region. Access is already
 * restricted to admins by the `role:admin` route middleware.
 */
class WilayahRequest extends FormRequest
{
    /**
     * Normalise names, read the 3T checkbox as a boolean, and fall back to the
     * default priority score (100 for 3T, 0 otherwise) when none is given.
     */
    protected function prepareForValidation(): void
    {
        $is3t = $this->boolean('is_3t');

        $this->merge([
            'provinsi' => $this->string('provinsi')->squish()->toString(),
            'kabupaten' => $this->string('kabupaten')->squish()->toString(),
            'is_3t' => $is3t,
            'skor_prioritas' => $this->filled('skor_prioritas')
                ? $this->input('skor_prioritas')
                : ($is3t ? Wilayah::DEFAULT_SKOR_3T : Wilayah::DEFAULT_SKOR_NON_3T),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'provinsi' => ['required', 'string', 'max:100'],
            'kabupaten' => [
                'required',
                'string',
                'max:100',
                Rule::unique(Wilayah::class)
                    ->where('provinsi', $this->input('provinsi'))
                    ->ignore($this->route('wilayah')),
            ],
            'is_3t' => ['boolean'],
            'skor_prioritas' => ['required', 'integer', 'between:0,100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kabupaten' => 'kabupaten/kota',
            'is_3t' => 'penanda 3T',
            'skor_prioritas' => 'skor prioritas',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kabupaten.unique' => 'Kabupaten/kota ini sudah terdaftar di provinsi tersebut.',
        ];
    }
}
