<?php

namespace Tests\Feature\Admin;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_all_student_profiles(): void
    {
        $profil = Mahasiswa::factory()->for(User::factory()->state(['nama' => 'Budi Santoso']))->create(['kampus' => 'Universitas Andalas']);

        $this->actingAs($this->admin)
            ->get(route('admin.profil.index'))
            ->assertOk()
            ->assertSeeText('Budi Santoso')
            ->assertSeeText('Universitas Andalas')
            ->assertSeeText($profil->wilayah->kabupaten);
    }

    public function test_profiles_can_be_searched_by_name(): void
    {
        Mahasiswa::factory()->for(User::factory()->state(['nama' => 'Budi Santoso']))->create();
        Mahasiswa::factory()->for(User::factory()->state(['nama' => 'Citra Lestari']))->create();

        $this->actingAs($this->admin)
            ->get(route('admin.profil.index', ['q' => 'budi']))
            ->assertOk()
            ->assertSeeText('Budi Santoso')
            ->assertDontSeeText('Citra Lestari');
    }

    public function test_profiles_can_be_filtered_by_account_status(): void
    {
        Mahasiswa::factory()->for(User::factory()->state(['nama' => 'Budi Santoso']))->create();
        Mahasiswa::factory()->for(User::factory()->inactive()->state(['nama' => 'Citra Lestari']))->create();

        $this->actingAs($this->admin)
            ->get(route('admin.profil.index', ['status' => 'nonaktif']))
            ->assertOk()
            ->assertSeeText('Citra Lestari')
            ->assertDontSeeText('Budi Santoso');
    }

    public function test_admin_can_view_profile_detail(): void
    {
        $profil = Mahasiswa::factory()->terdaftarDtsen(3)->create(['penghasilan_ortu' => 1_500_000]);

        $this->actingAs($this->admin)
            ->get(route('admin.profil.show', $profil))
            ->assertOk()
            ->assertSeeText($profil->user->nama)
            ->assertSeeText('Rp 1.500.000')
            ->assertSeeText('Terdaftar, desil 3');
    }

    public function test_admin_can_deactivate_a_profile_without_deleting_it(): void
    {
        $profil = Mahasiswa::factory()->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.profil.nonaktif', $profil))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertFalse($profil->user->refresh()->is_active);
        $this->assertModelExists($profil);
    }

    public function test_deactivating_an_inactive_profile_reports_an_error(): void
    {
        $profil = Mahasiswa::factory()->for(User::factory()->inactive())->create();

        $this->actingAs($this->admin)
            ->patch(route('admin.profil.nonaktif', $profil))
            ->assertSessionHas('error');
    }

    public function test_student_cannot_access_admin_profile_pages(): void
    {
        $student = User::factory()->create();
        $profil = Mahasiswa::factory()->create();

        $this->actingAs($student)->get(route('admin.profil.index'))->assertForbidden();
        $this->actingAs($student)->patch(route('admin.profil.nonaktif', $profil))->assertForbidden();

        $this->assertTrue($profil->user->refresh()->is_active);
    }
}
