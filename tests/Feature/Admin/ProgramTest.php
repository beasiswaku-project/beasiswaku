<?php

namespace Tests\Feature\Admin;

use App\Enums\StatusProgram;
use App\Models\Jenjang;
use App\Models\Kategori;
use App\Models\KonfigurasiPenilaian;
use App\Models\Pendaftaran;
use App\Models\Penyedia;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'judul' => 'Beasiswa Prestasi Nusantara',
            'penyedia_id' => Penyedia::factory()->create()->id,
            'kategori_id' => Kategori::factory()->create()->id,
            'jenjang_id' => Jenjang::factory()->create()->id,
            'kuota' => '50',
            'tgl_buka' => today()->toDateString(),
            'deadline' => today()->addMonth()->toDateString(),
            ...$overrides,
        ];
    }

    public function test_admin_sees_every_program_including_drafts(): void
    {
        Program::factory()->create(['judul' => 'Program Draf']);
        Program::factory()->dibuka()->create(['judul' => 'Program Dibuka']);

        $this->actingAs($this->admin)
            ->get(route('admin.program.index'))
            ->assertOk()
            ->assertSeeText('Program Draf')
            ->assertSeeText('Program Dibuka');
    }

    public function test_programs_can_be_filtered_by_status_and_category(): void
    {
        $kategori = Kategori::factory()->create();
        Program::factory()->dibuka()->for($kategori)->create(['judul' => 'Program Dibuka']);
        Program::factory()->create(['judul' => 'Program Draf']);

        $this->actingAs($this->admin)
            ->get(route('admin.program.index', ['status' => 'dibuka']))
            ->assertSeeText('Program Dibuka')
            ->assertDontSeeText('Program Draf');

        $this->actingAs($this->admin)
            ->get(route('admin.program.index', ['kategori' => $kategori->id]))
            ->assertSeeText('Program Dibuka')
            ->assertDontSeeText('Program Draf');
    }

    public function test_new_program_is_a_draft_linked_to_the_newest_configuration(): void
    {
        KonfigurasiPenilaian::factory()->create(['versi' => 1]);
        $terbaru = KonfigurasiPenilaian::factory()->create(['versi' => 2]);

        $this->actingAs($this->admin)
            ->post(route('admin.program.store'), $this->validPayload())
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $program = Program::sole();
        $this->assertSame(StatusProgram::Draft, $program->status);
        $this->assertSame($terbaru->id, $program->konfigurasi_id);
        $this->assertSame('Beasiswa Prestasi Nusantara', $program->judul);
    }

    public function test_first_program_creates_the_default_configuration(): void
    {
        $this->actingAs($this->admin)->post(route('admin.program.store'), $this->validPayload());

        $konfigurasi = KonfigurasiPenilaian::sole();
        $this->assertSame(1, $konfigurasi->versi);
        $this->assertSame(KonfigurasiPenilaian::BOBOT_AWAL, $konfigurasi->bobot);
        $this->assertSame($konfigurasi->id, Program::sole()->konfigurasi_id);
    }

    public function test_status_and_configuration_cannot_be_set_through_the_form(): void
    {
        $konfigurasi = KonfigurasiPenilaian::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.program.store'), $this->validPayload([
            'status' => 'dibuka',
            'konfigurasi_id' => 999,
        ]));

        $program = Program::sole();
        $this->assertSame(StatusProgram::Draft, $program->status);
        $this->assertSame($konfigurasi->id, $program->konfigurasi_id);
    }

    public function test_deadline_must_not_be_before_the_opening_date(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.program.store'), $this->validPayload([
                'tgl_buka' => today()->toDateString(),
                'deadline' => today()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('deadline');
    }

    public function test_admin_can_view_and_update_a_program(): void
    {
        $program = Program::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.program.show', $program))
            ->assertOk()
            ->assertSeeText($program->judul);

        $this->actingAs($this->admin)
            ->put(route('admin.program.update', $program), $this->validPayload(['judul' => 'Judul Baru', 'kuota' => '75']))
            ->assertSessionHasNoErrors();

        $this->assertSame('Judul Baru', $program->refresh()->judul);
        $this->assertSame(75, $program->kuota);
    }

    public function test_admin_can_open_and_close_a_program(): void
    {
        $program = Program::factory()->create(['deadline' => today()->addWeek()]);

        $this->actingAs($this->admin)
            ->patch(route('admin.program.status', $program), ['status' => 'dibuka'])
            ->assertSessionHasNoErrors();
        $this->assertSame(StatusProgram::Dibuka, $program->refresh()->status);

        $this->actingAs($this->admin)
            ->patch(route('admin.program.status', $program), ['status' => 'ditutup'])
            ->assertSessionHasNoErrors();
        $this->assertSame(StatusProgram::Ditutup, $program->refresh()->status);
    }

    public function test_program_past_its_deadline_cannot_be_opened(): void
    {
        $program = Program::factory()->create(['tgl_buka' => today()->subMonth(), 'deadline' => today()->subDay()]);

        $this->actingAs($this->admin)
            ->patch(route('admin.program.status', $program), ['status' => 'dibuka'])
            ->assertSessionHasErrors('status');

        $this->assertSame(StatusProgram::Draft, $program->refresh()->status);
    }

    public function test_program_without_registrations_can_be_deleted(): void
    {
        $program = Program::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.program.destroy', $program))
            ->assertRedirect(route('admin.program.index'));

        $this->assertModelMissing($program);
    }

    public function test_program_with_registrations_cannot_be_deleted(): void
    {
        $program = Program::factory()->create();
        Pendaftaran::factory()->for($program)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.program.destroy', $program))
            ->assertSessionHas('error');

        $this->assertModelExists($program);
    }

    public function test_student_cannot_manage_programs(): void
    {
        $program = Program::factory()->create();
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('admin.program.index'))->assertForbidden();
        $this->actingAs($student)->patch(route('admin.program.status', $program), ['status' => 'dibuka'])->assertForbidden();
    }
}
