<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusVerifikasi;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates an admin's verification of a document. A rejection needs a note
 * so the student knows what to fix.
 */
class VerifikasiDokumenRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'catatan_admin' => $this->string('catatan_admin')->trim()->toString() ?: null,
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
            'status_verifikasi' => ['required', Rule::enum(StatusVerifikasi::class)],
            'catatan_admin' => ['nullable', 'required_if:status_verifikasi,'.StatusVerifikasi::TidakValid->value, 'string', 'max:1000'],
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
            'status_verifikasi' => 'status verifikasi',
            'catatan_admin' => 'catatan',
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
            'catatan_admin.required_if' => 'Tuliskan alasan penolakan agar mahasiswa dapat memperbaiki dokumennya.',
        ];
    }
}
