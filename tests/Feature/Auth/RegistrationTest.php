<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get(route('register'))->assertOk();
    }

    public function test_new_user_is_registered_as_mahasiswa(): void
    {
        $response = $this->post(route('register'), $this->validPayload());

        $user = User::firstWhere('email', 'nadia@email.com');

        $this->assertAuthenticatedAs($user);
        $this->assertSame(UserRole::Mahasiswa, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertSame('081234567890', $user->no_telepon);
        $response->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_role_cannot_be_escalated_through_registration_input(): void
    {
        $this->post(route('register'), $this->validPayload(['role' => 'admin', 'is_active' => false]));

        $user = User::firstWhere('email', 'nadia@email.com');

        $this->assertSame(UserRole::Mahasiswa, $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_registration_requires_accepting_terms(): void
    {
        $this->post(route('register'), $this->validPayload(['terms' => null]))
            ->assertSessionHasErrors('terms');

        $this->assertGuest();
    }

    public function test_nik_and_email_must_be_unique(): void
    {
        $existing = User::factory()->create();

        $this->post(route('register'), $this->validPayload([
            'nik' => $existing->nik,
            'email' => strtoupper($existing->email),
        ]))->assertSessionHasErrors(['nik', 'email']);
    }

    public function test_nik_and_phone_number_must_be_valid(): void
    {
        $this->post(route('register'), $this->validPayload([
            'nik' => '12345',
            'no_telepon' => '12345',
        ]))->assertSessionHasErrors(['nik', 'no_telepon']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'nama' => 'Nadia Putri',
            'nik' => '3201234567890001',
            'email' => 'nadia@email.com',
            'no_telepon' => '0812-3456-7890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'terms' => '1',
            ...$overrides,
        ];
    }
}
