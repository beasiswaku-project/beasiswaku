<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\StatusEsai;
use App\Enums\StatusPendaftaran;
use App\Models\Esai;
use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\Program;
use App\Models\RiwayatPendidikan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EsaiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Pendaftaran $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $profil = Mahasiswa::factory()->for($this->user)->create();
        RiwayatPendidikan::factory()->for($profil)->create(['jenjang_id' => $profil->jenjang_id]);
        $program = Program::factory()->dibuka()->create(['jenjang_id' => $profil->jenjang_id]);
        $this->pendaftaran = Pendaftaran::factory()->for($profil)->for($program)->create();
    }

    private function isiEsai(): string
    {
        return str_repeat('Saya ingin melanjutkan kuliah untuk membantu keluarga. ', 5);
    }

    public function test_student_sees_an_empty_essay_editor(): void
    {
        $this->actingAs($this->user)
            ->get(route('mahasiswa.pendaftaran.esai.index', $this->pendaftaran))
            ->assertOk()
            ->assertSeeText('Esai motivasi')
            ->assertSeeText('Simpan draf');
    }

    public function test_student_can_write_a_draft_essay(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.esai.store', $this->pendaftaran), ['judul' => 'Mimpi saya', 'isi' => $this->isiEsai()])
            ->assertRedirect(route('mahasiswa.pendaftaran.esai.index', $this->pendaftaran))
            ->assertSessionHas('success');

        $esai = Esai::sole();
        $this->assertSame($this->pendaftaran->id, $esai->pendaftaran_id);
        $this->assertSame(StatusEsai::Draft, $esai->status);
        $this->assertSame('Mimpi saya', $esai->judul);
    }

    public function test_essay_must_meet_the_minimum_length(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.esai.store', $this->pendaftaran), ['isi' => 'Terlalu pendek.'])
            ->assertSessionHasErrors('isi');

        $this->assertDatabaseCount('esai', 0);
    }

    public function test_a_registration_has_only_one_essay(): void
    {
        Esai::factory()->for($this->pendaftaran)->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.esai.store', $this->pendaftaran), ['isi' => $this->isiEsai()])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('esai', 1);
    }

    public function test_student_can_edit_and_delete_a_draft_essay(): void
    {
        $esai = Esai::factory()->for($this->pendaftaran)->create();

        $this->actingAs($this->user)
            ->put(route('mahasiswa.esai.update', $esai), ['judul' => 'Judul baru', 'isi' => $this->isiEsai()])
            ->assertSessionHas('success');
        $this->assertSame('Judul baru', $esai->refresh()->judul);

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.esai.destroy', $esai))
            ->assertSessionHas('success');
        $this->assertModelMissing($esai);
    }

    public function test_registration_cannot_be_submitted_without_an_essay(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.kirim', $this->pendaftaran))
            ->assertSessionHas('error');

        $this->assertSame(StatusPendaftaran::Draft, $this->pendaftaran->refresh()->status);
    }

    public function test_submitting_the_registration_locks_the_essay(): void
    {
        $esai = Esai::factory()->for($this->pendaftaran)->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.kirim', $this->pendaftaran))
            ->assertSessionHas('success');

        $this->assertSame(StatusEsai::Dikirim, $esai->refresh()->status);

        $this->actingAs($this->user)
            ->put(route('mahasiswa.esai.update', $esai), ['isi' => $this->isiEsai()])
            ->assertSessionHas('error');
        $this->actingAs($this->user)
            ->delete(route('mahasiswa.esai.destroy', $esai))
            ->assertSessionHas('error');

        $this->assertModelExists($esai);

        $this->actingAs($this->user)
            ->get(route('mahasiswa.pendaftaran.esai.index', $this->pendaftaran))
            ->assertOk()
            ->assertSee(e($esai->isi), false)
            ->assertDontSeeText('Simpan draf');
    }

    public function test_student_cannot_touch_another_students_essay(): void
    {
        $lain = Mahasiswa::factory()->create();
        $esai = Esai::factory()->for($this->pendaftaran)->create();

        $this->actingAs($lain->user)->get(route('mahasiswa.pendaftaran.esai.index', $this->pendaftaran))->assertForbidden();
        $this->actingAs($lain->user)->post(route('mahasiswa.pendaftaran.esai.store', $this->pendaftaran), ['isi' => $this->isiEsai()])->assertForbidden();
        $this->actingAs($lain->user)->put(route('mahasiswa.esai.update', $esai), ['isi' => $this->isiEsai()])->assertForbidden();
        $this->actingAs($lain->user)->delete(route('mahasiswa.esai.destroy', $esai))->assertForbidden();

        $this->assertModelExists($esai);
    }

    public function test_admin_can_read_a_submitted_essay(): void
    {
        $pendaftaran = Pendaftaran::factory()->dikirim()->create();
        Esai::factory()->for($pendaftaran)->dikirim()->create(['judul' => 'Harapan untuk keluarga']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.pendaftaran.esai', $pendaftaran))
            ->assertOk()
            ->assertSeeText('Harapan untuk keluarga');

        $this->actingAs($this->user)
            ->get(route('admin.pendaftaran.esai', $pendaftaran))
            ->assertForbidden();
    }
}
