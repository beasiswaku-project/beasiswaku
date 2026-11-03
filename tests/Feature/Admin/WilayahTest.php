<?php

namespace Tests\Feature\Admin;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_regions_with_their_profile_count(): void
    {
        $wilayah = Wilayah::factory()->tertinggal()->create([
            'provinsi' => 'Nusa Tenggara Timur',
            'kabupaten' => 'Kabupaten Sumba Barat Daya',
        ]);
        Mahasiswa::factory()->count(2)->for($wilayah)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index'))
            ->assertOk()
            ->assertSeeText('Kabupaten Sumba Barat Daya')
            ->assertSeeText('Nusa Tenggara Timur')
            ->assertSeeText('Wilayah 3T')
            ->assertSeeText('2 profil');
    }

    public function test_regions_can_be_searched_by_regency_name(): void
    {
        Wilayah::factory()->create(['kabupaten' => 'Kota Bandung']);
        Wilayah::factory()->create(['kabupaten' => 'Kota Surabaya']);

        $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['q' => 'bandung']))
            ->assertOk()
            ->assertSeeText('Kota Bandung')
            ->assertDontSeeText('Kota Surabaya');
    }

    public function test_regions_can_be_filtered_by_province(): void
    {
        Wilayah::factory()->create(['provinsi' => 'Maluku', 'kabupaten' => 'Kabupaten Maluku Barat Daya']);
        Wilayah::factory()->create(['provinsi' => 'Jawa Barat', 'kabupaten' => 'Kota Bandung']);

        $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['provinsi' => 'Maluku']))
            ->assertOk()
            ->assertSeeText('Kabupaten Maluku Barat Daya')
            ->assertDontSeeText('Kota Bandung');
    }

    public function test_regions_can_be_filtered_by_3t_status(): void
    {
        Wilayah::factory()->tertinggal()->create(['kabupaten' => 'Kabupaten Nduga']);
        Wilayah::factory()->create(['kabupaten' => 'Kota Yogyakarta']);

        $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['status' => '3t']))
            ->assertOk()
            ->assertSeeText('Kabupaten Nduga')
            ->assertDontSeeText('Kota Yogyakarta');

        $this->actingAs($this->admin)
            ->get(route('admin.wilayah.index', ['status' => 'non_3t']))
            ->assertOk()
            ->assertSeeText('Kota Yogyakarta')
            ->assertDontSeeText('Kabupaten Nduga');
    }

    public function test_prioritas_3t_tab_is_active_only_when_filtering_3t_regions(): void
    {
        $activeTab = fn (string $label): string => '/aria-current="page"\s*>'.preg_quote($label, '/').'<\/a>/';

        $filtered = $this->actingAs($this->admin)->get(route('admin.wilayah.index', ['status' => '3t']))->getContent();
        $this->assertMatchesRegularExpression($activeTab('Prioritas 3T'), $filtered);
        $this->assertDoesNotMatchRegularExpression($activeTab('Wilayah'), $filtered);

        $unfiltered = $this->actingAs($this->admin)->get(route('admin.wilayah.index'))->getContent();
        $this->assertMatchesRegularExpression($activeTab('Wilayah'), $unfiltered);
        $this->assertDoesNotMatchRegularExpression($activeTab('Prioritas 3T'), $unfiltered);
    }

    public function test_admin_can_view_region_detail(): void
    {
        $wilayah = Wilayah::factory()->create(['skor_prioritas' => 40]);
        Mahasiswa::factory()->for($wilayah)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.wilayah.show', $wilayah))
            ->assertOk()
            ->assertSeeText($wilayah->kabupaten)
            ->assertSeeText($wilayah->provinsi)
            ->assertSeeText('40');
    }

    public function test_admin_can_create_a_region(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.wilayah.store'), [
                'provinsi' => '  Papua  Pegunungan ',
                'kabupaten' => 'Kabupaten Lanny Jaya',
                'is_3t' => '1',
                'skor_prioritas' => '85',
            ])
            ->assertRedirect(route('admin.wilayah.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah', [
            'provinsi' => 'Papua Pegunungan',
            'kabupaten' => 'Kabupaten Lanny Jaya',
            'is_3t' => true,
            'skor_prioritas' => 85,
        ]);
    }

    public function test_priority_score_defaults_to_100_for_3t_and_0_otherwise(): void
    {
        $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'provinsi' => 'Maluku',
            'kabupaten' => 'Kabupaten Kepulauan Aru',
            'is_3t' => '1',
        ]);

        $this->actingAs($this->admin)->post(route('admin.wilayah.store'), [
            'provinsi' => 'Jawa Timur',
            'kabupaten' => 'Kota Malang',
        ]);

        $this->assertDatabaseHas('wilayah', ['kabupaten' => 'Kabupaten Kepulauan Aru', 'is_3t' => true, 'skor_prioritas' => 100]);
        $this->assertDatabaseHas('wilayah', ['kabupaten' => 'Kota Malang', 'is_3t' => false, 'skor_prioritas' => 0]);
    }

    public function test_province_and_regency_are_required(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.wilayah.store'), ['provinsi' => '', 'kabupaten' => ''])
            ->assertSessionHasErrors(['provinsi', 'kabupaten']);

        $this->assertDatabaseCount('wilayah', 0);
    }

    public function test_priority_score_must_be_between_0_and_100(): void
    {
        foreach (['-1', '101', 'tinggi'] as $skor) {
            $this->actingAs($this->admin)
                ->post(route('admin.wilayah.store'), [
                    'provinsi' => 'Aceh',
                    'kabupaten' => 'Kabupaten Simeulue',
                    'skor_prioritas' => $skor,
                ])
                ->assertSessionHasErrors('skor_prioritas');
        }

        $this->assertDatabaseCount('wilayah', 0);
    }

    public function test_regency_must_be_unique_within_its_province(): void
    {
        Wilayah::factory()->create(['provinsi' => 'Jawa Barat', 'kabupaten' => 'Kabupaten Bandung']);

        $this->actingAs($this->admin)
            ->post(route('admin.wilayah.store'), ['provinsi' => 'Jawa Barat', 'kabupaten' => 'Kabupaten Bandung'])
            ->assertSessionHasErrors('kabupaten');

        $this->actingAs($this->admin)
            ->post(route('admin.wilayah.store'), ['provinsi' => 'Jawa Tengah', 'kabupaten' => 'Kabupaten Bandung'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('wilayah', 2);
    }

    public function test_admin_can_update_3t_flag_and_priority_score(): void
    {
        $wilayah = Wilayah::factory()->create(['skor_prioritas' => 0]);

        $this->actingAs($this->admin)
            ->from(route('admin.wilayah.index'))
            ->put(route('admin.wilayah.update', $wilayah), [
                'provinsi' => $wilayah->provinsi,
                'kabupaten' => $wilayah->kabupaten,
                'is_3t' => '1',
                'skor_prioritas' => '90',
            ])
            ->assertRedirect(route('admin.wilayah.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('wilayah', [
            'id' => $wilayah->id,
            'is_3t' => true,
            'skor_prioritas' => 90,
        ]);
    }

    public function test_unchecking_3t_flag_on_update_clears_it(): void
    {
        $wilayah = Wilayah::factory()->tertinggal()->create();

        $this->actingAs($this->admin)
            ->put(route('admin.wilayah.update', $wilayah), [
                'provinsi' => $wilayah->provinsi,
                'kabupaten' => $wilayah->kabupaten,
                'skor_prioritas' => '20',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('wilayah', ['id' => $wilayah->id, 'is_3t' => false, 'skor_prioritas' => 20]);
    }

    public function test_unused_region_can_be_deleted(): void
    {
        $wilayah = Wilayah::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.wilayah.destroy', $wilayah))
            ->assertRedirect(route('admin.wilayah.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($wilayah);
    }

    public function test_region_used_by_a_student_profile_cannot_be_deleted(): void
    {
        $wilayah = Wilayah::factory()->create();
        Mahasiswa::factory()->for($wilayah)->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.wilayah.destroy', $wilayah))
            ->assertSessionHas('error');

        $this->assertModelExists($wilayah);
    }

    public function test_mahasiswa_cannot_manage_regions(): void
    {
        $mahasiswa = User::factory()->create();
        $wilayah = Wilayah::factory()->create();
        $payload = ['provinsi' => 'Aceh', 'kabupaten' => 'Kota Banda Aceh'];

        $this->actingAs($mahasiswa)->get(route('admin.wilayah.index'))->assertForbidden();
        $this->actingAs($mahasiswa)->get(route('admin.wilayah.show', $wilayah))->assertForbidden();
        $this->actingAs($mahasiswa)->post(route('admin.wilayah.store'), $payload)->assertForbidden();
        $this->actingAs($mahasiswa)->put(route('admin.wilayah.update', $wilayah), $payload)->assertForbidden();
        $this->actingAs($mahasiswa)->delete(route('admin.wilayah.destroy', $wilayah))->assertForbidden();

        $this->assertModelExists($wilayah);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.wilayah.index'))->assertRedirect(route('login'));
    }
}
