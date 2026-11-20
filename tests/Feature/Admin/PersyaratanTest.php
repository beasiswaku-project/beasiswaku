<?php

namespace Tests\Feature\Admin;

use App\Enums\JenisPersyaratan;
use App\Models\Pendaftaran;
use App\Models\Persyaratan;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersyaratanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Program $program;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->program = Program::factory()->create();
    }

    public function test_requirements_are_listed_on_the_program_detail(): void
    {
        Persyaratan::factory()->for($this->program)->create(['nilai' => '3.25']);
        Persyaratan::factory()->for($this->program)->dokumen('SKTM')->create();

        $this->actingAs($this->admin)
            ->get(route('admin.program.show', $this->program))
            ->assertOk()
            ->assertSeeText('IPK minimum 3,25')
            ->assertSeeText('Dokumen: SKTM');
    }

    public function test_admin_can_add_a_mandatory_requirement(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.program.persyaratan.store', $this->program), [
                'jenis' => 'ipk_minimum',
                'nilai' => '3,00',
                'wajib' => '1',
            ])
            ->assertRedirect(route('admin.program.show', $this->program))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('persyaratan', [
            'program_id' => $this->program->id,
            'jenis' => 'ipk_minimum',
            'nilai' => '3.00',
            'wajib' => true,
        ]);
    }

    public function test_admin_can_add_a_preference_without_a_value(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.program.persyaratan.store', $this->program), [
                'jenis' => 'wilayah_3t',
                'nilai' => 'diabaikan',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('persyaratan', ['jenis' => 'wilayah_3t', 'nilai' => null, 'wajib' => false]);
    }

    public function test_requirement_value_is_validated_by_type(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.program.persyaratan.store', $this->program), ['jenis' => 'ipk_minimum', 'nilai' => '5'])
            ->assertSessionHasErrors('nilai');

        $this->actingAs($this->admin)
            ->post(route('admin.program.persyaratan.store', $this->program), ['jenis' => 'dokumen', 'nilai' => ''])
            ->assertSessionHasErrors('nilai');

        $this->actingAs($this->admin)
            ->post(route('admin.program.persyaratan.store', $this->program), ['jenis' => 'tanggungan_minimum', 'nilai' => '3'])
            ->assertSessionHasErrors('jenis');

        $this->assertDatabaseCount('persyaratan', 0);
    }

    public function test_admin_can_update_and_delete_a_requirement(): void
    {
        $persyaratan = Persyaratan::factory()->for($this->program)->create();

        $this->actingAs($this->admin)
            ->put(route('admin.persyaratan.update', $persyaratan), ['jenis' => 'penghasilan_maksimum', 'nilai' => '4000000'])
            ->assertSessionHasNoErrors();

        $persyaratan->refresh();
        $this->assertSame(JenisPersyaratan::PenghasilanMaksimum, $persyaratan->jenis);
        $this->assertFalse($persyaratan->wajib);

        $this->actingAs($this->admin)
            ->delete(route('admin.persyaratan.destroy', $persyaratan))
            ->assertRedirect(route('admin.program.show', $this->program));

        $this->assertModelMissing($persyaratan);
    }

    public function test_requirements_are_locked_once_the_program_has_registrations(): void
    {
        $persyaratan = Persyaratan::factory()->for($this->program)->create(['nilai' => '3.00']);
        Pendaftaran::factory()->for($this->program)->create();

        $this->actingAs($this->admin)
            ->post(route('admin.program.persyaratan.store', $this->program), ['jenis' => 'kipk', 'wajib' => '1'])
            ->assertSessionHas('error');

        $this->actingAs($this->admin)
            ->put(route('admin.persyaratan.update', $persyaratan), ['jenis' => 'ipk_minimum', 'nilai' => '3.50', 'wajib' => '1'])
            ->assertSessionHas('error');

        $this->actingAs($this->admin)
            ->delete(route('admin.persyaratan.destroy', $persyaratan))
            ->assertSessionHas('error');

        $this->assertSame('3.00', $persyaratan->refresh()->nilai);
        $this->assertDatabaseCount('persyaratan', 1);
    }

    public function test_student_cannot_manage_requirements(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.program.persyaratan.store', $this->program), ['jenis' => 'kipk'])
            ->assertForbidden();
    }
}
