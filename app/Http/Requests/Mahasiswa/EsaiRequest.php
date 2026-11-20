<?php

namespace App\Http\Requests\Mahasiswa;

use App\Models\Esai;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates writing (POST) or editing (PUT) the motivation essay. Ownership is
 * checked before validation.
 */
class EsaiRequest extends FormRequest
{
    /**
     * Only the owner may write the essay of a registration.
     */
    public function authorize(): bool
    {
        return $this->isMethod('post')
            ? $this->user()->can('update', $this->route('pendaftaran'))
            : $this->user()->can('update', $this->route('esai'));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'judul' => $this->string('judul')->squish()->toString() ?: null,
            'isi' => $this->string('isi')->trim()->toString(),
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
            'judul' => ['nullable', 'string', 'max:200'],
            'isi' => ['required', 'string', 'min:'.Esai::PANJANG_MINIMUM, 'max:10000'],
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
            'isi' => 'isi esai',
        ];
    }
}
