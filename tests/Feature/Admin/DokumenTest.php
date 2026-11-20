<?php

namespace Tests\Feature\Admin;

use App\Enums\StatusVerifikasi;
use App\Models\Dokumen;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokumenTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Dokumen $dokumen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->dokumen = Dokumen::factory()->for(Pendaftaran::factory()->dikirim())->create(['jenis_dokumen' => 'SKTM']);
    }

    public function test_admin_sees_documents_on_registration_detail(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.pendaftaran.show', $this->dokumen->pendaftaran))
            ->assertOk()
            ->assertSeeText('SKTM')
            ->assertSeeText('Menunggu verifikasi');
    }

    public function test_admin_can_accept_a_document(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.dokumen.verifikasi', $this->dokumen), ['status_verifikasi' => 'valid'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertSame(StatusVerifikasi::Valid, $this->dokumen->refresh()->status_verifikasi);
    }

    public function test_rejecting_a_document_requires_a_note(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.dokumen.verifikasi', $this->dokumen), ['status_verifikasi' => 'tidak_valid', 'catatan_admin' => ''])
            ->assertSessionHasErrors('catatan_admin');

        $this->actingAs($this->admin)
            ->patch(route('admin.dokumen.verifikasi', $this->dokumen), ['status_verifikasi' => 'tidak_valid', 'catatan_admin' => 'Foto buram.'])
            ->assertSessionHasNoErrors();

        $this->dokumen->refresh();
        $this->assertSame(StatusVerifikasi::TidakValid, $this->dokumen->status_verifikasi);
        $this->assertSame('Foto buram.', $this->dokumen->catatan_admin);
    }

    public function test_student_cannot_verify_documents(): void
    {
        $this->actingAs($this->dokumen->pendaftaran->mahasiswa->user)
            ->patch(route('admin.dokumen.verifikasi', $this->dokumen), ['status_verifikasi' => 'valid'])
            ->assertForbidden();

        $this->assertSame(StatusVerifikasi::Menunggu, $this->dokumen->refresh()->status_verifikasi);
    }
}
