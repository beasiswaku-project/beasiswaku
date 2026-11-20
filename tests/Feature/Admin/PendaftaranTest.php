<?php

namespace Tests\Feature\Admin;

use App\Enums\StatusPendaftaran;
use App\Models\Pendaftaran;
use App\Models\Prestasi;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendaftaranTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_sees_submitted_registrations_but_not_drafts(): void
    {
        $terkirim = Pendaftaran::factory()->dikirim()->create();
        $draf = Pendaftaran::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.index'))
            ->assertOk()
            ->assertSeeText($terkirim->mahasiswa->user->nama)
            ->assertDontSeeText($draf->mahasiswa->user->nama);

        $this->actingAs($this->admin)->get(route('admin.pendaftaran.show', $draf))->assertNotFound();
    }

    public function test_registrations_can_be_filtered_by_program_and_status(): void
    {
        $program = Program::factory()->create();
        $cocok = Pendaftaran::factory()->dikirim()->for($program)->create();
        $lain = Pendaftaran::factory()->dikirim()->create(['status' => StatusPendaftaran::Lolos]);

        $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.index', ['program' => $program->id]))
            ->assertSeeText($cocok->mahasiswa->user->nama)
            ->assertDontSeeText($lain->mahasiswa->user->nama);

        $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.index', ['status' => 'lolos']))
            ->assertSeeText($lain->mahasiswa->user->nama)
            ->assertDontSeeText($cocok->mahasiswa->user->nama);
    }

    public function test_admin_can_view_registration_snapshot(): void
    {
        $pendaftaran = Pendaftaran::factory()->dikirim()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.show', $pendaftaran))
            ->assertOk()
            ->assertSeeText('Data saat pendaftaran dikirim')
            ->assertSeeText('Rp 2.000.000');
    }

    public function test_admin_can_read_the_applicants_achievements(): void
    {
        $pendaftaran = Pendaftaran::factory()->dikirim()->create();
        Prestasi::factory()->for($pendaftaran->mahasiswa)->create(['nama_kegiatan' => 'Juara Debat Nasional']);

        $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.prestasi', $pendaftaran))
            ->assertOk()
            ->assertSeeText('Juara Debat Nasional');
    }

    public function test_admin_can_change_the_status_of_a_submitted_registration(): void
    {
        $pendaftaran = Pendaftaran::factory()->dikirim()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.pendaftaran.status', $pendaftaran), ['status' => 'verifikasi'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame(StatusPendaftaran::Verifikasi, $pendaftaran->refresh()->status);
    }

    public function test_admin_cannot_set_draft_or_change_a_draft(): void
    {
        $terkirim = Pendaftaran::factory()->dikirim()->create();
        $draf = Pendaftaran::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.pendaftaran.status', $terkirim), ['status' => 'draft'])
            ->assertSessionHasErrors('status');

        $this->actingAs($this->admin)
            ->patch(route('admin.pendaftaran.status', $draf), ['status' => 'verifikasi'])
            ->assertSessionHasErrors('status');

        $this->assertSame(StatusPendaftaran::Draft, $draf->refresh()->status);
    }

    public function test_student_cannot_change_registration_status(): void
    {
        $pendaftaran = Pendaftaran::factory()->dikirim()->create();

        $this->actingAs($pendaftaran->mahasiswa->user)
            ->patch(route('admin.pendaftaran.status', $pendaftaran), ['status' => 'diterima'])
            ->assertForbidden();
    }
}
