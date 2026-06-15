<?php

namespace App\Models;

use Database\Factories\DentalCaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['patient_id', 'doctor_id', 'title', 'description', 'notes'])]
class DentalCase extends Model
{
    /** @use HasFactory<DentalCaseFactory> */
    use HasFactory;

    protected $table = 'dental_cases';

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function workJobs(): HasMany
    {
        return $this->hasMany(WorkJob::class, 'case_id')->orderBy('scheduled_at');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'case_id')->orderBy('scheduled_at');
    }
}
