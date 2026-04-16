<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDoctor() || $this->user()?->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'scheduled_at'   => ['required', 'date', 'after_or_equal:today'],
            'technician_id'  => ['required', 'integer', Rule::exists('users', 'id')->where('role', Role::Technician->value)],
            'files'          => ['nullable', 'array', 'max:10'],
            'files.*'        => ['file', 'max:20480', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp,zip'],
        ];
    }
}
