<?php

namespace App\Http\Requests\Mahasiswa;

use App\Models\Jenjang;
use App\Models\RiwayatPendidikan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates creating and correcting a GPA entry on the student's own profile.
 * Ownership of an existing entry is checked by RiwayatPendidikanPolicy.
 */
class RiwayatPendidikanRequest extends FormRequest
{
    /**
     * Accept a comma as the decimal separator, e.g. "3,75".
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('ipk')) {
            $this->merge(['ipk' => str_replace(',', '.', $this->string('ipk')->trim()->toString())]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'jenjang_id' => ['required', 'integer', Rule::exists(Jenjang::class, 'id')],
            'semester' => [
                'required',
                'integer',
                'between:1,14',
                Rule::unique(RiwayatPendidikan::class)
                    ->where('mahasiswa_id', $this->user()->mahasiswa?->id)
                    ->where('jenjang_id', $this->input('jenjang_id'))
                    ->ignore($this->route('riwayatPendidikan')),
            ],
            'ipk' => ['required', 'decimal:0,2', 'between:0,4'],
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
            'jenjang_id' => 'jenjang',
            'ipk' => 'IPK',
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
            'semester.unique' => 'IPK untuk semester ini pada jenjang tersebut sudah dicatat.',
        ];
    }
}
