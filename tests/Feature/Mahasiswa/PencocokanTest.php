<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\JenisPersyaratan;
use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\Persyaratan;
use App\Models\Program;
use App\Models\RiwayatPendidikan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PencocokanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mahasiswa $profil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->profil = Mahasiswa::factory()->for($this->user)->for(Wilayah::factory())->create([
            'penghasilan_ortu' => 3_000_000,
            'jml_tanggungan' => 4,
        ]);
        RiwayatPendidikan::factory()->for($this->profil)->create([
            'jenjang_id' => $this->profil->jenjang_id,
            'semester' => 4,
            'ipk' => 3.40,
        ]);
    }

    private function programUntukJenjangSaya(array $attributes = []): Program
    {
        return Program::factory()->dibuka()->create(['jenjang_id' => $this->profil->jenjang_id, ...$attributes]);
    }

    private function syarat(Program $program, JenisPersyaratan $jenis, ?string $nilai = null, bool $wajib = true): void
    {
        Persyaratan::factory()->for($program)->create(['jenis' => $jenis, 'nilai' => $nilai, 'wajib' => $wajib]);
    }

    public function test_proposal_example_scores_four_of_five_requirements(): void
    {
        $profil = Mahasiswa::factory()->terdaftarDtsen(3)->for(Wilayah::factory())->create(['penghasilan_ortu' => 3_500_000]);
        RiwayatPendidikan::factory()->for($profil)->create(['jenjang_id' => $profil->jenjang_id, 'ipk' => 3.20]);
        $program = Program::factory()->dibuka()->create(['jenjang_id' => $profil->jenjang_id]);
        $this->syarat($program, JenisPersyaratan::IpkMinimum, '3.00');
        $this->syarat($program, JenisPersyaratan::PenghasilanMaksimum, '4000000');
        $this->syarat($program, JenisPersyaratan::SemesterMinimum, '1');
        $this->syarat($program, JenisPersyaratan::Dtsen, '4', wajib: false);
        $this->syarat($program, JenisPersyaratan::Wilayah3t, wajib: false);
        Persyaratan::factory()->for($program)->dokumen('SKTM')->create();

        $hasil = $program->load('persyaratan')->hasilPencocokan($profil->load(['wilayah', 'riwayatPendidikan']));

        $this->assertTrue($hasil['memenuhi']);
        $this->assertSame(80, $hasil['persentase']);
        $this->assertSame(4, $hasil['terpenuhi']);
        $this->assertSame(5, $hasil['total']);
    }

    public function test_programs_failing_a_mandatory_requirement_are_hidden(): void
    {
        $cocok = $this->programUntukJenjangSaya(['judul' => 'Beasiswa Cocok']);
        $this->syarat($cocok, JenisPersyaratan::IpkMinimum, '3.00');

        $gagal = $this->programUntukJenjangSaya(['judul' => 'Beasiswa IPK Tinggi']);
        $this->syarat($gagal, JenisPersyaratan::IpkMinimum, '3.80');

        $this->actingAs($this->user)
            ->get(route('mahasiswa.pencocokan'))
            ->assertOk()
            ->assertSeeText('Beasiswa Cocok')
            ->assertSeeText('100% sesuai')
            ->assertDontSeeText('Beasiswa IPK Tinggi');
    }

    public function test_unmet_preference_lowers_the_percentage_without_hiding_the_program(): void
    {
        $program = $this->programUntukJenjangSaya(['judul' => 'Beasiswa KIP-K']);
        $this->syarat($program, JenisPersyaratan::IpkMinimum, '3.00');
        $this->syarat($program, JenisPersyaratan::Kipk, wajib: false);

        $this->actingAs($this->user)
            ->get(route('mahasiswa.pencocokan'))
            ->assertSeeText('Beasiswa KIP-K')
            ->assertSeeText('50% sesuai');
    }

    public function test_programs_for_another_level_or_not_open_are_skipped(): void
    {
        Program::factory()->dibuka()->for(Jenjang::factory())->create(['judul' => 'Beasiswa Jenjang Lain']);
        $this->programUntukJenjangSaya(['judul' => 'Beasiswa Draf', 'status' => 'draft']);

        $this->actingAs($this->user)
            ->get(route('mahasiswa.pencocokan'))
            ->assertDontSeeText('Beasiswa Jenjang Lain')
            ->assertDontSeeText('Beasiswa Draf');
    }

    public function test_results_are_sorted_by_percentage_then_deadline_with_open_to_all_last(): void
    {
        $umum = $this->programUntukJenjangSaya(['judul' => 'Beasiswa Umum', 'deadline' => today()->addDays(2)]);
        Persyaratan::factory()->for($umum)->dokumen()->create();

        $limaPuluh = $this->programUntukJenjangSaya(['judul' => 'Beasiswa Lima Puluh', 'deadline' => today()->addDays(3)]);
        $this->syarat($limaPuluh, JenisPersyaratan::IpkMinimum, '3.00');
        $this->syarat($limaPuluh, JenisPersyaratan::Kipk, wajib: false);

        $penuhJauh = $this->programUntukJenjangSaya(['judul' => 'Beasiswa Penuh Jauh', 'deadline' => today()->addDays(30)]);
        $this->syarat($penuhJauh, JenisPersyaratan::IpkMinimum, '3.00');

        $penuhDekat = $this->programUntukJenjangSaya(['judul' => 'Beasiswa Penuh Dekat', 'deadline' => today()->addDays(5)]);
        $this->syarat($penuhDekat, JenisPersyaratan::PenghasilanMaksimum, '5000000');

        $this->actingAs($this->user)
            ->get(route('mahasiswa.pencocokan'))
            ->assertSeeTextInOrder(['Beasiswa Penuh Dekat', 'Beasiswa Penuh Jauh', 'Beasiswa Lima Puluh', 'Beasiswa Umum'])
            ->assertSeeText('Terbuka umum');
    }

    public function test_student_without_profile_is_asked_to_fill_it(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('mahasiswa.pencocokan'))
            ->assertOk()
            ->assertSeeText('Isi profil terlebih dahulu');
    }

    public function test_program_detail_marks_which_requirements_the_student_meets(): void
    {
        $program = $this->programUntukJenjangSaya();
        $this->syarat($program, JenisPersyaratan::IpkMinimum, '3.00');
        $this->syarat($program, JenisPersyaratan::Kipk, wajib: false);

        $this->actingAs($this->user)
            ->get(route('beasiswa.show', $program))
            ->assertOk()
            ->assertSeeTextInOrder(['IPK minimum 3,00', 'terpenuhi', 'Penerima KIP Kuliah', 'belum terpenuhi']);
    }
}
