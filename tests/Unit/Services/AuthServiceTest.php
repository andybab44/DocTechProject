<?php

namespace Tests\Unit\Services;

use App\Enums\Role;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AuthService();
    }

    public function test_login_returns_the_authenticated_user(): void
    {
        $user = User::factory()->doctor()->create([
            'email'    => 'doctor@example.com',
            'password' => bcrypt('secret'),
        ]);

        $result = $this->service->login(['email' => 'doctor@example.com', 'password' => 'secret']);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
    }

    public function test_login_throws_validation_exception_for_wrong_password(): void
    {
        User::factory()->create(['email' => 'user@example.com', 'password' => bcrypt('secret')]);

        $this->expectException(ValidationException::class);

        $this->service->login(['email' => 'user@example.com', 'password' => 'wrong']);
    }

    public function test_login_throws_validation_exception_for_unknown_email(): void
    {
        $this->expectException(ValidationException::class);

        $this->service->login(['email' => 'nobody@example.com', 'password' => 'password']);
    }

    public function test_redirect_path_for_admin(): void
    {
        $this->assertEquals('/dashboard/admin', $this->service->redirectPathForRole(Role::Admin));
    }

    public function test_redirect_path_for_doctor(): void
    {
        $this->assertEquals('/dashboard/doctor', $this->service->redirectPathForRole(Role::Doctor));
    }

    public function test_redirect_path_for_technician(): void
    {
        $this->assertEquals('/dashboard/technician', $this->service->redirectPathForRole(Role::Technician));
    }
}
