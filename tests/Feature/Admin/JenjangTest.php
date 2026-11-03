<?php

namespace Tests\Feature\Admin;

use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\Program;
use App\Models\RiwayatPendidikan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JenjangTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_levels_with_their_usage_counts(): void
    {
        $jenjang = Jenjang::factory()->create(['nama_jenjang' => 'S1']);
        Program::factory()->count(2)->for($jenjang)->create();
        Mahasiswa::factory()->count(3)->for($jenjang)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.jenjang.index'))
            ->assertOk()
            ->assertSeeText('S1')
            ->assertSeeText('2 program')
            ->assertSeeText('3 profil');
    }

    public function test_levels_can_be_searched_by_name(): void
    {
        Jenjang::factory()->create(['nama_jenjang' => 'D3', 'keterangan' => 'Diploma Tiga']);
        Jenjang::factory()->create(['nama_jenjang' => 'S2', 'keterangan' => 'Magister']);

        $this->actingAs($this->admin)
            ->get(route('admin.jenjang.index', ['q' => 'd3']))
            ->assertOk()
            ->assertSeeText('Diploma Tiga')
            ->assertDontSeeText('Magister');
    }

    public function test_admin_can_view_level_detail_with_its_programs(): void
    {
        $jenjang = Jenjang::factory()->create();
        $program = Program::factory()->for($jenjang)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.jenjang.show', $jenjang))
            ->assertOk()
            ->assertSeeText($jenjang->nama_jenjang)
            ->assertSeeText($program->judul);
    }

    public function test_admin_can_create_a_level(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.jenjang.store'), [
                'nama_jenjang' => '  D4  ',
                'keterangan' => 'Diploma Empat',
            ])
            ->assertRedirect(route('admin.jenjang.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('jenjang', [
            'nama_jenjang' => 'D4',
            'keterangan' => 'Diploma Empat',
        ]);
    }

    public function test_level_name_is_required_unique_and_limited_in_length(): void
    {
        Jenjang::factory()->create(['nama_jenjang' => 'S1']);

        $this->actingAs($this->admin)
            ->post(route('admin.jenjang.store'), ['nama_jenjang' => ''])
            ->assertSessionHasErrors('nama_jenjang');

        $this->actingAs($this->admin)
            ->post(route('admin.jenjang.store'), ['nama_jenjang' => 'S1'])
            ->assertSessionHasErrors('nama_jenjang');

        $this->actingAs($this->admin)
            ->post(route('admin.jenjang.store'), ['nama_jenjang' => str_repeat('a', 51)])
            ->assertSessionHasErrors('nama_jenjang');

        $this->assertDatabaseCount('jenjang', 1);
    }

    public function test_admin_can_update_a_level(): void
    {
        $jenjang = Jenjang::factory()->create(['nama_jenjang' => 'S1']);

        $this->actingAs($this->admin)
            ->from(route('admin.jenjang.index'))
            ->put(route('admin.jenjang.update', $jenjang), [
                'nama_jenjang' => 'S1 Terapan',
                'keterangan' => 'Sarjana Terapan',
            ])
            ->assertRedirect(route('admin.jenjang.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('jenjang', [
            'id' => $jenjang->id,
            'nama_jenjang' => 'S1 Terapan',
            'keterangan' => 'Sarjana Terapan',
        ]);
    }

    public function test_updating_a_level_may_keep_its_own_name(): void
    {
        $jenjang = Jenjang::factory()->create(['nama_jenjang' => 'S2']);

        $this->actingAs($this->admin)
            ->put(route('admin.jenjang.update', $jenjang), [
                'nama_jenjang' => 'S2',
                'keterangan' => 'Keterangan baru',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_unused_level_can_be_deleted(): void
    {
        $jenjang = Jenjang::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.jenjang.destroy', $jenjang))
            ->assertRedirect(route('admin.jenjang.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($jenjang);
    }

    public function test_level_used_by_a_program_cannot_be_deleted(): void
    {
        $jenjang = Jenjang::factory()->create();
        Program::factory()->for($jenjang)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.jenjang.destroy', $jenjang))
            ->assertSessionHas('error');

        $this->assertModelExists($jenjang);
    }

    public function test_level_used_by_a_student_profile_cannot_be_deleted(): void
    {
        $jenjang = Jenjang::factory()->create();
        Mahasiswa::factory()->for($jenjang)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.jenjang.destroy', $jenjang))
            ->assertSessionHas('error');

        $this->assertModelExists($jenjang);
    }

    public function test_level_used_by_an_education_history_cannot_be_deleted(): void
    {
        $jenjang = Jenjang::factory()->create();
        RiwayatPendidikan::factory()->for($jenjang)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.jenjang.destroy', $jenjang))
            ->assertSessionHas('error');

        $this->assertModelExists($jenjang);
    }

    public function test_mahasiswa_cannot_manage_levels(): void
    {
        $mahasiswa = User::factory()->create();
        $jenjang = Jenjang::factory()->create();

        $this->actingAs($mahasiswa)->get(route('admin.jenjang.index'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('admin.jenjang.show', $jenjang))->assertForbidden();
        $this->actingAs($mahasiswa)->post(route('admin.jenjang.store'), ['nama_jenjang' => 'S3'])->assertForbidden();
        $this->actingAs($mahasiswa)->put(route('admin.jenjang.update', $jenjang), ['nama_jenjang' => 'S3'])->assertForbidden();
        $this->actingAs($mahasiswa)->delete(route('admin.jenjang.destroy', $jenjang))->assertForbidden();

        $this->assertModelExists($jenjang);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.jenjang.index'))->assertRedirect(route('login'));
    }
}
