<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    // --- guest access ---

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/dashboard/admin')->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_doctor_dashboard(): void
    {
        $this->get('/dashboard/doctor')->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_technician_dashboard(): void
    {
        $this->get('/dashboard/technician')->assertRedirect(route('login'));
    }

    // --- admin ---

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/dashboard/admin')
            ->assertOk()
            ->assertViewIs('dashboard.admin');
    }

    public function test_admin_cannot_access_doctor_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/dashboard/doctor')
            ->assertForbidden();
    }

    public function test_admin_cannot_access_technician_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/dashboard/technician')
            ->assertForbidden();
    }

    // --- doctor ---

    public function test_doctor_can_access_doctor_dashboard(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get('/dashboard/doctor')
            ->assertOk()
            ->assertViewIs('dashboard.doctor');
    }

    public function test_doctor_cannot_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get('/dashboard/admin')
            ->assertForbidden();
    }

    public function test_doctor_cannot_access_technician_dashboard(): void
    {
        $this->actingAs(User::factory()->doctor()->create())
            ->get('/dashboard/technician')
            ->assertForbidden();
    }

    // --- technician ---

    public function test_technician_can_access_technician_dashboard(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get('/dashboard/technician')
            ->assertOk()
            ->assertViewIs('dashboard.technician');
    }

    public function test_technician_cannot_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get('/dashboard/admin')
            ->assertForbidden();
    }

    public function test_technician_cannot_access_doctor_dashboard(): void
    {
        $this->actingAs(User::factory()->technician()->create())
            ->get('/dashboard/doctor')
            ->assertForbidden();
    }
}
