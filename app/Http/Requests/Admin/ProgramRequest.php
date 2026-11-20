<?php

namespace App\Http\Requests\Admin;

use App\Models\Jenjang;
use App\Models\Kategori;
use App\Models\Penyedia;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both creating and updating a scholarship program. Access is
 * already restricted to admins by the `role:admin` route middleware.
 */
class ProgramRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'judul' => $this->string('judul')->squish()->toString(),
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
            'judul' => ['required', 'string', 'max:200'],
            'penyedia_id' => ['required', 'integer', Rule::exists(Penyedia::class, 'id')],
            'kategori_id' => ['required', 'integer', Rule::exists(Kategori::class, 'id')],
            'jenjang_id' => ['required', 'integer', Rule::exists(Jenjang::class, 'id')],
            'kuota' => ['required', 'integer', 'between:1,100000'],
            'tgl_buka' => ['required', 'date'],
            'deadline' => ['required', 'date', 'after_or_equal:tgl_buka'],
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
            'penyedia_id' => 'penyedia',
            'kategori_id' => 'kategori',
            'jenjang_id' => 'jenjang sasaran',
            'tgl_buka' => 'tanggal buka',
        ];
    }
}
