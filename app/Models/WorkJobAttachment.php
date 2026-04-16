<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['work_job_id', 'uploaded_by', 'original_name', 'stored_name', 'mime_type', 'size'])]
class WorkJobAttachment extends Model
{
    use HasFactory;
    public function workJob(): BelongsTo
    {
        return $this->belongsTo(WorkJob::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
