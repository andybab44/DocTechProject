<?php

namespace Tests\Unit\Services;

use App\Enums\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    public function test_paginate_returns_paginated_users(): void
    {
        User::factory()->count(5)->create();

        $result = $this->service->paginate(3);

        $this->assertEquals(3, $result->perPage());
        $this->assertEquals(5, $result->total());
    }

    public function test_paginate_orders_users_by_name(): void
    {
        User::factory()->create(['name' => 'Zebra User']);
        User::factory()->create(['name' => 'Apple User']);

        $result = $this->service->paginate();

        $this->assertEquals('Apple User', $result->first()->name);
    }

    public function test_create_persists_user_with_correct_role(): void
    {
        $user = $this->service->create([
            'name'     => 'Test Doctor',
            'email'    => 'test@example.com',
            'password' => 'Password123!',
            'role'     => Role::Doctor->value,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(Role::Doctor, $user->role);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_create_hashes_the_password(): void
    {
        $user = $this->service->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => 'Password123!',
            'role'     => Role::Technician->value,
        ]);

        $this->assertTrue(Hash::check('Password123!', $user->password));
        $this->assertNotEquals('Password123!', $user->password);
    }

    public function test_update_changes_user_fields(): void
    {
        $user = User::factory()->doctor()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

        $updated = $this->service->update($user, [
            'name'  => 'New Name',
            'email' => 'new@example.com',
            'role'  => Role::Technician->value,
        ]);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals('new@example.com', $updated->email);
        $this->assertEquals(Role::Technician, $updated->role);
    }

    public function test_update_changes_password_when_provided(): void
    {
        $user    = User::factory()->create();
        $oldHash = $user->password;

        $updated = $this->service->update($user, [
            'name'     => $user->name,
            'email'    => $user->email,
            'role'     => $user->role->value,
            'password' => 'NewPassword1!',
        ]);

        $this->assertNotEquals($oldHash, $updated->password);
        $this->assertTrue(Hash::check('NewPassword1!', $updated->password));
    }

    public function test_update_does_not_change_password_when_empty(): void
    {
        $user    = User::factory()->create();
        $oldHash = $user->password;

        $this->service->update($user, [
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role->value,
        ]);

        $this->assertEquals($oldHash, $user->fresh()->password);
    }

    public function test_set_active_deactivates_user(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $result = $this->service->setActive($user, false);

        $this->assertFalse($result->is_active);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);
    }

    public function test_set_active_activates_user(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $result = $this->service->setActive($user, true);

        $this->assertTrue($result->is_active);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => true]);
    }
}
