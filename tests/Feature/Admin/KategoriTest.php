<?php

namespace Tests\Feature\Admin;

use App\Models\Kategori;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_categories_with_their_program_count(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Prestasi Akademik']);
        Program::factory()->count(2)->for($kategori)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.kategori.index'))
            ->assertOk()
            ->assertSeeText('Prestasi Akademik')
            ->assertSeeText('2 program');
    }

    public function test_categories_can_be_searched_by_name(): void
    {
        Kategori::factory()->create(['nama_kategori' => 'Afirmasi 3T']);
        Kategori::factory()->create(['nama_kategori' => 'Keagamaan']);

        $this->actingAs($this->admin)
            ->get(route('admin.kategori.index', ['q' => 'afirmasi']))
            ->assertOk()
            ->assertSeeText('Afirmasi 3T')
            ->assertDontSeeText('Keagamaan');
    }

    public function test_admin_can_view_category_detail_with_its_programs(): void
    {
        $kategori = Kategori::factory()->create();
        $program = Program::factory()->for($kategori)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.kategori.show', $kategori))
            ->assertOk()
            ->assertSeeText($kategori->nama_kategori)
            ->assertSeeText($program->judul);
    }

    public function test_admin_can_create_a_category(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.kategori.store'), [
                'nama_kategori' => '  KIP-K  ',
                'deskripsi' => 'Lanjutan dukungan penerima KIP Kuliah',
            ])
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'KIP-K',
            'deskripsi' => 'Lanjutan dukungan penerima KIP Kuliah',
        ]);
    }

    public function test_category_name_is_required_and_unique(): void
    {
        Kategori::factory()->create(['nama_kategori' => 'Difabel']);

        $this->actingAs($this->admin)
            ->post(route('admin.kategori.store'), ['nama_kategori' => ''])
            ->assertSessionHasErrors('nama_kategori');

        $this->actingAs($this->admin)
            ->post(route('admin.kategori.store'), ['nama_kategori' => 'Difabel'])
            ->assertSessionHasErrors('nama_kategori');

        $this->assertDatabaseCount('kategori', 1);
    }

    public function test_admin_can_update_a_category(): void
    {
        $kategori = Kategori::factory()->create();

        $this->actingAs($this->admin)
            ->from(route('admin.kategori.index'))
            ->put(route('admin.kategori.update', $kategori), [
                'nama_kategori' => 'Prestasi Non-Akademik',
                'deskripsi' => 'Prestasi di bidang seni dan olahraga',
            ])
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('kategori', [
            'id' => $kategori->id,
            'nama_kategori' => 'Prestasi Non-Akademik',
            'deskripsi' => 'Prestasi di bidang seni dan olahraga',
        ]);
    }

    public function test_updating_a_category_may_keep_its_own_name(): void
    {
        $kategori = Kategori::factory()->create(['nama_kategori' => 'Ekonomi']);

        $this->actingAs($this->admin)
            ->put(route('admin.kategori.update', $kategori), [
                'nama_kategori' => 'Ekonomi',
                'deskripsi' => 'Deskripsi baru',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $kategori = Kategori::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.kategori.destroy', $kategori))
            ->assertRedirect(route('admin.kategori.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($kategori);
    }

    public function test_category_used_by_a_program_cannot_be_deleted(): void
    {
        $kategori = Kategori::factory()->create();
        Program::factory()->for($kategori)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.kategori.destroy', $kategori))
            ->assertSessionHas('error');

        $this->assertModelExists($kategori);
    }

    public function test_mahasiswa_cannot_manage_categories(): void
    {
        $mahasiswa = User::factory()->create();
        $kategori = Kategori::factory()->create();

        $this->actingAs($mahasiswa)->get(route('admin.kategori.index'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('admin.kategori.show', $kategori))->assertForbidden();
        $this->actingAs($mahasiswa)->post(route('admin.kategori.store'), ['nama_kategori' => 'Baru'])->assertForbidden();
        $this->actingAs($mahasiswa)->put(route('admin.kategori.update', $kategori), ['nama_kategori' => 'Ubah'])->assertForbidden();
        $this->actingAs($mahasiswa)->delete(route('admin.kategori.destroy', $kategori))->assertForbidden();

        $this->assertModelExists($kategori);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.kategori.index'))->assertRedirect(route('login'));
    }
}
