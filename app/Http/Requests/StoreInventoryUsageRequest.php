<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'quantity_used' => ['required', 'integer', 'min:1'],
            'work_job_id'   => ['nullable', 'integer', 'exists:work_jobs,id'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ];
    }
}
