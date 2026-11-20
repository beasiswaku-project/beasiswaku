<?php

namespace App\Http\Requests\Admin;

use App\Enums\JenisPersyaratan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates one program requirement. The value rules depend on the type, e.g.
 * a GPA between 0 and 4 or a document name. Access is already restricted to
 * admins by the `role:admin` route middleware.
 */
class PersyaratanRequest extends FormRequest
{
    /**
     * Accept a comma decimal separator, read the checkbox, and drop the value
     * for types that do not use one.
     */
    protected function prepareForValidation(): void
    {
        $jenis = JenisPersyaratan::tryFrom($this->string('jenis')->toString());
        $nilai = $this->string('nilai')->squish()->toString();

        if (in_array($jenis, [JenisPersyaratan::IpkMinimum, JenisPersyaratan::PenghasilanMaksimum], true)) {
            $nilai = str_replace(',', '.', $nilai);
        }

        $this->merge([
            'wajib' => $this->boolean('wajib'),
            'nilai' => in_array($jenis, [JenisPersyaratan::Kipk, JenisPersyaratan::Wilayah3t], true) || $nilai === '' ? null : $nilai,
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
            'jenis' => ['required', Rule::enum(JenisPersyaratan::class)],
            'nilai' => match (JenisPersyaratan::tryFrom($this->string('jenis')->toString())) {
                JenisPersyaratan::IpkMinimum => ['required', 'decimal:0,2', 'between:0,4'],
                JenisPersyaratan::PenghasilanMaksimum => ['required', 'integer', 'min:0'],
                JenisPersyaratan::SemesterMinimum => ['required', 'integer', 'between:1,14'],
                JenisPersyaratan::Dtsen => ['nullable', 'integer', 'between:1,10'],
                JenisPersyaratan::Dokumen => ['required', 'string', 'max:100'],
                default => ['nullable'],
            },
            'wajib' => ['boolean'],
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
            'jenis' => 'jenis syarat',
            'nilai' => 'nilai syarat',
            'wajib' => 'sifat syarat',
        ];
    }
}
