<?php

namespace App\Http\Requests\Admin;

use App\Models\Jenjang;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates both creating and updating an education level. Access is already
 * restricted to admins by the `role:admin` route middleware.
 */
class JenjangRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_jenjang' => $this->string('nama_jenjang')->squish()->toString(),
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
            'nama_jenjang' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Jenjang::class)->ignore($this->route('jenjang')),
            ],
            'keterangan' => ['nullable', 'string', 'max:255'],
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
            'nama_jenjang' => 'nama jenjang',
        ];
    }
}
