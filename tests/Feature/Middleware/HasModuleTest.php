<?php

namespace Tests\Feature\Middleware;

use App\Enums\Module;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HasModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/test-module-inventory', fn () => response('ok'))
            ->middleware(['auth', 'module:inventory']);
    }

    public function test_user_without_license_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/test-module-inventory')
            ->assertForbidden();
    }

    public function test_user_with_expired_license_gets_403(): void
    {
        $user = User::factory()->create();
        License::factory()->for($user)->expired()->create([
            'modules' => [Module::Inventory->value],
        ]);

        $this->actingAs($user)
            ->get('/test-module-inventory')
            ->assertForbidden();
    }

    public function test_user_with_inactive_license_gets_403(): void
    {
        $user = User::factory()->create();
        License::factory()->for($user)->inactive()->create([
            'modules' => [Module::Inventory->value],
        ]);

        $this->actingAs($user)
            ->get('/test-module-inventory')
            ->assertForbidden();
    }

    public function test_user_without_module_gets_403(): void
    {
        $user = User::factory()->create();
        License::factory()->for($user)->create(['modules' => []]);

        $this->actingAs($user)
            ->get('/test-module-inventory')
            ->assertForbidden();
    }

    public function test_user_with_valid_license_and_module_can_access(): void
    {
        $user = User::factory()->create();
        License::factory()->for($user)->create([
            'modules' => [Module::Inventory->value],
        ]);

        $this->actingAs($user)
            ->get('/test-module-inventory')
            ->assertOk();
    }

    public function test_perpetual_license_with_module_can_access(): void
    {
        $user = User::factory()->create();
        License::factory()->for($user)->perpetual()->create([
            'modules' => [Module::Inventory->value],
        ]);

        $this->actingAs($user)
            ->get('/test-module-inventory')
            ->assertOk();
    }
}
