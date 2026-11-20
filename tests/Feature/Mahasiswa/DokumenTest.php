<?php

namespace Tests\Feature\Mahasiswa;

use App\Enums\StatusPendaftaran;
use App\Enums\StatusVerifikasi;
use App\Models\Dokumen;
use App\Models\Esai;
use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\Persyaratan;
use App\Models\Program;
use App\Models\RiwayatPendidikan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mahasiswa $profil;

    private Pendaftaran $pendaftaran;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        $this->user = User::factory()->create();
        $this->profil = Mahasiswa::factory()->for($this->user)->penerimaKipk()->create();
        RiwayatPendidikan::factory()->for($this->profil)->create(['jenjang_id' => $this->profil->jenjang_id]);
        $program = Program::factory()->dibuka()->create(['jenjang_id' => $this->profil->jenjang_id]);
        Persyaratan::factory()->for($program)->dokumen('SKTM')->create();
        $this->pendaftaran = Pendaftaran::factory()->for($this->profil)->for($program)->create();
    }

    private function pdf(int $kilobytes = 200): UploadedFile
    {
        return UploadedFile::fake()->create('berkas.pdf', $kilobytes, 'application/pdf');
    }

    public function test_required_documents_include_program_documents_and_declared_assistance_proofs(): void
    {
        $this->assertSame(['SKTM', Dokumen::BUKTI_KIPK], $this->pendaftaran->dokumenWajib());

        $this->actingAs($this->user)
            ->get(route('mahasiswa.pendaftaran.show', $this->pendaftaran))
            ->assertOk()
            ->assertSeeText('SKTM')
            ->assertSeeText(Dokumen::BUKTI_KIPK)
            ->assertSeeText('Wajib, belum diunggah');
    }

    public function test_student_can_upload_a_required_document(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.dokumen.store', $this->pendaftaran), ['jenis_dokumen' => 'SKTM', 'file' => $this->pdf()])
            ->assertRedirect(route('mahasiswa.pendaftaran.show', $this->pendaftaran))
            ->assertSessionHas('success');

        $dokumen = Dokumen::sole();
        $this->assertFalse($dokumen->opsional);
        $this->assertSame(StatusVerifikasi::Menunggu, $dokumen->status_verifikasi);
        Storage::assertExists($dokumen->file_path);
    }

    public function test_extra_documents_are_marked_optional(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.dokumen.store', $this->pendaftaran), ['jenis_dokumen' => 'Surat rekomendasi', 'file' => $this->pdf()]);

        $this->assertTrue(Dokumen::sole()->opsional);
    }

    public function test_document_must_be_a_pdf_or_image_of_at_most_2_mb(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.dokumen.store', $this->pendaftaran), ['jenis_dokumen' => 'SKTM', 'file' => $this->pdf(2500)])
            ->assertSessionHasErrors('file');

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.dokumen.store', $this->pendaftaran), [
                'jenis_dokumen' => 'SKTM',
                'file' => UploadedFile::fake()->create('skrip.php', 5, 'application/x-php'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('dokumen', 0);
    }

    public function test_the_same_document_cannot_be_uploaded_twice(): void
    {
        Dokumen::factory()->for($this->pendaftaran)->create(['jenis_dokumen' => 'SKTM']);

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.dokumen.store', $this->pendaftaran), ['jenis_dokumen' => 'SKTM', 'file' => $this->pdf()])
            ->assertSessionHasErrors('jenis_dokumen');
    }

    public function test_registration_cannot_be_submitted_until_required_documents_are_uploaded(): void
    {
        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.kirim', $this->pendaftaran))
            ->assertSessionHas('error');
        $this->assertSame(StatusPendaftaran::Draft, $this->pendaftaran->refresh()->status);

        Dokumen::factory()->for($this->pendaftaran)->create(['jenis_dokumen' => 'SKTM']);
        Dokumen::factory()->for($this->pendaftaran)->create(['jenis_dokumen' => Dokumen::BUKTI_KIPK]);
        Esai::factory()->for($this->pendaftaran)->create();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.kirim', $this->pendaftaran))
            ->assertSessionHas('success');
        $this->assertSame(StatusPendaftaran::Dikirim, $this->pendaftaran->refresh()->status);
    }

    public function test_replacing_a_rejected_document_resets_its_verification(): void
    {
        $lama = $this->pdf()->store(Dokumen::FOLDER);
        $dokumen = Dokumen::factory()->for($this->pendaftaran)->ditolak()->create(['file_path' => $lama]);

        $this->actingAs($this->user)
            ->put(route('mahasiswa.dokumen.update', $dokumen), ['file' => $this->pdf()])
            ->assertSessionHas('success');

        $dokumen->refresh();
        $this->assertSame(StatusVerifikasi::Menunggu, $dokumen->status_verifikasi);
        $this->assertNull($dokumen->catatan_admin);
        Storage::assertMissing($lama);
        Storage::assertExists($dokumen->file_path);
    }

    public function test_valid_documents_cannot_be_replaced_or_deleted(): void
    {
        $dokumen = Dokumen::factory()->for($this->pendaftaran)->valid()->create();
        $path = $dokumen->file_path;

        $this->actingAs($this->user)
            ->put(route('mahasiswa.dokumen.update', $dokumen), ['file' => $this->pdf()])
            ->assertSessionHas('error');

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.dokumen.destroy', $dokumen))
            ->assertSessionHas('error');

        $this->assertSame($path, $dokumen->refresh()->file_path);
    }

    public function test_student_can_delete_an_unverified_document(): void
    {
        $path = $this->pdf()->store(Dokumen::FOLDER);
        $dokumen = Dokumen::factory()->for($this->pendaftaran)->create(['file_path' => $path]);

        $this->actingAs($this->user)
            ->delete(route('mahasiswa.dokumen.destroy', $dokumen))
            ->assertSessionHas('success');

        $this->assertModelMissing($dokumen);
        Storage::assertMissing($path);
    }

    public function test_cancelling_a_registration_removes_its_files(): void
    {
        $path = $this->pdf()->store(Dokumen::FOLDER);
        Dokumen::factory()->for($this->pendaftaran)->create(['file_path' => $path]);

        $this->actingAs($this->user)->delete(route('mahasiswa.pendaftaran.destroy', $this->pendaftaran));

        $this->assertDatabaseCount('dokumen', 0);
        Storage::assertMissing($path);
    }

    public function test_documents_cannot_be_uploaded_after_verification_stage(): void
    {
        $this->pendaftaran->forceFill(['status' => StatusPendaftaran::Seleksi])->save();

        $this->actingAs($this->user)
            ->post(route('mahasiswa.pendaftaran.dokumen.store', $this->pendaftaran), ['jenis_dokumen' => 'SKTM', 'file' => $this->pdf()])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('dokumen', 0);
    }

    public function test_only_the_owner_and_admins_can_download_a_document(): void
    {
        $dokumen = Dokumen::factory()->for($this->pendaftaran)->create(['file_path' => $this->pdf()->store(Dokumen::FOLDER)]);

        $this->actingAs($this->user)->get(route('dokumen.unduh', $dokumen))->assertOk()->assertDownload();
        $this->actingAs(User::factory()->admin()->create())->get(route('dokumen.unduh', $dokumen))->assertOk();

        $lain = Mahasiswa::factory()->create();
        $this->actingAs($lain->user)->get(route('dokumen.unduh', $dokumen))->assertForbidden();
    }

    public function test_student_cannot_touch_another_students_documents(): void
    {
        $lain = Mahasiswa::factory()->create();
        $dokumen = Dokumen::factory()->for($this->pendaftaran)->create();

        $this->actingAs($lain->user)
            ->post(route('mahasiswa.pendaftaran.dokumen.store', $this->pendaftaran), ['jenis_dokumen' => 'SKTM', 'file' => $this->pdf()])
            ->assertForbidden();
        $this->actingAs($lain->user)->put(route('mahasiswa.dokumen.update', $dokumen), ['file' => $this->pdf()])->assertForbidden();
        $this->actingAs($lain->user)->delete(route('mahasiswa.dokumen.destroy', $dokumen))->assertForbidden();

        $this->assertModelExists($dokumen);
    }
}
