<?php

namespace App\Models;

use App\Enums\WorkJobStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'scheduled_at', 'status', 'doctor_id', 'technician_id'])]
class WorkJob extends Model
{
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
}
