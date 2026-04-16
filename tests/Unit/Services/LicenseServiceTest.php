<?php

namespace Tests\Unit\Services;

use App\Enums\Module;
use App\Models\License;
use App\Models\User;
use App\Services\LicenseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseServiceTest extends TestCase
{
    use RefreshDatabase;

    private LicenseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LicenseService();
    }

    public function test_create_license(): void
    {
        $user    = User::factory()->create();
        $license = $this->service->create($user, [
            'expires_at' => now()->addYear(),
            'modules'    => [Module::Inventory->value],
        ]);

        $this->assertInstanceOf(License::class, $license);
        $this->assertEquals($user->id, $license->user_id);
        $this->assertTrue($license->is_active);
        $this->assertTrue($license->hasModule(Module::Inventory));
    }

    public function test_create_license_without_expiry(): void
    {
        $user    = User::factory()->create();
        $license = $this->service->create($user, []);

        $this->assertNull($license->expires_at);
        $this->assertTrue($license->isValid());
    }

    public function test_update_license_modules(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->create(['modules' => []]);

        $this->service->update($license, [
            'modules' => [Module::Appointments->value, Module::Reviews->value],
        ]);

        $license->refresh();
        $this->assertTrue($license->hasModule(Module::Appointments));
        $this->assertTrue($license->hasModule(Module::Reviews));
        $this->assertFalse($license->hasModule(Module::Inventory));
    }

    public function test_update_clears_modules_when_empty(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->create([
            'modules' => [Module::Inventory->value],
        ]);

        $this->service->update($license, []);

        $license->refresh();
        $this->assertFalse($license->hasModule(Module::Inventory));
    }

    public function test_toggle_active_deactivates(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->create(['is_active' => true]);

        $this->service->toggleActive($license);

        $this->assertFalse($license->fresh()->is_active);
    }

    public function test_toggle_active_reactivates(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->create(['is_active' => false]);

        $this->service->toggleActive($license);

        $this->assertTrue($license->fresh()->is_active);
    }

    public function test_is_valid_with_no_expiry(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->perpetual()->create();

        $this->assertTrue($license->isValid());
    }

    public function test_is_valid_with_future_expiry(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->create(['expires_at' => now()->addYear()]);

        $this->assertTrue($license->isValid());
    }

    public function test_is_not_valid_when_expired(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->expired()->create();

        $this->assertFalse($license->isValid());
    }

    public function test_is_not_valid_when_inactive(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->inactive()->create();

        $this->assertFalse($license->isValid());
    }

    public function test_has_module_returns_true(): void
    {
        $user    = User::factory()->create();
        $license = License::factory()->for($user)->create([
            'modules' => [Module::Inventory->value],
        ]);

        $this->assertTrue($license->hasModule(Module::Inventory));
        $this->assertFalse($license->hasModule(Module::Appointments));
    }
}
