<?php

namespace Tests\Feature;

use App\Enums\Module;
use App\Models\Appointment;
use App\Models\License;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
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

    public function test_guest_cannot_access_appointments(): void
    {
        $this->get(route('appointments.calendar'))->assertRedirect(route('login'));
    }

    public function test_user_without_license_cannot_access_appointments(): void
    {
        $user = User::factory()->doctor()->create();

        $this->actingAs($user)
            ->get(route('appointments.calendar'))
            ->assertForbidden();
    }

    public function test_user_with_expired_license_cannot_access_appointments(): void
    {
        $user = User::factory()->doctor()->create();
        License::factory()->for($user)->expired()->create(['modules' => [Module::Appointments->value]]);

        $this->actingAs($user)
            ->get(route('appointments.calendar'))
            ->assertForbidden();
    }

    // ----------------------------------------------------------------
    // Calendar
    // ----------------------------------------------------------------

    public function test_doctor_with_license_can_view_calendar(): void
    {
        $doctor = $this->userWithLicense('doctor');

        $this->actingAs($doctor)
            ->get(route('appointments.calendar'))
            ->assertOk()
            ->assertViewIs('appointments.calendar');
    }

    public function test_admin_with_license_can_view_calendar(): void
    {
        $admin = $this->adminWithLicense();

        $this->actingAs($admin)
            ->get(route('appointments.calendar'))
            ->assertOk();
    }

    // ----------------------------------------------------------------
    // Create & Store
    // ----------------------------------------------------------------

    public function test_doctor_can_view_create_form(): void
    {
        $doctor = $this->userWithLicense('doctor');
        Patient::factory()->create();

        $this->actingAs($doctor)
            ->get(route('appointments.create'))
            ->assertOk()
            ->assertViewIs('appointments.create');
    }

    public function test_doctor_can_create_appointment(): void
    {
        $doctor  = $this->userWithLicense('doctor');
        $patient = Patient::factory()->create();

        $this->actingAs($doctor)
            ->post(route('appointments.store'), [
                'patient_id'   => $patient->id,
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'notes'        => 'Test note',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id'  => $doctor->id,
        ]);
    }

    public function test_create_appointment_validates_required_fields(): void
    {
        $doctor = $this->userWithLicense('doctor');

        $this->actingAs($doctor)
            ->post(route('appointments.store'), [])
            ->assertSessionHasErrors(['patient_id', 'scheduled_at']);
    }

    // ----------------------------------------------------------------
    // Show
    // ----------------------------------------------------------------

    public function test_doctor_can_view_own_appointment(): void
    {
        $doctor      = $this->userWithLicense('doctor');
        $patient     = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($doctor)
            ->get(route('appointments.show', $appointment))
            ->assertOk()
            ->assertViewIs('appointments.show');
    }

    public function test_doctor_cannot_view_other_doctors_appointment(): void
    {
        $doctor1 = $this->userWithLicense('doctor');
        $doctor2 = $this->userWithLicense('doctor');
        $patient = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor2, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($doctor1)
            ->get(route('appointments.show', $appointment))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_appointment(): void
    {
        $admin   = $this->adminWithLicense();
        $doctor  = $this->userWithLicense('doctor');
        $patient = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($admin)
            ->get(route('appointments.show', $appointment))
            ->assertOk();
    }

    // ----------------------------------------------------------------
    // Edit & Update
    // ----------------------------------------------------------------

    public function test_doctor_can_edit_own_appointment(): void
    {
        $doctor      = $this->userWithLicense('doctor');
        $patient     = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($doctor)
            ->get(route('appointments.edit', $appointment))
            ->assertOk();
    }

    public function test_doctor_cannot_edit_other_doctors_appointment(): void
    {
        $doctor1 = $this->userWithLicense('doctor');
        $doctor2 = $this->userWithLicense('doctor');
        $patient = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor2, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($doctor1)
            ->put(route('appointments.update', $appointment), [
                'scheduled_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            ])
            ->assertForbidden();
    }

    public function test_doctor_can_update_own_appointment(): void
    {
        $doctor      = $this->userWithLicense('doctor');
        $patient     = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create();
        $newTime     = now()->addDays(5)->format('Y-m-d H:i:s');

        $this->actingAs($doctor)
            ->put(route('appointments.update', $appointment), [
                'scheduled_at' => $newTime,
                'notes'        => 'Rescheduled',
            ])
            ->assertRedirect(route('appointments.show', $appointment));
    }

    // ----------------------------------------------------------------
    // Cancel
    // ----------------------------------------------------------------

    public function test_doctor_can_cancel_own_appointment(): void
    {
        $doctor      = $this->userWithLicense('doctor');
        $patient     = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($doctor)
            ->patch(route('appointments.cancel', $appointment))
            ->assertRedirect(route('appointments.show', $appointment));

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'cancelled']);
    }

    public function test_cannot_cancel_already_cancelled_appointment(): void
    {
        $doctor      = $this->userWithLicense('doctor');
        $patient     = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->cancelled()->create();

        $this->actingAs($doctor)
            ->patch(route('appointments.cancel', $appointment))
            ->assertStatus(422);
    }

    // ----------------------------------------------------------------
    // Complete
    // ----------------------------------------------------------------

    public function test_doctor_can_complete_own_appointment(): void
    {
        $doctor      = $this->userWithLicense('doctor');
        $patient     = Patient::factory()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->for($patient, 'patient')->scheduled()->create();

        $this->actingAs($doctor)
            ->patch(route('appointments.complete', $appointment))
            ->assertRedirect(route('appointments.show', $appointment));

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'completed']);
    }

    // ----------------------------------------------------------------
    // Technician cannot edit
    // ----------------------------------------------------------------

    public function test_technician_cannot_create_appointment(): void
    {
        $technician = $this->userWithLicense('technician');
        $patient    = Patient::factory()->create();

        $this->actingAs($technician)
            ->post(route('appointments.store'), [
                'patient_id'   => $patient->id,
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ])
            ->assertForbidden();
    }
}
