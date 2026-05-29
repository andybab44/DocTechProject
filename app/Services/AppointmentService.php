<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AppointmentService
{
    /**
     * Return all patients ordered by name.
     *
     * @return LengthAwarePaginator<Patient>
     */
    public function paginatePatients(int $perPage = 20): LengthAwarePaginator
    {
        return Patient::withCount('appointments')->orderBy('name')->paginate($perPage);
    }

    /**
     * Create a new patient.
     *
     * @param  array{name: string, date_of_birth: ?string, email: ?string, phone: ?string, notes: ?string}  $data
     */
    public function createPatient(array $data): Patient
    {
        return Patient::create($data);
    }

    /**
     * Update a patient.
     *
     * @param  array{name: string, date_of_birth: ?string, email: ?string, phone: ?string, notes: ?string}  $data
     */
    public function updatePatient(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient->fresh();
    }

    /**
     * Delete a patient (and cascade-delete their appointments).
     */
    public function deletePatient(Patient $patient): void
    {
        $patient->delete();
    }

    /**
     * Return appointments for the calendar month, scoped by role.
     *
     * @return Collection<int, Appointment>
     */
    public function getForCalendar(User $user, int $year, int $month): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $query = Appointment::with(['patient', 'doctor'])
            ->whereBetween('scheduled_at', [$start, $end]);

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        }

        return $query->orderBy('scheduled_at')->get();
    }

    /**
     * Create a new appointment.
     *
     * @param  array{patient_id: int, scheduled_at: string, work_job_id: ?int, notes: ?string}  $data
     */
    public function create(User $doctor, array $data): Appointment
    {
        return Appointment::create([
            'patient_id'   => $data['patient_id'],
            'doctor_id'    => $doctor->isAdmin() ? ($data['doctor_id'] ?? $doctor->id) : $doctor->id,
            'work_job_id'  => $data['work_job_id'] ?? null,
            'scheduled_at' => $data['scheduled_at'],
            'status'       => AppointmentStatus::Scheduled,
            'notes'        => $data['notes'] ?? null,
        ]);
    }

    /**
     * Reschedule / update appointment details.
     *
     * @param  array{scheduled_at: string, work_job_id: ?int, notes: ?string}  $data
     */
    public function update(Appointment $appointment, array $data): Appointment
    {
        $appointment->update([
            'scheduled_at' => $data['scheduled_at'],
            'work_job_id'  => $data['work_job_id'] ?? null,
            'notes'        => $data['notes'] ?? null,
        ]);

        return $appointment->fresh();
    }

    /**
     * Cancel an appointment.
     */
    public function cancel(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => AppointmentStatus::Cancelled]);

        return $appointment->fresh();
    }

    /**
     * Mark an appointment as completed.
     */
    public function complete(Appointment $appointment): Appointment
    {
        $appointment->update(['status' => AppointmentStatus::Completed]);

        return $appointment->fresh();
    }

    /**
     * Return appointments that need a reminder: scheduled, reminder window matches.
     *
     * @return Collection<int, Appointment>
     */
    public function getDueForReminder(int $hoursAhead = 24): Collection
    {
        $start = now()->addHours($hoursAhead)->startOfHour();
        $end   = $start->copy()->endOfHour();

        return Appointment::with(['patient', 'doctor'])
            ->where('status', AppointmentStatus::Scheduled)
            ->whereBetween('scheduled_at', [$start, $end])
            ->get();
    }
}
