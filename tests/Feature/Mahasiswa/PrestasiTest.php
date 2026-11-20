<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Mahasiswa;
use App\Models\Prestasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PrestasiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mahasiswa $profil;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        $this->user = User::factory()->create();
        $this->profil = Mahasiswa::factory()->for($this->user)->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'jenis' => 'non_akademik',
            'nama_kegiatan' => 'Juara 1 Lomba Debat',
            'tingkat' => 'Nasional',
            ...$overrides,
        ];
    }

    public function test_student_sees_only_their_own_achievements(): void
    {
        Prestasi::factory()->for($this->profil)->create(['nama_kegiatan' => 'Ketua BEM']);
        Prestasi::factory()->create(['nama_kegiatan' => 'Juara Lomba Orang Lain']);

        $this->actingAs($this->user)
            ->get(route('mahasiswa.prestasi.index'))
            ->assertOk()
            ->assertSeeText('Ketua BEM')
            ->assertDontSeeText('Juara Lomba Orang Lain');
    }

    public function test_student_can_add_an_achievement_with_proof(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.prestasi.store'), $this->validPayload([
                'bukti' => UploadedFile::fake()->create('sertifikat.pdf', 500, 'application/pdf'),
            ]))
            ->assertRedirect(route('mahasiswa.prestasi.index'))
            ->assertSessionHas('success');

        $prestasi = Prestasi::sole();
        $this->assertSame($this->profil->id, $prestasi->mahasiswa_id);
        $this->assertSame('Juara 1 Lomba Debat', $prestasi->nama_kegiatan);
        Storage::assertExists($prestasi->bukti_path);
    }

    public function test_proof_must_be_a_small_pdf_or_image(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.prestasi.store'), $this->validPayload([
                'bukti' => UploadedFile::fake()->create('virus.exe', 10, 'application/x-msdownload'),
            ]))
            ->assertSessionHasErrors('bukti');

        $this->actingAs($this->user)
            ->post(route('mahasiswa.prestasi.store'), $this->validPayload([
                'bukti' => UploadedFile::fake()->create('besar.pdf', 3000, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('bukti');

        $this->assertDatabaseCount('prestasi', 0);
    }

    public function test_achievement_type_and_level_must_be_known_values(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.prestasi.store'), $this->validPayload(['jenis' => 'olahraga', 'tingkat' => 'Galaksi']))
            ->assertSessionHasErrors(['jenis', 'tingkat']);
    }

    public function test_uploading_a_new_proof_replaces_the_old_file(): void
    {
        $lama = UploadedFile::fake()->image('lama.jpg')->store(Prestasi::FOLDER_BUKTI);
        $prestasi = Prestasi::factory()->for($this->profil)->create(['bukti_path' => $lama]);

        $this->actingAs($this->user)
            ->put(route('mahasiswa.prestasi.update', $prestasi), $this->validPayload([
                'nama_kegiatan' => 'Juara 2 Lomba Debat',
                'bukti' => UploadedFile::fake()->image('baru.png'),
            ]))
            ->assertRedirect(route('mahasiswa.prestasi.index'));

        $prestasi->refresh();
        $this->assertSame('Juara 2 Lomba Debat', $prestasi->nama_kegiatan);
        Storage::assertMissing($lama);
        Storage::assertExists($prestasi->bukti_path);
    }

    public function test_updating_without_a_file_keeps_the_existing_proof(): void
    {
        $lama = UploadedFile::fake()->image('lama.jpg')->store(Prestasi::FOLDER_BUKTI);
        $prestasi = Prestasi::factory()->for($this->profil)->create(['bukti_path' => $lama]);

        $this->actingAs($this->user)->put(route('mahasiswa.prestasi.update', $prestasi), $this->validPayload());

        $this->assertSame($lama, $prestasi->refresh()->bukti_path);
        Storage::assertExists($lama);
    }

    public function test_deleting_an_achievement_removes_its_proof(): void
    {
        $bukti = UploadedFile::fake()->image('bukti.jpg')->store(Prestasi::FOLDER_BUKTI);
        $prestasi = Prestasi::factory()->for($this->profil)->create(['bukti_path' => $bukti]);

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.prestasi.destroy', $prestasi))
            ->assertRedirect(route('mahasiswa.prestasi.index'));

        $this->assertModelMissing($prestasi);
        Storage::assertMissing($bukti);
    }

    public function test_student_cannot_change_another_students_achievement(): void
    {
        $prestasi = Prestasi::factory()->create(['nama_kegiatan' => 'Asli']);

        $this->actingAs($this->user)
            ->put(route('mahasiswa.prestasi.update', $prestasi), $this->validPayload())
            ->assertForbidden();

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.prestasi.destroy', $prestasi))
            ->assertForbidden();

        $this->assertSame('Asli', $prestasi->refresh()->nama_kegiatan);
    }

    public function test_proof_can_be_downloaded_by_its_owner_and_admins_only(): void
    {
        $bukti = UploadedFile::fake()->create('bukti.pdf', 10, 'application/pdf')->store(Prestasi::FOLDER_BUKTI);
        $prestasi = Prestasi::factory()->for($this->profil)->create(['bukti_path' => $bukti]);

        $this->actingAs($this->user)->get(route('prestasi.bukti', $prestasi))->assertOk()->assertDownload();
        $this->actingAs(User::factory()->admin()->create())->get(route('prestasi.bukti', $prestasi))->assertOk();
        $this->actingAs(User::factory()->create())->get(route('prestasi.bukti', $prestasi))->assertForbidden();
    }

    public function test_admin_sees_achievements_on_profile_detail(): void
    {
        Prestasi::factory()->for($this->profil)->create(['nama_kegiatan' => 'Ketua Himpunan']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.profil.show', $this->profil))
            ->assertOk()
            ->assertSeeText('Ketua Himpunan');
    }
}
