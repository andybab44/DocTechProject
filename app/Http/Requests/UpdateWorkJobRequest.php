<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Enums\WorkJobStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->user();

        // Technicians may only update the status
        if ($user?->isTechnician()) {
            return [
                'status' => ['required', Rule::enum(WorkJobStatus::class)],
            ];
        }

        // Doctors and admins may update all fields
        return [
            'title'         => ['sometimes', 'required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:2000'],
            'scheduled_at'  => ['sometimes', 'required', 'date'],
            'technician_id' => ['sometimes', 'required', 'integer', Rule::exists('users', 'id')->where('role', Role::Technician->value)],
            'status'        => ['sometimes', 'required', Rule::enum(WorkJobStatus::class)],
        ];
    }
}
