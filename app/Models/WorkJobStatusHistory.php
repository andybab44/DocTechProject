<?php

namespace App\Models;

use App\Enums\WorkJobStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['work_job_id', 'from_status', 'to_status', 'changed_by', 'notes'])]
class WorkJobStatusHistory extends Model
{
    public $timestamps = false;

    protected $table = 'work_job_status_history';

    protected function casts(): array
    {
        return [
            'from_status' => WorkJobStatus::class,
            'to_status'   => WorkJobStatus::class,
            'created_at'  => 'datetime',
        ];
    }

    public function workJob(): BelongsTo
    {
        return $this->belongsTo(WorkJob::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
