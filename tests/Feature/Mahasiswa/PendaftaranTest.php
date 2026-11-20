<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\JenisPersyaratan;
use App\Enums\StatusPendaftaran;
use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\Persyaratan;
use App\Models\Program;
use App\Models\RiwayatPendidikan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendaftaranTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mahasiswa $profil;

    private Program $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->profil = Mahasiswa::factory()->for($this->user)->penerimaKipk()->create([
            'penghasilan_ortu' => 2_500_000,
            'jml_tanggungan' => 4,
        ]);
        RiwayatPendidikan::factory()->for($this->profil)->create(['jenjang_id' => $this->profil->jenjang_id, 'semester' => 3, 'ipk' => 3.40]);
        $this->program = Program::factory()->dibuka()->create(['jenjang_id' => $this->profil->jenjang_id]);
    }

    public function test_student_can_start_a_draft_registration(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.store'), ['program_id' => $this->program->id]);

        $pendaftaran = Pendaftaran::sole();
        $response->assertRedirect(route('mahasiswa.pendaftaran.show', $pendaftaran));
        $this->assertSame($this->profil->id, $pendaftaran->mahasiswa_id);
        $this->assertSame(StatusPendaftaran::Draft, $pendaftaran->status);
        $this->assertNull($pendaftaran->snapshot_ekonomi);
    }

    public function test_registration_is_rejected_for_a_duplicate(): void
    {
        Pendaftaran::factory()->for($this->profil)->for($this->program)->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.store'), ['program_id' => $this->program->id])
            ->assertSessionHasErrors(['program_id' => 'Kamu sudah terdaftar pada program ini.']);

        $this->assertDatabaseCount('pendaftaran', 1);
    }

    public function test_registration_is_rejected_for_a_program_that_is_not_open(): void
    {
        $tutup = Program::factory()->ditutup()->create(['jenjang_id' => $this->profil->jenjang_id]);

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.store'), ['program_id' => $tutup->id])
            ->assertSessionHasErrors(['program_id' => 'Program ini sudah ditutup atau belum dibuka.']);
    }

    public function test_registration_is_rejected_when_the_profile_is_incomplete(): void
    {
        $tanpaIpk = Mahasiswa::factory()->create(['jenjang_id' => $this->profil->jenjang_id]);

        $this->actingAs($tanpaIpk->user)
            ->post(route('mahasiswa.pendaftaran.store'), ['program_id' => $this->program->id])
            ->assertSessionHasErrors('program_id');

        $this->actingAs(User::factory()->create())
            ->post(route('mahasiswa.pendaftaran.store'), ['program_id' => $this->program->id])
            ->assertSessionHasErrors('program_id');

        $this->assertDatabaseCount('pendaftaran', 0);
    }

    public function test_registration_is_rejected_when_a_mandatory_requirement_is_not_met(): void
    {
        Persyaratan::factory()->for($this->program)->create(['jenis' => JenisPersyaratan::IpkMinimum, 'nilai' => '3.80']);

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.store'), ['program_id' => $this->program->id])
            ->assertSessionHasErrors('program_id');
    }

    public function test_submitting_saves_a_snapshot_of_economic_data_and_gpa(): void
    {
        $pendaftaran = Pendaftaran::factory()->for($this->profil)->for($this->program)->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.kirim', $pendaftaran))
            ->assertRedirect(route('mahasiswa.pendaftaran.show', $pendaftaran))
            ->assertSessionHas('success');

        $pendaftaran->refresh();
        $this->assertSame(StatusPendaftaran::Dikirim, $pendaftaran->status);
        $this->assertNotNull($pendaftaran->tgl_kirim);
        $this->assertEquals(2_500_000, $pendaftaran->snapshot_ekonomi['penghasilan_ortu']);
        $this->assertSame(4, $pendaftaran->snapshot_ekonomi['jml_tanggungan']);
        $this->assertTrue($pendaftaran->snapshot_ekonomi['punya_kipk']);
        $this->assertEquals(3.4, $pendaftaran->snapshot_ekonomi['ipk_terakhir']);
    }

    public function test_later_profile_changes_do_not_alter_the_snapshot(): void
    {
        $pendaftaran = Pendaftaran::factory()->for($this->profil)->for($this->program)->create();
        $this->actingAs($this->user)->post(route('mahasiswa.pendaftaran.kirim', $pendaftaran));

        $this->profil->update(['penghasilan_ortu' => 9_000_000]);

        $this->assertEquals(2_500_000, $pendaftaran->refresh()->snapshot_ekonomi['penghasilan_ortu']);
    }

    public function test_registration_cannot_be_submitted_twice_or_after_the_deadline(): void
    {
        $terkirim = Pendaftaran::factory()->for($this->profil)->for($this->program)->dikirim()->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.kirim', $terkirim))
            ->assertSessionHas('error');

        $lewat = Program::factory()->dibuka()->create(['jenjang_id' => $this->profil->jenjang_id, 'tgl_buka' => today()->subMonth(), 'deadline' => today()->subDay()]);
        $draf = Pendaftaran::factory()->for($this->profil)->for($lewat)->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.kirim', $draf))
            ->assertSessionHas('error');

        $this->assertSame(StatusPendaftaran::Draft, $draf->refresh()->status);
    }

    public function test_student_can_cancel_before_the_deadline(): void
    {
        $pendaftaran = Pendaftaran::factory()->for($this->profil)->for($this->program)->dikirim()->create();

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.pendaftaran.destroy', $pendaftaran))
            ->assertRedirect(route('mahasiswa.pendaftaran.index'));

        $this->assertModelMissing($pendaftaran);
    }

    public function test_registration_being_processed_cannot_be_cancelled(): void
    {
        $pendaftaran = Pendaftaran::factory()->for($this->profil)->for($this->program)->create(['status' => StatusPendaftaran::Verifikasi]);

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.pendaftaran.destroy', $pendaftaran))
            ->assertSessionHas('error');

        $this->assertModelExists($pendaftaran);
    }

    public function test_student_sees_only_their_own_registrations(): void
    {
        Pendaftaran::factory()->for($this->profil)->for($this->program)->create();
        $lain = Pendaftaran::factory()->create();

        $this->actingAs($this->user)
            ->get(route('mahasiswa.pendaftaran.index'))
            ->assertOk()
            ->assertSeeText($this->program->judul)
            ->assertDontSeeText($lain->program->judul);

        $this->actingAs($this->user)->get(route('mahasiswa.pendaftaran.show', $lain))->assertForbidden();
        $this->actingAs($this->user)->post(route('mahasiswa.pendaftaran.kirim', $lain))->assertForbidden();
        $this->actingAs($this->user)->delete(route('mahasiswa.pendaftaran.destroy', $lain))->assertForbidden();
    }

    public function test_program_detail_offers_registration_or_links_to_the_existing_one(): void
    {
        $this->actingAs($this->user)
            ->get(route('beasiswa.show', $this->program))
            ->assertSeeText('Daftar beasiswa ini');

        Pendaftaran::factory()->for($this->profil)->for($this->program)->create();

        $this->actingAs($this->user)
            ->get(route('beasiswa.show', $this->program))
            ->assertSeeText('Lihat pendaftaranku')
            ->assertDontSeeText('Daftar beasiswa ini');
    }
}
