<?php

namespace App\Http\Requests\Admin;

use App\Enums\JenisPenyedia;
use App\Models\Penyedia;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both creating and updating a scholarship provider. Access is
 * already restricted to admins by the `role:admin` route middleware.
 */
class PenyediaRequest extends FormRequest
{
    /**
     * Normalise whitespace and turn empty optional fields into null.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_penyedia' => $this->string('nama_penyedia')->squish()->toString(),
            'kontak' => $this->string('kontak')->squish()->toString() ?: null,
            'alamat' => $this->string('alamat')->squish()->toString() ?: null,
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
            'nama_penyedia' => [
                'required',
                'string',
                'max:150',
                Rule::unique(Penyedia::class)->ignore($this->route('penyedia')),
            ],
            'jenis' => ['required', Rule::enum(JenisPenyedia::class)],
            'kontak' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:255'],
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
            'nama_penyedia' => 'nama penyedia',
            'jenis' => 'jenis penyedia',
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
            'nama_penyedia.unique' => 'Penyedia dengan nama ini sudah terdaftar.',
        ];
    }
}
