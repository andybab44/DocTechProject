<?php

namespace Tests\Feature\Admin;

use App\Enums\Module;
use App\Models\License;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_license_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.licenses.index'))
            ->assertOk();
    }

    public function test_doctor_cannot_view_license_index(): void
    {
        $doctor = User::factory()->doctor()->create();

        $this->actingAs($doctor)
            ->get(route('admin.licenses.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_license_index(): void
    {
        $this->get(route('admin.licenses.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_create_license(): void
    {
        $admin  = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create();

        $this->actingAs($admin)
            ->post(route('admin.licenses.store'), [
                'user_id'    => $doctor->id,
                'expires_at' => now()->addYear()->format('Y-m-d'),
                'modules'    => [Module::Inventory->value],
            ])
            ->assertRedirect(route('admin.licenses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('licenses', [
            'user_id'   => $doctor->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_create_duplicate_license(): void
    {
        $admin  = User::factory()->admin()->create();
        $doctor = User::factory()->doctor()->create();
        License::factory()->for($doctor)->create();

        $this->actingAs($admin)
            ->post(route('admin.licenses.store'), [
                'user_id' => $doctor->id,
            ])
            ->assertSessionHasErrors('user_id');
    }

    public function test_admin_can_update_license(): void
    {
        $admin   = User::factory()->admin()->create();
        $doctor  = User::factory()->doctor()->create();
        $license = License::factory()->for($doctor)->create(['modules' => []]);

        $this->actingAs($admin)
            ->put(route('admin.licenses.update', $license), [
                'expires_at' => now()->addYears(2)->format('Y-m-d'),
                'modules'    => [Module::Appointments->value, Module::Reviews->value],
            ])
            ->assertRedirect(route('admin.licenses.index'))
            ->assertSessionHas('success');

        $license->refresh();
        $this->assertTrue($license->hasModule(Module::Appointments));
        $this->assertTrue($license->hasModule(Module::Reviews));
        $this->assertFalse($license->hasModule(Module::Inventory));
    }

    public function test_admin_can_toggle_license_active(): void
    {
        $admin   = User::factory()->admin()->create();
        $doctor  = User::factory()->doctor()->create();
        $license = License::factory()->for($doctor)->create(['is_active' => true]);

        $this->actingAs($admin)
            ->post(route('admin.licenses.toggle-active', $license))
            ->assertRedirect(route('admin.licenses.index'));

        $this->assertFalse($license->fresh()->is_active);
    }

    public function test_non_admin_cannot_create_license(): void
    {
        $doctor  = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();

        $this->actingAs($doctor)
            ->post(route('admin.licenses.store'), [
                'user_id' => $doctor2->id,
            ])
            ->assertForbidden();
    }

    public function test_admin_can_view_create_form(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.licenses.create'))
            ->assertOk();
    }

    public function test_admin_can_view_edit_form(): void
    {
        $admin   = User::factory()->admin()->create();
        $doctor  = User::factory()->doctor()->create();
        $license = License::factory()->for($doctor)->create();

        $this->actingAs($admin)
            ->get(route('admin.licenses.edit', $license))
            ->assertOk()
            ->assertSee($doctor->name);
    }
}
