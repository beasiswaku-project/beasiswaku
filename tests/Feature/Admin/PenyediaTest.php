<?php

namespace Tests\Feature\Admin;

use App\Enums\JenisPenyedia;
use App\Models\Penyedia;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenyediaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_providers_with_their_program_count(): void
    {
        $penyedia = Penyedia::factory()->create(['nama_penyedia' => 'Yayasan Cahaya Bangsa', 'jenis' => JenisPenyedia::Yayasan]);
        Program::factory()->count(2)->for($penyedia)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.penyedia.index'))
            ->assertOk()
            ->assertSeeText('Yayasan Cahaya Bangsa')
            ->assertSeeText('Yayasan')
            ->assertSeeText('2 program');
    }

    public function test_providers_can_be_searched_and_filtered_by_type(): void
    {
        Penyedia::factory()->create(['nama_penyedia' => 'Kemendikbudristek', 'jenis' => JenisPenyedia::Pemerintah]);
        Penyedia::factory()->create(['nama_penyedia' => 'PT Sinar Jaya', 'jenis' => JenisPenyedia::Swasta]);

        $this->actingAs($this->admin)
            ->get(route('admin.penyedia.index', ['q' => 'sinar']))
            ->assertSeeText('PT Sinar Jaya')
            ->assertDontSeeText('Kemendikbudristek');

        $this->actingAs($this->admin)
            ->get(route('admin.penyedia.index', ['jenis' => 'pemerintah']))
            ->assertSeeText('Kemendikbudristek')
            ->assertDontSeeText('PT Sinar Jaya');
    }

    public function test_admin_can_view_provider_detail(): void
    {
        $penyedia = Penyedia::factory()->create(['kontak' => 'beasiswa@contoh.id']);

        $this->actingAs($this->admin)
            ->get(route('admin.penyedia.show', $penyedia))
            ->assertOk()
            ->assertSeeText($penyedia->nama_penyedia)
            ->assertSeeText('beasiswa@contoh.id');
    }

    public function test_admin_can_create_a_provider(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.penyedia.store'), [
                'nama_penyedia' => '  Universitas   Telkom ',
                'jenis' => 'kampus',
                'kontak' => '',
                'alamat' => 'Jl. Telekomunikasi No. 1, Bandung',
            ])
            ->assertRedirect(route('admin.penyedia.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('penyedia', [
            'nama_penyedia' => 'Universitas Telkom',
            'jenis' => 'kampus',
            'kontak' => null,
        ]);
    }

    public function test_provider_requires_a_unique_name_and_known_type(): void
    {
        Penyedia::factory()->create(['nama_penyedia' => 'Yayasan Cahaya Bangsa']);

        $this->actingAs($this->admin)
            ->post(route('admin.penyedia.store'), ['nama_penyedia' => 'Yayasan Cahaya Bangsa', 'jenis' => 'lainnya'])
            ->assertSessionHasErrors(['nama_penyedia', 'jenis']);
    }

    public function test_admin_can_update_a_provider_keeping_its_own_name(): void
    {
        $penyedia = Penyedia::factory()->create(['nama_penyedia' => 'Yayasan Cahaya Bangsa', 'jenis' => JenisPenyedia::Yayasan]);

        $this->actingAs($this->admin)
            ->put(route('admin.penyedia.update', $penyedia), [
                'nama_penyedia' => 'Yayasan Cahaya Bangsa',
                'jenis' => 'swasta',
                'kontak' => '021-555-0101',
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame(JenisPenyedia::Swasta, $penyedia->refresh()->jenis);
        $this->assertSame('021-555-0101', $penyedia->kontak);
    }

    public function test_admin_can_delete_a_provider_without_programs(): void
    {
        $penyedia = Penyedia::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.penyedia.destroy', $penyedia))
            ->assertRedirect(route('admin.penyedia.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($penyedia);
    }

    public function test_provider_with_programs_cannot_be_deleted(): void
    {
        $penyedia = Penyedia::factory()->create();
        Program::factory()->for($penyedia)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.penyedia.destroy', $penyedia))
            ->assertSessionHas('error');

        $this->assertModelExists($penyedia);
    }

    public function test_student_cannot_manage_providers(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->get(route('admin.penyedia.index'))->assertForbidden();
        $this->actingAs($student)->post(route('admin.penyedia.store'), ['nama_penyedia' => 'X', 'jenis' => 'kampus'])->assertForbidden();

        $this->assertDatabaseCount('penyedia', 0);
    }
}
