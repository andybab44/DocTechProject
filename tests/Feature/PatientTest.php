<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Appointment;
use App\Models\License;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function userWithLicense(string $role = 'doctor'): User
    {
        $user = User::factory()->{$role}()->create();
        License::factory()->for($user)->create([
            'is_active'  => true,
            'expires_at' => now()->addYear(),
            'modules'    => [Module::Appointments->value],
        ]);
        return $user;
    }

    private function adminWithLicense(): User
    {
        return $this->userWithLicense('admin');
    }

    // ----------------------------------------------------------------
    // Module gating
    // ----------------------------------------------------------------

    public function test_guest_cannot_access_patients(): void
    {
        $this->get(route('patients.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_license_cannot_access_patients(): void
    {
        $user = User::factory()->doctor()->create();

        $this->actingAs($user)
            ->get(route('patients.index'))
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Index
    // ----------------------------------------------------------------

    public function test_doctor_with_license_can_view_patients(): void
    {
        $doctor = $this->userWithLicense('doctor');
        Patient::factory()->count(3)->create();

        $this->actingAs($doctor)
            ->get(route('patients.index'))
            ->assertOk()
            ->assertViewIs('patients.index');
    }

    // ----------------------------------------------------------------
    // Show
    // ----------------------------------------------------------------

    public function test_doctor_can_view_patient_detail(): void
    {
        $doctor  = $this->userWithLicense('doctor');
        $patient = Patient::factory()->create();

        $this->actingAs($doctor)
            ->get(route('patients.show', $patient))
            ->assertOk()
            ->assertViewIs('patients.show');
    }

    // ----------------------------------------------------------------
    // Create (admin only)
    // ----------------------------------------------------------------

    public function test_admin_can_view_create_patient_form(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->get(route('patients.create'))
            ->assertOk()
            ->assertViewIs('patients.create');
    }

    public function test_non_admin_cannot_view_create_patient_form(): void
    {
        $doctor = $this->userWithLicense('doctor');

        $this->actingAs($doctor)
            ->get(route('patients.create'))
            ->assertForbidden();
    }

    public function test_admin_can_create_patient(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->post(route('patients.store'), [
                'name'  => 'John Patient',
                'email' => 'john@example.com',
                'phone' => '0712345678',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('patients', ['name' => 'John Patient']);
    }

    public function test_non_admin_cannot_create_patient(): void
    {
        $doctor = $this->userWithLicense('doctor');

        $this->actingAs($doctor)
            ->post(route('patients.store'), ['name' => 'Jane Patient'])
            ->assertForbidden();
    }

    public function test_create_patient_validates_name_required(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->post(route('patients.store'), [])
            ->assertSessionHasErrors(['name']);
    }

    // ----------------------------------------------------------------
    // Edit / Update (admin only)
    // ----------------------------------------------------------------

    public function test_admin_can_edit_patient(): void
    {
        $admin   = $this->adminWithLicense();
        $patient = Patient::factory()->create();

        $this->actingAs($admin)
            ->get(route('patients.edit', $patient))
            ->assertOk()
            ->assertViewIs('patients.edit');
    }

    public function test_non_admin_cannot_edit_patient(): void
    {
        $doctor  = $this->userWithLicense('doctor');
        $patient = Patient::factory()->create();

        $this->actingAs($doctor)
            ->get(route('patients.edit', $patient))
            ->assertForbidden();
    }

    public function test_admin_can_update_patient(): void
    {
        $admin   = $this->adminWithLicense();
        $patient = Patient::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->put(route('patients.update', $patient), ['name' => 'New Name'])
            ->assertRedirect(route('patients.show', $patient));

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'name' => 'New Name']);
    }

    // ----------------------------------------------------------------
    // Destroy (admin only)
    // ----------------------------------------------------------------

    public function test_admin_can_delete_patient(): void
    {
        $admin   = $this->adminWithLicense();
        $patient = Patient::factory()->create();

        $this->actingAs($admin)
            ->delete(route('patients.destroy', $patient))
            ->assertRedirect(route('patients.index'));

        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }

    public function test_non_admin_cannot_delete_patient(): void
    {
        $doctor  = $this->userWithLicense('doctor');
        $patient = Patient::factory()->create();

        $this->actingAs($doctor)
            ->delete(route('patients.destroy', $patient))
            ->assertForbidden();
    }

    public function test_deleting_patient_cascades_appointments(): void
    {
        $admin       = $this->adminWithLicense();
        $doctor      = $this->userWithLicense('doctor');
        $patient     = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($admin)
            ->delete(route('patients.destroy', $patient));

        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }
}
