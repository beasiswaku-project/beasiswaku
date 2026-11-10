<?php

namespace App\Http\Requests\Mahasiswa;

use App\Models\Jenjang;
use App\Models\Wilayah;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates a student filling in or updating their own profile. Access is
 * already restricted to students by the `role:mahasiswa` route middleware.
 */
class ProfilRequest extends FormRequest
{
    /**
     * Normalise text, read checkboxes as booleans, and drop the DTSEN decile
     * when the student is not registered in DTSEN.
     */
    protected function prepareForValidation(): void
    {
        $terdaftarDtsen = $this->boolean('terdaftar_dtsen');

        $this->merge([
            'nama' => $this->string('nama')->squish()->toString(),
            'kampus' => $this->string('kampus')->squish()->toString(),
            'kondisi_khusus' => $this->string('kondisi_khusus')->squish()->toString() ?: null,
            'punya_kipk' => $this->boolean('punya_kipk'),
            'terdaftar_dtsen' => $terdaftarDtsen,
            'desil_dtsen' => $terdaftarDtsen ? $this->input('desil_dtsen') : null,
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
            'no_telepon' => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'kampus' => ['required', 'string', 'max:150'],
            'jenjang_id' => ['required', 'integer', Rule::exists(Jenjang::class, 'id')],
            'wilayah_id' => ['required', 'integer', Rule::exists(Wilayah::class, 'id')],
            'penghasilan_ortu' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'jml_tanggungan' => ['required', 'integer', 'between:1,30'],
            'punya_kipk' => ['boolean'],
            'terdaftar_dtsen' => ['boolean'],
            'desil_dtsen' => ['nullable', 'required_if:terdaftar_dtsen,true', 'integer', 'between:1,10'],
            'kondisi_khusus' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Fields stored on the `users` table rather than the profile.
     *
     * @return array{nama: string, no_telepon: string}
     */
    public function identitas(): array
    {
        return $this->safe()->only(['nama', 'no_telepon']);
    }

    /**
     * Fields stored on the `mahasiswa` profile.
     *
     * @return array<string, mixed>
     */
    public function profil(): array
    {
        return $this->safe()->except(['nama', 'no_telepon']);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'no_telepon' => 'nomor telepon',
            'jenjang_id' => 'jenjang',
            'wilayah_id' => 'wilayah asal',
            'penghasilan_ortu' => 'penghasilan orang tua',
            'jml_tanggungan' => 'jumlah tanggungan',
            'punya_kipk' => 'status KIP-K',
            'terdaftar_dtsen' => 'status DTSEN',
            'desil_dtsen' => 'desil DTSEN',
            'kondisi_khusus' => 'kondisi khusus',
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
            'no_telepon.regex' => 'Nomor telepon harus diawali 08, 628, atau +628.',
            'desil_dtsen.required_if' => 'Desil DTSEN wajib diisi jika terdaftar di DTSEN.',
        ];
    }
}
