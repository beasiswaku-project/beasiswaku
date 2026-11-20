<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusProgram;
use App\Models\Program;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates changing a program's status. A program can only be opened while
 * its deadline has not passed, so students never see an expired program.
 */
class StatusProgramRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(StatusProgram::class)],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Program $program */
                $program = $this->route('program');

                if ($this->input('status') === StatusProgram::Dibuka->value && $program->deadline?->lt(today())) {
                    $validator->errors()->add('status', 'Program tidak dapat dibuka karena deadline sudah lewat. Ubah deadline terlebih dahulu.');
                }
            },
        ];
    }
}
