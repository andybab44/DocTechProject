<?php

namespace Tests\Unit\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private AppointmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AppointmentService();
    }

    // ----------------------------------------------------------------
    // Patient CRUD
    // ----------------------------------------------------------------

    public function test_create_patient_persists(): void
    {
        $patient = $this->service->createPatient([
            'name'          => 'Jane Doe',
            'date_of_birth' => '1990-01-15',
            'email'         => 'jane@example.com',
            'phone'         => null,
            'notes'         => null,
        ]);

        $this->assertDatabaseHas('patients', ['name' => 'Jane Doe', 'email' => 'jane@example.com']);
        $this->assertInstanceOf(Patient::class, $patient);
    }

    public function test_update_patient_changes_fields(): void
    {
        $patient = Patient::factory()->create(['name' => 'Old Name']);

        $this->service->updatePatient($patient, [
            'name'          => 'New Name',
            'date_of_birth' => null,
            'email'         => null,
            'phone'         => null,
            'notes'         => 'Some notes',
        ]);

        $this->assertDatabaseHas('patients', ['id' => $patient->id, 'name' => 'New Name', 'notes' => 'Some notes']);
    }

    public function test_delete_patient_removes_record(): void
    {
        $patient = Patient::factory()->create();

        $this->service->deletePatient($patient);

        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }

    // ----------------------------------------------------------------
    // Appointment CRUD
    // ----------------------------------------------------------------

    public function test_create_appointment_persists(): void
    {
        $doctor  = User::factory()->doctor()->create();
        $patient = Patient::factory()->create();

        $appointment = $this->service->create($doctor, [
            'patient_id'   => $patient->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'notes'        => null,
        ]);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id'  => $doctor->id,
            'status'     => AppointmentStatus::Scheduled->value,
        ]);
        $this->assertInstanceOf(Appointment::class, $appointment);
    }

    public function test_update_appointment_reschedules(): void
    {
        $doctor      = User::factory()->doctor()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->scheduled()->create();
        $newTime     = now()->addDays(7)->toDateTimeString();

        $this->service->update($appointment, ['scheduled_at' => $newTime, 'notes' => 'Updated notes']);

        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'notes' => 'Updated notes']);
    }

    public function test_cancel_sets_status_to_cancelled(): void
    {
        $doctor      = User::factory()->doctor()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->scheduled()->create();

        $this->service->cancel($appointment);

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointment->id,
            'status' => AppointmentStatus::Cancelled->value,
        ]);
    }

    public function test_complete_sets_status_to_completed(): void
    {
        $doctor      = User::factory()->doctor()->create();
        $appointment = Appointment::factory()->for($doctor, 'doctor')->scheduled()->create();

        $this->service->complete($appointment);

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointment->id,
            'status' => AppointmentStatus::Completed->value,
        ]);
    }

    // ----------------------------------------------------------------
    // Calendar scoping
    // ----------------------------------------------------------------

    public function test_admin_sees_all_appointments_in_calendar(): void
    {
        $admin   = User::factory()->admin()->create();
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();

        Appointment::factory()->for($doctor1, 'doctor')->scheduled()->create([
            'scheduled_at' => now()->startOfMonth()->addDays(1),
        ]);
        Appointment::factory()->for($doctor2, 'doctor')->scheduled()->create([
            'scheduled_at' => now()->startOfMonth()->addDays(2),
        ]);

        $appointments = $this->service->getForCalendar($admin, now()->year, now()->month);

        $this->assertCount(2, $appointments);
    }

    public function test_doctor_sees_only_own_appointments_in_calendar(): void
    {
        $doctor1 = User::factory()->doctor()->create();
        $doctor2 = User::factory()->doctor()->create();

        Appointment::factory()->for($doctor1, 'doctor')->scheduled()->create([
            'scheduled_at' => now()->startOfMonth()->addDays(1),
        ]);
        Appointment::factory()->for($doctor2, 'doctor')->scheduled()->create([
            'scheduled_at' => now()->startOfMonth()->addDays(2),
        ]);

        $appointments = $this->service->getForCalendar($doctor1, now()->year, now()->month);

        $this->assertCount(1, $appointments);
        $this->assertEquals($doctor1->id, $appointments->first()->doctor_id);
    }

    // ----------------------------------------------------------------
    // Reminder
    // ----------------------------------------------------------------

    public function test_get_due_for_reminder_returns_scheduled_in_window(): void
    {
        $doctor      = User::factory()->doctor()->create();
        $inWindow    = Appointment::factory()->for($doctor, 'doctor')->scheduled()->create([
            'scheduled_at' => now()->addHours(24)->startOfHour(),
        ]);
        $outOfWindow = Appointment::factory()->for($doctor, 'doctor')->scheduled()->create([
            'scheduled_at' => now()->addHours(48),
        ]);

        $due = $this->service->getDueForReminder(24);

        $this->assertTrue($due->contains('id', $inWindow->id));
        $this->assertFalse($due->contains('id', $outOfWindow->id));
    }

    public function test_get_due_for_reminder_excludes_cancelled(): void
    {
        $doctor = User::factory()->doctor()->create();
        Appointment::factory()->for($doctor, 'doctor')->cancelled()->create([
            'scheduled_at' => now()->addHours(24)->startOfHour(),
        ]);

        $due = $this->service->getDueForReminder(24);

        $this->assertEmpty($due);
    }
}
