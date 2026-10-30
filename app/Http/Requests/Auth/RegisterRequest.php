<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->string('email')->trim()->lower()->toString(),
            'no_telepon' => preg_replace('/[\s-]/', '', $this->string('no_telepon')->toString()),
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
            'nama' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'digits:16', Rule::unique(User::class)],
            'email' => ['required', 'string', 'email', 'max:100', Rule::unique(User::class)],
            'no_telepon' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['accepted'],
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
            'nama' => 'nama lengkap',
            'nik' => 'NIK',
            'no_telepon' => 'nomor telepon',
            'terms' => 'syarat dan kebijakan privasi',
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
            'no_telepon.regex' => 'Format nomor telepon tidak valid, contoh: 081234567890.',
        ];
    }
}
