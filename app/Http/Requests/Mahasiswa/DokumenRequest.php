<?php

namespace App\Http\Requests\Mahasiswa;

use App\Models\Dokumen;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates uploading a registration document (POST) or replacing its file
 * (PUT). Files must be PDF, JPG, or PNG of at most 2 MB. Ownership is checked
 * before validation so another student's registration is never probed.
 */
class DokumenRequest extends FormRequest
{
    /**
     * Maximum document size in kilobytes.
     */
    public const int MAKS_UKURAN_KB = 2048;

    /**
     * Only the owner may upload to a registration or replace its document.
     */
    public function authorize(): bool
    {
        return $this->isMethod('post')
            ? $this->user()->can('update', $this->route('pendaftaran'))
            : $this->user()->can('update', $this->route('dokumen'));
    }

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('post')) {
            $this->merge(['jenis_dokumen' => $this->string('jenis_dokumen')->squish()->toString()]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:'.self::MAKS_UKURAN_KB],
        ];

        if ($this->isMethod('post')) {
            $rules['jenis_dokumen'] = [
                'required',
                'string',
                'max:100',
                Rule::unique(Dokumen::class)->where('pendaftaran_id', $this->route('pendaftaran')->id),
            ];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'jenis_dokumen' => 'nama dokumen',
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
            'jenis_dokumen.unique' => 'Dokumen ini sudah diunggah. Ganti filenya dari daftar dokumen.',
        ];
    }
}
