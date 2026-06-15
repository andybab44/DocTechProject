<?php

namespace App\Models;

use App\Enums\WorkJobStatus;
use Database\Factories\WorkJobFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'scheduled_at', 'status', 'doctor_id', 'technician_id', 'case_id'])]
class WorkJob extends Model
{
    /** @use HasFactory<WorkJobFactory> */
    use HasFactory;
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'status'        => WorkJobStatus::class,
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(WorkJobAttachment::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(WorkJobStatusHistory::class)->orderBy('created_at');
    }

    public function dentalCase(): BelongsTo
    {
        return $this->belongsTo(DentalCase::class, 'case_id');
    }
}
