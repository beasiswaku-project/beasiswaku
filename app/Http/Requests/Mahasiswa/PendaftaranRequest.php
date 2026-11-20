<?php

namespace App\Http\Requests\Mahasiswa;

use App\Models\Program;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates a student registering for a program. A registration is rejected
 * when the program is not open, the student already registered for it, the
 * profile is incomplete, or a mandatory requirement is not met.
 */
class PendaftaranRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'program_id' => ['required', 'integer', Rule::exists(Program::class, 'id')],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $profil = $this->user()->mahasiswa?->load(['wilayah', 'riwayatPendidikan']);
                $program = Program::query()->with('persyaratan')->find($this->integer('program_id'));

                $kendala = match (true) {
                    $profil === null || ! $profil->isLengkap() => 'Lengkapi profil dan riwayat IPK terlebih dahulu sebelum mendaftar.',
                    ! $program->isTerbuka() => 'Program ini sudah ditutup atau belum dibuka.',
                    $profil->pendaftaran()->where('program_id', $program->id)->exists() => 'Kamu sudah terdaftar pada program ini.',
                    ! $program->hasilPencocokan($profil)['memenuhi'] => 'Profilmu belum memenuhi jenjang atau syarat wajib program ini.',
                    default => null,
                };

                if ($kendala) {
                    $validator->errors()->add('program_id', $kendala);
                }
            },
        ];
    }
}
