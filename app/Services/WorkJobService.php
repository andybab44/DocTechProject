<?php

namespace App\Services;

use App\Enums\Role;
use App\Enums\WorkJobStatus;
use App\Models\User;
use App\Models\WorkJob;
use App\Models\WorkJobAttachment;
use App\Models\WorkJobStatusHistory;
use App\Notifications\WorkJobStatusChangedNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class WorkJobService
{
    /**
     * Allowed status transitions keyed by role.
     *
     * @return array<string, array<string, string[]>>
     */
    public static function allowedTransitions(): array
    {
        return [
            Role::Technician->value => [
                WorkJobStatus::AwaitingAcceptance->value => [WorkJobStatus::InProgress->value],
                WorkJobStatus::InProgress->value         => [WorkJobStatus::InReview->value],
                WorkJobStatus::NeedsRevision->value      => [WorkJobStatus::InProgress->value],
            ],
            Role::Doctor->value => [
                WorkJobStatus::InReview->value         => [WorkJobStatus::ReadyForDelivery->value, WorkJobStatus::NeedsRevision->value],
                WorkJobStatus::ReadyForDelivery->value => [WorkJobStatus::Delivered->value],
            ],
            Role::Admin->value => [], // Admin may perform any transition (checked separately)
        ];
    }

    /**
     * Determine whether a user may transition a work job to the given status.
     */
    public function canTransition(User $user, WorkJob $workJob, WorkJobStatus $newStatus): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $transitions = self::allowedTransitions()[$user->role->value] ?? [];
        $allowed     = $transitions[$workJob->status->value] ?? [];

        return in_array($newStatus->value, $allowed, true);
    }

    /**
     * Return the list of WorkJobStatus values the user may transition to from the current status.
     * Always includes the current status so the form can show it as the selected option.
     *
     * @return WorkJobStatus[]
     */
    public function allowedTransitionsForUser(User $user, WorkJob $workJob): array
    {
        if ($user->isAdmin()) {
            return WorkJobStatus::cases();
        }

        $transitions = self::allowedTransitions()[$user->role->value] ?? [];
        $nextValues  = $transitions[$workJob->status->value] ?? [];

        $statuses = [$workJob->status];

        foreach ($nextValues as $value) {
            $statuses[] = WorkJobStatus::from($value);
        }

        return $statuses;
    }

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
            'status'         => WorkJobStatus::AwaitingAcceptance,
        ]);
    }

    /**
     * Update the status of a work job and record audit history.
     */
    public function updateStatus(WorkJob $workJob, WorkJobStatus $newStatus, User $changedBy, ?string $notes = null): WorkJob
    {
        $fromStatus = $workJob->status;

        $workJob->update(['status' => $newStatus]);

        WorkJobStatusHistory::create([
            'work_job_id' => $workJob->id,
            'from_status' => $fromStatus->value,
            'to_status'   => $newStatus->value,
            'changed_by'  => $changedBy->id,
            'notes'       => $notes,
        ]);

        $this->notifyStatusChange($workJob, $newStatus, $changedBy);

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

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function notifyStatusChange(WorkJob $workJob, WorkJobStatus $newStatus, User $changedBy): void
    {
        // Doctor notifications (technician triggers these)
        $doctorNotifiedStatuses = [
            WorkJobStatus::InProgress->value,
            WorkJobStatus::InReview->value,
            WorkJobStatus::ReadyForDelivery->value,
        ];

        // Technician notifications (doctor triggers these)
        $technicianNotifiedStatuses = [
            WorkJobStatus::NeedsRevision->value,
            WorkJobStatus::Delivered->value,
        ];

        $workJob->loadMissing(['doctor', 'technician']);

        if (in_array($newStatus->value, $doctorNotifiedStatuses, true) && $workJob->doctor_id !== $changedBy->id) {
            $workJob->doctor->notify(new WorkJobStatusChangedNotification($workJob, $newStatus, $changedBy));
        }

        if (in_array($newStatus->value, $technicianNotifiedStatuses, true) && $workJob->technician_id !== $changedBy->id) {
            $workJob->technician->notify(new WorkJobStatusChangedNotification($workJob, $newStatus, $changedBy));
        }
    }
}
