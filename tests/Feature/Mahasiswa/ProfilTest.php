<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'nama' => 'Nadia Putri',
            'no_telepon' => '081234567890',
            'kampus' => 'Universitas Telkom',
            'jenjang_id' => Jenjang::factory()->create()->id,
            'wilayah_id' => Wilayah::factory()->create()->id,
            'penghasilan_ortu' => '2500000',
            'jml_tanggungan' => '4',
            'punya_kipk' => '1',
            'terdaftar_dtsen' => '1',
            'desil_dtsen' => '3',
            'kondisi_khusus' => '',
            ...$overrides,
        ];
    }

    public function test_student_without_profile_sees_an_empty_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('mahasiswa.profil.show'))
            ->assertOk()
            ->assertSeeText('Simpan profil')
            ->assertSeeText('Isi formulir profil');
    }

    public function test_student_can_create_their_profile(): void
    {
        $payload = $this->validPayload();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.profil.store'), $payload)
            ->assertRedirect(route('mahasiswa.profil.show'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('mahasiswa', [
            'user_id' => $this->user->id,
            'kampus' => 'Universitas Telkom',
            'jenjang_id' => $payload['jenjang_id'],
            'wilayah_id' => $payload['wilayah_id'],
            'jml_tanggungan' => 4,
            'punya_kipk' => true,
            'terdaftar_dtsen' => true,
            'desil_dtsen' => 3,
            'kondisi_khusus' => null,
        ]);
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'nama' => 'Nadia Putri', 'no_telepon' => '081234567890']);
    }

    public function test_student_cannot_create_a_second_profile(): void
    {
        Mahasiswa::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.profil.store'), $this->validPayload())
            ->assertRedirect(route('mahasiswa.profil.show'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('mahasiswa', 1);
    }

    public function test_student_can_update_their_profile(): void
    {
        $profil = Mahasiswa::factory()->for($this->user)->terdaftarDtsen(2)->create();

        $this->actingAs($this->user)
            ->put(route('mahasiswa.profil.update'), $this->validPayload([
                'kampus' => 'Institut Teknologi Bandung',
                'terdaftar_dtsen' => '0',
                'desil_dtsen' => '2',
            ]))
            ->assertRedirect(route('mahasiswa.profil.show'))
            ->assertSessionHas('success');

        $profil->refresh();
        $this->assertSame('Institut Teknologi Bandung', $profil->kampus);
        $this->assertFalse($profil->terdaftar_dtsen);
        $this->assertNull($profil->desil_dtsen);
    }

    public function test_dtsen_decile_is_required_when_registered_in_dtsen(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.profil.store'), $this->validPayload(['desil_dtsen' => '']))
            ->assertSessionHasErrors('desil_dtsen');

        $this->assertDatabaseCount('mahasiswa', 0);
    }

    public function test_profile_requires_valid_references_and_numbers(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.profil.store'), $this->validPayload([
                'jenjang_id' => 999,
                'wilayah_id' => 999,
                'penghasilan_ortu' => '-1',
                'jml_tanggungan' => '0',
                'no_telepon' => '12345',
            ]))
            ->assertSessionHasErrors(['jenjang_id', 'wilayah_id', 'penghasilan_ortu', 'jml_tanggungan', 'no_telepon']);
    }

    public function test_user_id_cannot_be_overridden_through_the_form(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.profil.store'), $this->validPayload(['user_id' => $other->id]));

        $this->assertDatabaseHas('mahasiswa', ['user_id' => $this->user->id]);
        $this->assertDatabaseMissing('mahasiswa', ['user_id' => $other->id]);
    }

    public function test_profile_page_shows_completeness_indicator(): void
    {
        Mahasiswa::factory()->for($this->user)->create(['kampus' => 'Universitas Telkom']);

        $this->actingAs($this->user)
            ->get(route('mahasiswa.profil.show'))
            ->assertOk()
            ->assertSeeText('Kelengkapan profil')
            ->assertSeeText('100%')
            ->assertSeeText('Simpan perubahan');
    }

    public function test_completeness_drops_when_dtsen_decile_is_missing(): void
    {
        $profil = Mahasiswa::factory()->make(['terdaftar_dtsen' => true, 'desil_dtsen' => null]);

        $this->assertFalse($profil->isLengkap());
        $this->assertSame(75, $profil->persentaseKelengkapan());
    }

    public function test_admin_cannot_access_student_profile_form(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('mahasiswa.profil.show'))
            ->assertForbidden();
    }
}
