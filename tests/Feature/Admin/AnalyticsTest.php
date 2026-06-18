<?php

namespace Tests\Feature\Admin;

use App\Enums\Module;
use App\Enums\WorkJobStatus;
use App\Models\License;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function adminWithAnalytics(): User
    {
        $admin = User::factory()->admin()->create();
        License::factory()->for($admin)->create([
            'is_active'  => true,
            'expires_at' => now()->addYear(),
            'modules'    => [Module::Analytics->value],
        ]);
        return $admin;
    }

    private function adminWithoutLicense(): User
    {
        return User::factory()->admin()->create();
    }

    // -------------------------------------------------------------------------
    // Access control
    // -------------------------------------------------------------------------

    public function test_guest_cannot_access_analytics(): void
    {
        $this->get(route('admin.analytics.index'))->assertRedirect(route('login'));
    }

    public function test_admin_without_analytics_license_is_forbidden(): void
    {
        $this->actingAs($this->adminWithoutLicense())
            ->get(route('admin.analytics.index'))
            ->assertForbidden();
    }

    public function test_admin_with_inactive_license_is_forbidden(): void
    {
        $admin = User::factory()->admin()->create();
        License::factory()->for($admin)->inactive()->create([
            'modules' => [Module::Analytics->value],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.analytics.index'))
            ->assertForbidden();
    }

    public function test_admin_with_expired_license_is_forbidden(): void
    {
        $admin = User::factory()->admin()->create();
        License::factory()->for($admin)->expired()->create([
            'modules' => [Module::Analytics->value],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.analytics.index'))
            ->assertForbidden();
    }

    public function test_doctor_with_analytics_license_is_forbidden(): void
    {
        $doctor = User::factory()->doctor()->create();
        License::factory()->for($doctor)->create([
            'modules' => [Module::Analytics->value],
        ]);

        $this->actingAs($doctor)
            ->get(route('admin.analytics.index'))
            ->assertForbidden();
    }

    public function test_technician_with_analytics_license_is_forbidden(): void
    {
        $tech = User::factory()->technician()->create();
        License::factory()->for($tech)->create([
            'modules' => [Module::Analytics->value],
        ]);

        $this->actingAs($tech)
            ->get(route('admin.analytics.index'))
            ->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Happy path
    // -------------------------------------------------------------------------

    public function test_admin_with_analytics_license_can_access_analytics(): void
    {
        $this->actingAs($this->adminWithAnalytics())
            ->get(route('admin.analytics.index'))
            ->assertOk()
            ->assertViewIs('admin.analytics.index');
    }

    public function test_analytics_view_receives_required_data(): void
    {
        $this->actingAs($this->adminWithAnalytics())
            ->get(route('admin.analytics.index'))
            ->assertViewHasAll([
                'workJobStats',
                'workJobsByDoctor',
                'workJobsByTechnician',
                'userStats',
            ]);
    }

    public function test_analytics_reflects_work_job_totals(): void
    {
        $admin   = $this->adminWithAnalytics();
        $doctor  = User::factory()->doctor()->create();
        $tech    = User::factory()->technician()->create();

        WorkJob::factory()->count(3)->create([
            'doctor_id'     => $doctor->id,
            'technician_id' => $tech->id,
            'status'        => WorkJobStatus::InProgress->value,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.analytics.index'))
            ->assertOk();

        $stats = $response->viewData('workJobStats');
        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(3, $stats['by_status']->get(WorkJobStatus::InProgress->value)?->total);
    }

    public function test_analytics_reflects_user_counts(): void
    {
        $admin = $this->adminWithAnalytics();
        User::factory()->doctor()->count(2)->create();
        User::factory()->technician()->count(3)->create();

        $response = $this->actingAs($admin)
            ->get(route('admin.analytics.index'))
            ->assertOk();

        $stats = $response->viewData('userStats');
        // +1 for the admin itself
        $this->assertEquals(6, $stats['total']);
    }

    public function test_analytics_without_optional_modules_passes_null(): void
    {
        // License with analytics only — no inventory or appointments
        $response = $this->actingAs($this->adminWithAnalytics())
            ->get(route('admin.analytics.index'))
            ->assertOk();

        $this->assertNull($response->viewData('appointmentStats'));
        $this->assertNull($response->viewData('inventoryStats'));
        $this->assertNull($response->viewData('patientCount'));
    }

    public function test_analytics_with_all_modules_passes_data(): void
    {
        $admin = User::factory()->admin()->create();
        License::factory()->for($admin)->create([
            'is_active'  => true,
            'expires_at' => now()->addYear(),
            'modules'    => [
                Module::Analytics->value,
                Module::Inventory->value,
                Module::Appointments->value,
            ],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.analytics.index'))
            ->assertOk();

        $this->assertNotNull($response->viewData('appointmentStats'));
        $this->assertNotNull($response->viewData('inventoryStats'));
        $this->assertNotNull($response->viewData('patientCount'));
    }
}
