<?php

namespace Tests\Feature\Console;

use App\Models\License;
use App\Models\User;
use App\Notifications\LicenseExpiringNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotifyExpiringLicensesTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifies_users_whose_license_expires_in_seven_days(): void
    {
        Notification::fake();

        $user = User::factory()->doctor()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addDays(7)->startOfDay(),
        ]);

        $this->artisan('licenses:notify-expiring')->assertSuccessful();

        Notification::assertSentTo($user, LicenseExpiringNotification::class);
    }

    public function test_does_not_notify_users_with_non_expiring_license(): void
    {
        Notification::fake();

        $user = User::factory()->doctor()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addDays(30)->startOfDay(),
        ]);

        $this->artisan('licenses:notify-expiring')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_does_not_notify_users_with_inactive_license(): void
    {
        Notification::fake();

        $user = User::factory()->doctor()->create();
        License::factory()->for($user)->create([
            'is_active'  => false,
            'expires_at' => now()->addDays(7)->startOfDay(),
        ]);

        $this->artisan('licenses:notify-expiring')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_does_not_notify_users_with_perpetual_license(): void
    {
        Notification::fake();

        $user = User::factory()->doctor()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => null,
        ]);

        $this->artisan('licenses:notify-expiring')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_custom_days_option_is_respected(): void
    {
        Notification::fake();

        $user = User::factory()->doctor()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addDays(14)->startOfDay(),
        ]);

        $this->artisan('licenses:notify-expiring', ['--days' => 14])->assertSuccessful();

        Notification::assertSentTo($user, LicenseExpiringNotification::class);
    }

    public function test_outputs_notified_count(): void
    {
        $user = User::factory()->doctor()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addDays(7)->startOfDay(),
        ]);

        $this->artisan('licenses:notify-expiring')
            ->expectsOutputToContain('Notified 1 user(s)')
            ->assertSuccessful();
    }
}
