<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('mahasiswa.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_mahasiswa_cannot_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_mahasiswa_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('mahasiswa.dashboard'))
            ->assertForbidden();
    }

    public function test_deactivated_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $user->forceFill(['is_active' => false])->save();

        $this->get(route('mahasiswa.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => __('auth.inactive')]);

        $this->assertGuest();
    }
}
