<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'   => ['required', 'integer', 'exists:patients,id'],
            'doctor_id'    => ['nullable', 'integer', 'exists:users,id'],
            'work_job_id'  => ['nullable', 'integer', 'exists:work_jobs,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes'        => ['nullable', 'string', 'max:5000'],
        ];
    }
}
