<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\WorkJobStatus;
use App\Models\User;
use App\Models\WorkJob;
use App\Models\WorkJobAttachment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class WorkJobService
{
    /**
     * Return all work jobs for a given month, scoped by user role.
     */
    public function getForCalendar(User $user, int $year, int $month): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $query = WorkJob::with(['doctor', 'technician'])
            ->whereBetween('scheduled_at', [$start, $end]);

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        } elseif ($user->isTechnician()) {
            $query->where('technician_id', $user->id);
        }
        // Admin sees everything

        return $query->orderBy('scheduled_at')->get();
    }

    /**
     * Return all technician users available for assignment.
     *
     * @return Collection<int, User>
     */
    public function getTechnicians(): Collection
    {
        return User::where('role', Role::Technician->value)->orderBy('name')->get();
    }

    /**
     * Create a new work job requested by a doctor.
     *
     * @param  array{title: string, description: ?string, scheduled_at: string, technician_id: int}  $data
     */
    public function create(User $doctor, array $data): WorkJob
    {
        return WorkJob::create([
            'title'          => $data['title'],
            'description'    => $data['description'] ?? null,
            'scheduled_at'   => Carbon::parse($data['scheduled_at']),
            'technician_id'  => $data['technician_id'],
            'doctor_id'      => $doctor->id,
            'status'         => WorkJobStatus::Pending,
        ]);
    }

    /**
     * Update the status of a work job (used by technicians).
     */
    public function updateStatus(WorkJob $workJob, WorkJobStatus $status): WorkJob
    {
        $workJob->update(['status' => $status]);

        return $workJob->fresh();
    }

    /**
     * Update a work job (used by the owning doctor or admin).
     *
     * @param  array{title?: string, description?: ?string, scheduled_at?: string, technician_id?: int}  $data
     */
    public function update(WorkJob $workJob, array $data): WorkJob
    {
        if (isset($data['scheduled_at'])) {
            $data['scheduled_at'] = Carbon::parse($data['scheduled_at']);
        }

        $workJob->update($data);

        return $workJob->fresh();
    }

    /**
     * Delete a work job.
     */
    public function delete(WorkJob $workJob): void
    {
        $workJob->delete();
    }

    /**
     * Store an uploaded file as an attachment on a work job.
     */
    public function addAttachment(WorkJob $workJob, User $uploader, UploadedFile $file): WorkJobAttachment
    {
        $stored = $file->store("work-jobs/{$workJob->id}/attachments", 'local');

        return $workJob->attachments()->create([
            'uploaded_by'   => $uploader->id,
            'original_name' => $file->getClientOriginalName(),
            'stored_name'   => $stored,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
        ]);
    }

    /**
     * Delete an attachment from storage and the database.
     */
    public function deleteAttachment(WorkJobAttachment $attachment): void
    {
        Storage::disk('local')->delete($attachment->stored_name);
        $attachment->delete();
    }
}
