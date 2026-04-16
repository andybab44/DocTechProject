<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEditTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Edit form
    // -------------------------------------------------------------------------

    public function test_guest_cannot_access_edit_form(): void
    {
        $user = User::factory()->doctor()->create();

        $this->get(route('admin.users.edit', $user))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_edit_form(): void
    {
        $actor  = User::factory()->doctor()->create();
        $target = User::factory()->technician()->create();

        $this->actingAs($actor)
            ->get(route('admin.users.edit', $target))
            ->assertForbidden();
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->doctor()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $target))
            ->assertOk()
            ->assertViewIs('admin.users.edit')
            ->assertSee($target->name);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    public function test_admin_can_update_user_details(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->doctor()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name'  => 'Updated Name',
                'email' => 'updated@example.com',
                'role'  => 'technician',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id'    => $target->id,
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
            'role'  => 'technician',
        ]);
    }

    public function test_admin_can_change_user_password(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();
        $oldHash = $target->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name'                  => $target->name,
                'email'                 => $target->email,
                'role'                  => $target->role->value,
                'password'              => 'NewPassword1!',
                'password_confirmation' => 'NewPassword1!',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertNotEquals($oldHash, $target->fresh()->password);
    }

    public function test_update_skips_password_change_when_blank(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();
        $oldHash = $target->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name'  => $target->name,
                'email' => $target->email,
                'role'  => $target->role->value,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertEquals($oldHash, $target->fresh()->password);
    }

    public function test_update_requires_name(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name'  => '',
                'email' => $target->email,
                'role'  => $target->role->value,
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_update_requires_unique_email_ignoring_self(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->create();
        $other  = User::factory()->create();

        // Updating with own email should pass
        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name'  => $target->name,
                'email' => $target->email,
                'role'  => $target->role->value,
            ])
            ->assertRedirect(route('admin.users.index'));

        // Updating with another user's email should fail
        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name'  => $target->name,
                'email' => $other->email,
                'role'  => $target->role->value,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_non_admin_cannot_update_user(): void
    {
        $actor  = User::factory()->doctor()->create();
        $target = User::factory()->technician()->create();

        $this->actingAs($actor)
            ->put(route('admin.users.update', $target), [
                'name'  => 'Hacked',
                'email' => 'hacked@example.com',
                'role'  => 'admin',
            ])
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Toggle active
    // -------------------------------------------------------------------------

    public function test_admin_can_deactivate_a_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->doctor()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertFalse($target->fresh()->is_active);
    }

    public function test_admin_can_reactivate_a_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->doctor()->create(['is_active' => false]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $target))
            ->assertRedirect(route('admin.users.index'));

        $this->assertTrue($target->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $admin))
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_non_admin_cannot_toggle_active(): void
    {
        $actor  = User::factory()->doctor()->create();
        $target = User::factory()->technician()->create();

        $this->actingAs($actor)
            ->patch(route('admin.users.toggle-active', $target))
            ->assertForbidden();
    }

    public function test_guest_cannot_toggle_active(): void
    {
        $target = User::factory()->doctor()->create();

        $this->patch(route('admin.users.toggle-active', $target))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // Deactivated user cannot log in
    // -------------------------------------------------------------------------

    public function test_deactivated_user_cannot_log_in(): void
    {
        User::factory()->doctor()->create([
            'email'     => 'inactive@example.com',
            'password'  => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->post('/login', ['email' => 'inactive@example.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
