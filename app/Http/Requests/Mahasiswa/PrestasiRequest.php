<?php

namespace App\Http\Requests\Mahasiswa;

use App\Enums\JenisPrestasi;
use App\Models\Prestasi;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates creating and updating an achievement on the student's own profile.
 * Ownership of an existing entry is checked by PrestasiPolicy.
 */
class PrestasiRequest extends FormRequest
{
    /**
     * Maximum proof file size in kilobytes.
     */
    public const int MAKS_UKURAN_BUKTI_KB = 2048;

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_kegiatan' => $this->string('nama_kegiatan')->squish()->toString(),
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
            'jenis' => ['required', Rule::enum(JenisPrestasi::class)],
            'nama_kegiatan' => ['required', 'string', 'max:150'],
            'tingkat' => ['required', Rule::in(Prestasi::DAFTAR_TINGKAT)],
            'bukti' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:'.self::MAKS_UKURAN_BUKTI_KB],
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
            'nama_kegiatan' => 'nama kegiatan',
            'bukti' => 'file bukti',
        ];
    }
}
