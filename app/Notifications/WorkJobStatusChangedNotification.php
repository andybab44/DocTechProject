<?php

namespace App\Notifications;

use App\Enums\WorkJobStatus;
use App\Models\User;
use App\Models\WorkJob;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkJobStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly WorkJob $workJob,
        private readonly WorkJobStatus $newStatus,
        private readonly User $changedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'work_job_id'      => $this->workJob->id,
            'work_job_title'   => $this->workJob->title,
            'new_status'       => $this->newStatus->value,
            'new_status_label' => $this->newStatus->label(),
            'changed_by_name'  => $this->changedBy->name,
        ];
    }
}
