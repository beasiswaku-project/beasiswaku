<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusPendaftaran;
use App\Models\Pendaftaran;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates an admin changing a submitted registration's status. Drafts belong
 * to the student until they are submitted, so their status cannot be changed.
 */
class StatusPendaftaranRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_column(StatusPendaftaran::untukAdmin(), 'value'))],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Pendaftaran $pendaftaran */
                $pendaftaran = $this->route('pendaftaran');

                if ($pendaftaran->isDraft()) {
                    $validator->errors()->add('status', 'Status draf tidak dapat diubah sebelum mahasiswa mengirim pendaftaran.');
                }
            },
        ];
    }
}
