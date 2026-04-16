<?php

namespace Tests\Feature\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_guest_can_view_login_form(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertViewIs('auth.login');
    }

    public function test_admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        User::factory()->admin()->create(['email' => 'admin@example.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'password'])
            ->assertRedirect('/dashboard/admin');
    }

    public function test_doctor_is_redirected_to_doctor_dashboard_after_login(): void
    {
        User::factory()->doctor()->create(['email' => 'doctor@example.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'doctor@example.com', 'password' => 'password'])
            ->assertRedirect('/dashboard/doctor');
    }

    public function test_technician_is_redirected_to_technician_dashboard_after_login(): void
    {
        User::factory()->technician()->create(['email' => 'tech@example.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'tech@example.com', 'password' => 'password'])
            ->assertRedirect('/dashboard/technician');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'user@example.com', 'password' => bcrypt('password')]);

        $this->post('/login', ['email' => 'user@example.com', 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $this->post('/login', ['email' => 'nobody@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');
    }

    public function test_login_requires_email(): void
    {
        $this->post('/login', ['email' => '', 'password' => 'password'])
            ->assertSessionHasErrors('email');
    }

    public function test_login_requires_password(): void
    {
        $this->post('/login', ['email' => 'user@example.com', 'password' => ''])
            ->assertSessionHasErrors('password');
    }

    public function test_login_requires_valid_email_format(): void
    {
        $this->post('/login', ['email' => 'not-an-email', 'password' => 'password'])
            ->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->doctor()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
