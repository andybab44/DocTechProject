<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_job_id'  => ['nullable', 'integer', 'exists:work_jobs,id'],
            'scheduled_at' => ['required', 'date'],
            'notes'        => ['nullable', 'string', 'max:5000'],
        ];
    }
}
