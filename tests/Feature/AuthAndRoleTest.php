<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_registered_user_gets_user_role_and_redirects_to_dashboard(): void
    {
        $this->post(route('register'), [
            'name' => 'Registered User',
            'email' => 'registered@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'registered@example.com',
            'role' => 'user',
        ]);
    }

    public function test_user_cannot_access_knowledge_base_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get(route('knowledge-base.rules'))
            ->assertForbidden();
    }

    public function test_admin_can_access_knowledge_base_management(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('knowledge-base.rules'))
            ->assertOk();
    }

    public function test_login_redirects_to_dashboard(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $this->post(route('login'), [
            'email' => 'login@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }
}
