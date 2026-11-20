<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Jenjang;
use App\Models\Kategori;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeasiswaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_catalogue_only_lists_open_programs(): void
    {
        Program::factory()->dibuka()->create(['judul' => 'Beasiswa Terbuka']);
        Program::factory()->create(['judul' => 'Beasiswa Draf']);
        Program::factory()->ditutup()->create(['judul' => 'Beasiswa Ditutup']);
        Program::factory()->dibuka()->create(['judul' => 'Beasiswa Kedaluwarsa', 'tgl_buka' => today()->subMonth(), 'deadline' => today()->subDay()]);
        Program::factory()->dibuka()->create(['judul' => 'Beasiswa Belum Mulai', 'tgl_buka' => today()->addDay()]);

        $this->actingAs($this->user)
            ->get(route('beasiswa.index'))
            ->assertOk()
            ->assertSeeText('Beasiswa Terbuka')
            ->assertDontSeeText('Beasiswa Draf')
            ->assertDontSeeText('Beasiswa Ditutup')
            ->assertDontSeeText('Beasiswa Kedaluwarsa')
            ->assertDontSeeText('Beasiswa Belum Mulai');
    }

    public function test_catalogue_can_be_filtered_by_category_and_level(): void
    {
        $kategori = Kategori::factory()->create();
        $jenjang = Jenjang::factory()->create();
        Program::factory()->dibuka()->for($kategori)->for($jenjang)->create(['judul' => 'Beasiswa Cocok']);
        Program::factory()->dibuka()->create(['judul' => 'Beasiswa Lain']);

        $this->actingAs($this->user)
            ->get(route('beasiswa.index', ['kategori' => $kategori->id, 'jenjang' => $jenjang->id]))
            ->assertSeeText('Beasiswa Cocok')
            ->assertDontSeeText('Beasiswa Lain');
    }

    public function test_student_can_view_an_open_program(): void
    {
        $program = Program::factory()->dibuka()->create();

        $this->actingAs($this->user)
            ->get(route('beasiswa.show', $program))
            ->assertOk()
            ->assertSeeText($program->judul)
            ->assertSeeText($program->penyedia->nama_penyedia);
    }

    public function test_draft_program_is_not_visible_to_students(): void
    {
        $program = Program::factory()->create();

        $this->actingAs($this->user)
            ->get(route('beasiswa.show', $program))
            ->assertNotFound();
    }
}
