<?php

namespace App\Services;

use App\Models\DentalCase;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CaseService
{
    /**
     * Return paginated cases scoped by role.
     */
    public function paginate(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $query = DentalCase::with(['patient', 'doctor'])
            ->withCount(['workJobs', 'appointments'])
            ->latest();

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        }
        // Admins see all; technicians are not expected to access cases

        return $query->paginate($perPage);
    }

    /**
     * Create a new case.
     *
     * @param  array{patient_id: int, title: string, description: ?string, notes: ?string}  $data
     */
    public function create(User $doctor, array $data): DentalCase
    {
        return DentalCase::create([
            'patient_id'  => $data['patient_id'],
            'doctor_id'   => $doctor->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'notes'       => $data['notes'] ?? null,
        ]);
    }

    /**
     * Update an existing case.
     *
     * @param  array{title?: string, description?: ?string, notes?: ?string}  $data
     */
    public function update(DentalCase $dentalCase, array $data): DentalCase
    {
        $dentalCase->update($data);

        return $dentalCase->fresh();
    }

    /**
     * Delete a case.
     */
    public function delete(DentalCase $dentalCase): void
    {
        $dentalCase->delete();
    }

    /**
     * Return all patients for the case create/edit form.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Patient>
     */
    public function getPatients(): \Illuminate\Database\Eloquent\Collection
    {
        return Patient::orderBy('name')->get();
    }
}
