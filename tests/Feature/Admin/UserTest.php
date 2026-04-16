<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // --- index ---

    public function test_guest_cannot_access_user_list(): void
    {
        $this->get('/admin/users')->assertRedirect(route('login'));
    }

    public function test_doctor_cannot_access_user_list(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_technician_cannot_access_user_list(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_admin_can_view_user_list(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertViewIs('admin.users.index');
    }

    // --- create ---

    public function test_guest_cannot_access_create_user_form(): void
    {
        $this->get('/admin/users/create')->assertRedirect(route('login'));
    }

    public function test_doctor_cannot_access_create_user_form(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get('/admin/users/create')
            ->assertForbidden();
    }

    public function test_technician_cannot_access_create_user_form(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get('/admin/users/create')
            ->assertForbidden();
    }

    public function test_admin_can_view_create_user_form(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/users/create')
            ->assertOk()
            ->assertViewIs('admin.users.create');
    }

    // --- store ---

    public function test_guest_cannot_create_user(): void
    {
        $this->post('/admin/users', $this->validUserPayload())
            ->assertRedirect(route('login'));
    }

    public function test_doctor_cannot_create_user(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->post('/admin/users', $this->validUserPayload())
            ->assertForbidden();
    }

    public function test_technician_cannot_create_user(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->post('/admin/users', $this->validUserPayload())
            ->assertForbidden();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/users', $this->validUserPayload())
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'role'  => Role::Doctor->value,
        ]);
    }

    public function test_store_requires_name(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', array_merge($this->validUserPayload(), ['name' => '']))
            ->assertSessionHasErrors('name');
    }

    public function test_store_requires_valid_email(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', array_merge($this->validUserPayload(), ['email' => 'not-an-email']))
            ->assertSessionHasErrors('email');
    }

    public function test_store_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'newuser@example.com']);

        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', $this->validUserPayload())
            ->assertSessionHasErrors('email');
    }

    public function test_store_requires_confirmed_password(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', array_merge($this->validUserPayload(), ['password_confirmation' => 'different']))
            ->assertSessionHasErrors('password');
    }

    public function test_store_requires_valid_role(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', array_merge($this->validUserPayload(), ['role' => 'superuser']))
            ->assertSessionHasErrors('role');
    }

    public function test_store_requires_password_minimum_length(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', array_merge($this->validUserPayload(), [
                'password'              => 'short',
                'password_confirmation' => 'short',
            ]))
            ->assertSessionHasErrors('password');
    }

    // --- helpers ---

    private function validUserPayload(): array
    {
        return [
            'name'                  => 'New User',
            'email'                 => 'newuser@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => Role::Doctor->value,
        ];
    }
}
