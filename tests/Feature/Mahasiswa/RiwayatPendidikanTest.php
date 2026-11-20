<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Jenjang;
use App\Models\Mahasiswa;
use App\Models\RiwayatPendidikan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatPendidikanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mahasiswa $profil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->profil = Mahasiswa::factory()->for($this->user)->create();
    }

    public function test_student_sees_their_gpa_history_and_latest_gpa(): void
    {
        RiwayatPendidikan::factory()->for($this->profil)->create(['jenjang_id' => $this->profil->jenjang_id, 'semester' => 1, 'ipk' => 3.10]);
        RiwayatPendidikan::factory()->for($this->profil)->create(['jenjang_id' => $this->profil->jenjang_id, 'semester' => 2, 'ipk' => 3.45]);

        $this->actingAs($this->user)
            ->get(route('mahasiswa.riwayat-pendidikan.index'))
            ->assertOk()
            ->assertSeeText('Semester 1')
            ->assertSeeText('Semester 2')
            ->assertSeeText('3,45');

        $this->assertSame(3.45, $this->profil->refresh()->ipkTerakhir());
    }

    public function test_student_does_not_see_other_students_history(): void
    {
        RiwayatPendidikan::factory()->create(['semester' => 7, 'ipk' => 2.11]);

        $this->actingAs($this->user)
            ->get(route('mahasiswa.riwayat-pendidikan.index'))
            ->assertOk()
            ->assertDontSeeText('Semester 7');
    }

    public function test_student_without_profile_is_asked_to_fill_it_first(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('mahasiswa.riwayat-pendidikan.index'))
            ->assertOk()
            ->assertSeeText('Isi data diri terlebih dahulu');

        $this->actingAs(User::factory()->create())
            ->post(route('mahasiswa.riwayat-pendidikan.store'), ['jenjang_id' => $this->profil->jenjang_id, 'semester' => 1, 'ipk' => '3.00'])
            ->assertRedirect(route('mahasiswa.profil.show'));

        $this->assertDatabaseCount('riwayat_pendidikan', 0);
    }

    public function test_student_can_add_a_gpa_with_comma_decimal(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.riwayat-pendidikan.store'), [
                'jenjang_id' => $this->profil->jenjang_id,
                'semester' => '3',
                'ipk' => '3,75',
            ])
            ->assertRedirect(route('mahasiswa.riwayat-pendidikan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('riwayat_pendidikan', [
            'mahasiswa_id' => $this->profil->id,
            'semester' => 3,
            'ipk' => 3.75,
        ]);
    }

    public function test_the_same_semester_cannot_be_recorded_twice_at_one_level(): void
    {
        RiwayatPendidikan::factory()->for($this->profil)->create(['jenjang_id' => $this->profil->jenjang_id, 'semester' => 2]);

        $this->actingAs($this->user)
            ->post(route('mahasiswa.riwayat-pendidikan.store'), ['jenjang_id' => $this->profil->jenjang_id, 'semester' => 2, 'ipk' => '3.00'])
            ->assertSessionHasErrors('semester');

        $this->actingAs($this->user)
            ->post(route('mahasiswa.riwayat-pendidikan.store'), ['jenjang_id' => Jenjang::factory()->create()->id, 'semester' => 2, 'ipk' => '3.00'])
            ->assertSessionHasNoErrors();
    }

    public function test_gpa_must_be_between_zero_and_four(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.riwayat-pendidikan.store'), ['jenjang_id' => $this->profil->jenjang_id, 'semester' => 1, 'ipk' => '4.10'])
            ->assertSessionHasErrors('ipk');
    }

    public function test_student_can_correct_their_gpa(): void
    {
        $riwayat = RiwayatPendidikan::factory()->for($this->profil)->create(['jenjang_id' => $this->profil->jenjang_id, 'semester' => 2, 'ipk' => 3.00]);

        $this->actingAs($this->user)
            ->put(route('mahasiswa.riwayat-pendidikan.update', $riwayat), ['jenjang_id' => $this->profil->jenjang_id, 'semester' => 2, 'ipk' => '3.20'])
            ->assertRedirect(route('mahasiswa.riwayat-pendidikan.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('3.20', $riwayat->refresh()->ipk);
    }

    public function test_student_can_delete_their_gpa(): void
    {
        $riwayat = RiwayatPendidikan::factory()->for($this->profil)->create();

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.riwayat-pendidikan.destroy', $riwayat))
            ->assertRedirect(route('mahasiswa.riwayat-pendidikan.index'));

        $this->assertModelMissing($riwayat);
    }

    public function test_student_cannot_change_another_students_gpa(): void
    {
        $riwayat = RiwayatPendidikan::factory()->create(['semester' => 2, 'ipk' => 2.50]);

        $this->actingAs($this->user)
            ->put(route('mahasiswa.riwayat-pendidikan.update', $riwayat), ['jenjang_id' => $riwayat->jenjang_id, 'semester' => 2, 'ipk' => '4.00'])
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.riwayat-pendidikan.destroy', $riwayat))
            ->assertForbidden();

        $this->assertSame('2.50', $riwayat->refresh()->ipk);
    }
}
